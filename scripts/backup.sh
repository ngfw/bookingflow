#!/bin/bash

# BookingFlow - Backup Script
# This script creates automated backups of the database, files, and configuration

set -e

# Configuration
BACKUP_DIR="/var/backups/beauty-salon"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30
DB_NAME="bookingflow"
DB_USER="bookingflow_user"
DB_PASSWORD="bookingflow_password"
DB_HOST="localhost"
DB_PORT="3306"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$BACKUP_DIR/backup.log"
}

# Function to cleanup old backups
cleanup_old_backups() {
    log "Cleaning up backups older than $RETENTION_DAYS days..."
    find "$BACKUP_DIR" -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
    find "$BACKUP_DIR" -name "*.tar.gz" -mtime +$RETENTION_DAYS -delete
    log "Cleanup completed"
}

# Function to create database backup
backup_database() {
    log "Starting database backup..."
    
    # Create database dump
    mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --hex-blob \
        --opt \
        "$DB_NAME" > "$BACKUP_DIR/db_backup_$DATE.sql"
    
    # Compress the backup
    gzip "$BACKUP_DIR/db_backup_$DATE.sql"
    
    # Verify backup
    if [ -f "$BACKUP_DIR/db_backup_$DATE.sql.gz" ]; then
        log "Database backup completed: db_backup_$DATE.sql.gz"
    else
        log "ERROR: Database backup failed"
        exit 1
    fi
}

# Function to create files backup
backup_files() {
    log "Starting files backup..."
    
    # Create tar archive of application files
    tar -czf "$BACKUP_DIR/files_backup_$DATE.tar.gz" \
        --exclude='node_modules' \
        --exclude='vendor' \
        --exclude='storage/logs' \
        --exclude='storage/framework/cache' \
        --exclude='storage/framework/sessions' \
        --exclude='storage/framework/views' \
        --exclude='.git' \
        --exclude='.env' \
        -C /var/www/beauty-salon .
    
    # Verify backup
    if [ -f "$BACKUP_DIR/files_backup_$DATE.tar.gz" ]; then
        log "Files backup completed: files_backup_$DATE.tar.gz"
    else
        log "ERROR: Files backup failed"
        exit 1
    fi
}

# Function to backup configuration
backup_config() {
    log "Starting configuration backup..."
    
    # Backup environment file
    if [ -f "/var/www/beauty-salon/.env" ]; then
        cp "/var/www/beauty-salon/.env" "$BACKUP_DIR/env_backup_$DATE"
        log "Environment file backed up"
    fi
    
    # Backup Docker configuration
    if [ -d "/var/www/beauty-salon/docker" ]; then
        tar -czf "$BACKUP_DIR/docker_config_$DATE.tar.gz" \
            -C /var/www/beauty-salon docker/
        log "Docker configuration backed up"
    fi
    
    # Backup SSL certificates
    if [ -d "/etc/nginx/ssl" ]; then
        tar -czf "$BACKUP_DIR/ssl_certs_$DATE.tar.gz" \
            -C /etc/nginx ssl/
        log "SSL certificates backed up"
    fi
}

# Function to backup logs
backup_logs() {
    log "Starting logs backup..."
    
    # Backup application logs
    if [ -d "/var/www/beauty-salon/storage/logs" ]; then
        tar -czf "$BACKUP_DIR/logs_backup_$DATE.tar.gz" \
            -C /var/www/beauty-salon storage/logs/
        log "Application logs backed up"
    fi
    
    # Backup system logs
    if [ -d "/var/log" ]; then
        tar -czf "$BACKUP_DIR/system_logs_$DATE.tar.gz" \
            --exclude='*.log.*' \
            --exclude='*.gz' \
            -C /var/log .
        log "System logs backed up"
    fi
}

# Function to create backup manifest
create_manifest() {
    log "Creating backup manifest..."
    
    cat > "$BACKUP_DIR/manifest_$DATE.txt" << EOF
BookingFlow - Backup Manifest
Generated: $(date)
Backup Date: $DATE
Retention: $RETENTION_DAYS days

Files included in this backup:
- Database: db_backup_$DATE.sql.gz
- Application Files: files_backup_$DATE.tar.gz
- Configuration: env_backup_$DATE, docker_config_$DATE.tar.gz, ssl_certs_$DATE.tar.gz
- Logs: logs_backup_$DATE.tar.gz, system_logs_$DATE.tar.gz

Backup sizes:
$(du -h "$BACKUP_DIR"/*_$DATE* 2>/dev/null || echo "No files found")

System information:
- Hostname: $(hostname)
- OS: $(uname -a)
- Disk usage: $(df -h /)
- Memory usage: $(free -h)

EOF
    
    log "Backup manifest created: manifest_$DATE.txt"
}

# Function to verify backup integrity
verify_backup() {
    log "Verifying backup integrity..."
    
    # Check if all backup files exist
    local files=(
        "db_backup_$DATE.sql.gz"
        "files_backup_$DATE.tar.gz"
        "env_backup_$DATE"
        "docker_config_$DATE.tar.gz"
        "ssl_certs_$DATE.tar.gz"
        "logs_backup_$DATE.tar.gz"
        "system_logs_$DATE.tar.gz"
        "manifest_$DATE.txt"
    )
    
    for file in "${files[@]}"; do
        if [ -f "$BACKUP_DIR/$file" ]; then
            log "✓ $file exists"
        else
            log "✗ $file missing"
        fi
    done
    
    # Test database backup
    if [ -f "$BACKUP_DIR/db_backup_$DATE.sql.gz" ]; then
        if gzip -t "$BACKUP_DIR/db_backup_$DATE.sql.gz"; then
            log "✓ Database backup is valid"
        else
            log "✗ Database backup is corrupted"
        fi
    fi
    
    # Test file archives
    for archive in "$BACKUP_DIR"/*_$DATE.tar.gz; do
        if [ -f "$archive" ]; then
            if tar -tzf "$archive" >/dev/null 2>&1; then
                log "✓ $(basename "$archive") is valid"
            else
                log "✗ $(basename "$archive") is corrupted"
            fi
        fi
    done
}

# Function to send backup notification
send_notification() {
    local status=$1
    local message=$2
    
    # Send email notification (if configured)
    if command -v mail >/dev/null 2>&1; then
        echo "$message" | mail -s "Backup $status - BookingFlow System" admin@bookingflow.com
    fi
    
    # Send Slack notification (if webhook is configured)
    if [ -n "$SLACK_WEBHOOK" ]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"Backup $status: $message\"}" \
            "$SLACK_WEBHOOK"
    fi
    
    log "Notification sent: $status"
}

# Function to upload backup to cloud storage
upload_to_cloud() {
    if [ -n "$AWS_S3_BUCKET" ] && command -v aws >/dev/null 2>&1; then
        log "Uploading backup to S3..."
        aws s3 sync "$BACKUP_DIR" "s3://$AWS_S3_BUCKET/backups/" \
            --exclude "*" \
            --include "*_$DATE*"
        log "Backup uploaded to S3"
    fi
    
    if [ -n "$GOOGLE_CLOUD_BUCKET" ] && command -v gsutil >/dev/null 2>&1; then
        log "Uploading backup to Google Cloud..."
        gsutil -m cp "$BACKUP_DIR"/*_$DATE* "gs://$GOOGLE_CLOUD_BUCKET/backups/"
        log "Backup uploaded to Google Cloud"
    fi
}

# Main backup function
main() {
    log "Starting backup process..."
    
    # Create database backup
    backup_database
    
    # Create files backup
    backup_files
    
    # Create configuration backup
    backup_config
    
    # Create logs backup
    backup_logs
    
    # Create manifest
    create_manifest
    
    # Verify backup integrity
    verify_backup
    
    # Cleanup old backups
    cleanup_old_backups
    
    # Upload to cloud storage (if configured)
    upload_to_cloud
    
    # Send success notification
    send_notification "SUCCESS" "Backup completed successfully on $(date)"
    
    log "Backup process completed successfully"
}

# Error handling
trap 'log "ERROR: Backup failed at line $LINENO"; send_notification "FAILED" "Backup failed at line $LINENO"; exit 1' ERR

# Run main function
main "$@"
