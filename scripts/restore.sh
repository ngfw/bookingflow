#!/bin/bash

# BookingFlow - Restore Script
# This script restores the system from backup files

set -e

# Configuration
BACKUP_DIR="/var/backups/beauty-salon"
DB_NAME="bookingflow"
DB_USER="bookingflow_user"
DB_PASSWORD="bookingflow_password"
DB_HOST="localhost"
DB_PORT="3306"
APP_DIR="/var/www/beauty-salon"

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$BACKUP_DIR/restore.log"
}

# Function to show usage
usage() {
    echo "Usage: $0 [OPTIONS]"
    echo "Options:"
    echo "  -d, --date DATE     Restore from backup with specific date (YYYYMMDD_HHMMSS)"
    echo "  -l, --list          List available backups"
    echo "  -h, --help          Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 --list"
    echo "  $0 --date 20241220_143000"
    exit 1
}

# Function to list available backups
list_backups() {
    log "Available backups:"
    echo ""
    echo "Database backups:"
    ls -la "$BACKUP_DIR"/db_backup_*.sql.gz 2>/dev/null | awk '{print $9, $5, $6, $7, $8}' | column -t
    echo ""
    echo "File backups:"
    ls -la "$BACKUP_DIR"/files_backup_*.tar.gz 2>/dev/null | awk '{print $9, $5, $6, $7, $8}' | column -t
    echo ""
    echo "Configuration backups:"
    ls -la "$BACKUP_DIR"/docker_config_*.tar.gz 2>/dev/null | awk '{print $9, $5, $6, $7, $8}' | column -t
}

# Function to verify backup files exist
verify_backup_files() {
    local date=$1
    local missing_files=()
    
    # Check required backup files
    local files=(
        "db_backup_$date.sql.gz"
        "files_backup_$date.tar.gz"
        "env_backup_$date"
        "docker_config_$date.tar.gz"
    )
    
    for file in "${files[@]}"; do
        if [ ! -f "$BACKUP_DIR/$file" ]; then
            missing_files+=("$file")
        fi
    done
    
    if [ ${#missing_files[@]} -gt 0 ]; then
        log "ERROR: Missing backup files:"
        for file in "${missing_files[@]}"; do
            log "  - $file"
        done
        exit 1
    fi
    
    log "All required backup files found"
}

# Function to create restore point
create_restore_point() {
    local date=$1
    log "Creating restore point before restoration..."
    
    # Create current state backup
    local restore_point_dir="$BACKUP_DIR/restore_point_$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$restore_point_dir"
    
    # Backup current database
    mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --hex-blob \
        --opt \
        "$DB_NAME" > "$restore_point_dir/current_db.sql"
    gzip "$restore_point_dir/current_db.sql"
    
    # Backup current files
    tar -czf "$restore_point_dir/current_files.tar.gz" \
        --exclude='node_modules' \
        --exclude='vendor' \
        --exclude='storage/logs' \
        --exclude='storage/framework/cache' \
        --exclude='storage/framework/sessions' \
        --exclude='storage/framework/views' \
        --exclude='.git' \
        -C "$APP_DIR" .
    
    # Backup current environment
    if [ -f "$APP_DIR/.env" ]; then
        cp "$APP_DIR/.env" "$restore_point_dir/current_env"
    fi
    
    log "Restore point created: $restore_point_dir"
}

# Function to restore database
restore_database() {
    local date=$1
    log "Restoring database from backup..."
    
    # Stop application services
    log "Stopping application services..."
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" stop app queue scheduler
    
    # Drop and recreate database
    log "Recreating database..."
    mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" -e "DROP DATABASE IF EXISTS $DB_NAME;"
    mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    # Restore database
    log "Restoring database data..."
    gunzip -c "$BACKUP_DIR/db_backup_$date.sql.gz" | mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME"
    
    log "Database restored successfully"
}

# Function to restore files
restore_files() {
    local date=$1
    log "Restoring application files..."
    
    # Create temporary directory
    local temp_dir="/tmp/restore_$date"
    mkdir -p "$temp_dir"
    
    # Extract files backup
    tar -xzf "$BACKUP_DIR/files_backup_$date.tar.gz" -C "$temp_dir"
    
    # Backup current files
    local current_backup_dir="/tmp/current_backup_$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$current_backup_dir"
    cp -r "$APP_DIR"/* "$current_backup_dir/" 2>/dev/null || true
    
    # Remove current files (except .env and storage)
    find "$APP_DIR" -type f ! -path "*/storage/*" ! -name ".env" ! -name ".git*" -delete 2>/dev/null || true
    find "$APP_DIR" -type d -empty -delete 2>/dev/null || true
    
    # Copy restored files
    cp -r "$temp_dir"/* "$APP_DIR/"
    
    # Set permissions
    chown -R www-data:www-data "$APP_DIR"
    chmod -R 755 "$APP_DIR"
    chmod -R 775 "$APP_DIR/storage"
    chmod -R 775 "$APP_DIR/bootstrap/cache"
    
    # Cleanup
    rm -rf "$temp_dir"
    
    log "Application files restored successfully"
}

# Function to restore configuration
restore_configuration() {
    local date=$1
    log "Restoring configuration..."
    
    # Restore environment file
    if [ -f "$BACKUP_DIR/env_backup_$date" ]; then
        cp "$BACKUP_DIR/env_backup_$date" "$APP_DIR/.env"
        log "Environment file restored"
    fi
    
    # Restore Docker configuration
    if [ -f "$BACKUP_DIR/docker_config_$date.tar.gz" ]; then
        tar -xzf "$BACKUP_DIR/docker_config_$date.tar.gz" -C "$APP_DIR/"
        log "Docker configuration restored"
    fi
    
    # Restore SSL certificates
    if [ -f "$BACKUP_DIR/ssl_certs_$date.tar.gz" ]; then
        tar -xzf "$BACKUP_DIR/ssl_certs_$date.tar.gz" -C /
        log "SSL certificates restored"
    fi
}

# Function to restart services
restart_services() {
    log "Restarting services..."
    
    # Start services
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" up -d
    
    # Wait for services to be ready
    sleep 30
    
    # Run Laravel commands
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan config:cache
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan route:cache
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan view:cache
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan queue:restart
    
    log "Services restarted successfully"
}

# Function to verify restoration
verify_restoration() {
    local date=$1
    log "Verifying restoration..."
    
    # Check database connection
    if mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" -e "USE $DB_NAME; SELECT 1;" >/dev/null 2>&1; then
        log "✓ Database connection successful"
    else
        log "✗ Database connection failed"
        return 1
    fi
    
    # Check application files
    if [ -f "$APP_DIR/artisan" ]; then
        log "✓ Application files restored"
    else
        log "✗ Application files missing"
        return 1
    fi
    
    # Check services
    if docker-compose -f "$APP_DIR/docker/docker-compose.yml" ps | grep -q "Up"; then
        log "✓ Services are running"
    else
        log "✗ Services are not running"
        return 1
    fi
    
    # Test application
    if curl -f -s "http://localhost/health" >/dev/null; then
        log "✓ Application is responding"
    else
        log "✗ Application is not responding"
        return 1
    fi
    
    log "Restoration verification completed successfully"
}

# Function to send notification
send_notification() {
    local status=$1
    local message=$2
    
    # Send email notification (if configured)
    if command -v mail >/dev/null 2>&1; then
        echo "$message" | mail -s "Restore $status - BookingFlow System" admin@bookingflow.com
    fi
    
    # Send Slack notification (if webhook is configured)
    if [ -n "$SLACK_WEBHOOK" ]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"Restore $status: $message\"}" \
            "$SLACK_WEBHOOK"
    fi
    
    log "Notification sent: $status"
}

# Main restore function
main() {
    local date=""
    
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            -d|--date)
                date="$2"
                shift 2
                ;;
            -l|--list)
                list_backups
                exit 0
                ;;
            -h|--help)
                usage
                ;;
            *)
                echo "Unknown option: $1"
                usage
                ;;
        esac
    done
    
    # Check if date is provided
    if [ -z "$date" ]; then
        echo "ERROR: Backup date is required"
        usage
    fi
    
    log "Starting restore process for backup: $date"
    
    # Verify backup files exist
    verify_backup_files "$date"
    
    # Create restore point
    create_restore_point "$date"
    
    # Restore database
    restore_database "$date"
    
    # Restore files
    restore_files "$date"
    
    # Restore configuration
    restore_configuration "$date"
    
    # Restart services
    restart_services
    
    # Verify restoration
    if verify_restoration "$date"; then
        send_notification "SUCCESS" "System restored successfully from backup $date"
        log "Restore process completed successfully"
    else
        send_notification "FAILED" "Restore verification failed for backup $date"
        log "ERROR: Restore verification failed"
        exit 1
    fi
}

# Error handling
trap 'log "ERROR: Restore failed at line $LINENO"; send_notification "FAILED" "Restore failed at line $LINENO"; exit 1' ERR

# Run main function
main "$@"
