#!/bin/bash

# Beauty Salon Management System - Disaster Recovery Script
# This script handles disaster recovery scenarios and system restoration

set -e

# Configuration
BACKUP_DIR="/var/backups/beauty-salon"
APP_DIR="/var/www/beauty-salon"
DB_NAME="beauty_salon"
DB_USER="beauty_salon_user"
DB_PASSWORD="beauty_salon_password"
DB_HOST="localhost"
DB_PORT="3306"
RECOVERY_LOG="/var/log/disaster-recovery.log"

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$RECOVERY_LOG"
}

# Function to check system health
check_system_health() {
    log "Checking system health..."
    
    local issues=()
    
    # Check disk space
    local disk_usage=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
    if [ "$disk_usage" -gt 90 ]; then
        issues+=("High disk usage: ${disk_usage}%")
    fi
    
    # Check memory usage
    local memory_usage=$(free | awk 'NR==2{printf "%.0f", $3*100/$2}')
    if [ "$memory_usage" -gt 90 ]; then
        issues+=("High memory usage: ${memory_usage}%")
    fi
    
    # Check database connectivity
    if ! mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1;" >/dev/null 2>&1; then
        issues+=("Database connection failed")
    fi
    
    # Check application status
    if ! curl -f -s "http://localhost/health" >/dev/null; then
        issues+=("Application is not responding")
    fi
    
    # Check Docker services
    if ! docker-compose -f "$APP_DIR/docker/docker-compose.yml" ps | grep -q "Up"; then
        issues+=("Docker services are not running")
    fi
    
    if [ ${#issues[@]} -eq 0 ]; then
        log "✓ System health check passed"
        return 0
    else
        log "✗ System health issues detected:"
        for issue in "${issues[@]}"; do
            log "  - $issue"
        done
        return 1
    fi
}

# Function to assess disaster impact
assess_disaster_impact() {
    log "Assessing disaster impact..."
    
    local impact_level="LOW"
    local affected_components=()
    
    # Check database
    if ! mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1;" >/dev/null 2>&1; then
        affected_components+=("Database")
        impact_level="HIGH"
    fi
    
    # Check application files
    if [ ! -f "$APP_DIR/artisan" ]; then
        affected_components+=("Application Files")
        impact_level="CRITICAL"
    fi
    
    # Check configuration
    if [ ! -f "$APP_DIR/.env" ]; then
        affected_components+=("Configuration")
        impact_level="HIGH"
    fi
    
    # Check Docker services
    if ! docker-compose -f "$APP_DIR/docker/docker-compose.yml" ps | grep -q "Up"; then
        affected_components+=("Docker Services")
        impact_level="MEDIUM"
    fi
    
    log "Disaster impact level: $impact_level"
    log "Affected components: ${affected_components[*]}"
    
    echo "$impact_level"
}

# Function to activate emergency mode
activate_emergency_mode() {
    log "Activating emergency mode..."
    
    # Create emergency maintenance page
    cat > "$APP_DIR/public/maintenance.html" << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>System Maintenance</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .container { max-width: 600px; margin: 0 auto; }
        .message { background: #f8f9fa; padding: 30px; border-radius: 8px; }
        .status { color: #dc3545; font-size: 24px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="message">
            <div class="status">⚠️ System Maintenance</div>
            <h1>We're currently experiencing technical difficulties</h1>
            <p>Our team is working to restore service as quickly as possible.</p>
            <p>We apologize for any inconvenience.</p>
            <p><strong>Estimated recovery time: 30-60 minutes</strong></p>
        </div>
    </div>
</body>
</html>
EOF
    
    # Redirect all traffic to maintenance page
    if [ -f "$APP_DIR/docker/nginx.conf" ]; then
        cp "$APP_DIR/docker/nginx.conf" "$APP_DIR/docker/nginx.conf.backup"
        sed -i 's|try_files \$uri \$uri/ /index.php|\$query_string;|g' "$APP_DIR/docker/nginx.conf"
        sed -i 's|/index.php|\$query_string;|g' "$APP_DIR/docker/nginx.conf"
        echo "        location / { return 200 '<!DOCTYPE html><html><head><title>Maintenance</title></head><body><h1>System Maintenance</h1><p>We are currently performing maintenance. Please check back soon.</p></body></html>'; add_header Content-Type text/html; }" >> "$APP_DIR/docker/nginx.conf"
    fi
    
    # Restart nginx
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" restart nginx
    
    log "Emergency mode activated"
}

# Function to deactivate emergency mode
deactivate_emergency_mode() {
    log "Deactivating emergency mode..."
    
    # Restore nginx configuration
    if [ -f "$APP_DIR/docker/nginx.conf.backup" ]; then
        mv "$APP_DIR/docker/nginx.conf.backup" "$APP_DIR/docker/nginx.conf"
    fi
    
    # Remove maintenance page
    rm -f "$APP_DIR/public/maintenance.html"
    
    # Restart nginx
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" restart nginx
    
    log "Emergency mode deactivated"
}

# Function to find latest backup
find_latest_backup() {
    log "Finding latest backup..."
    
    local latest_db=$(ls -t "$BACKUP_DIR"/db_backup_*.sql.gz 2>/dev/null | head -n1)
    local latest_files=$(ls -t "$BACKUP_DIR"/files_backup_*.tar.gz 2>/dev/null | head -n1)
    
    if [ -z "$latest_db" ] || [ -z "$latest_files" ]; then
        log "ERROR: No backup files found"
        return 1
    fi
    
    # Extract date from backup filename
    local db_date=$(basename "$latest_db" | sed 's/db_backup_\(.*\)\.sql\.gz/\1/')
    local files_date=$(basename "$latest_files" | sed 's/files_backup_\(.*\)\.tar\.gz/\1/')
    
    if [ "$db_date" = "$files_date" ]; then
        log "Latest backup found: $db_date"
        echo "$db_date"
        return 0
    else
        log "ERROR: Backup dates don't match (DB: $db_date, Files: $files_date)"
        return 1
    fi
}

# Function to perform full system recovery
perform_full_recovery() {
    log "Performing full system recovery..."
    
    # Find latest backup
    local backup_date=$(find_latest_backup)
    if [ -z "$backup_date" ]; then
        log "ERROR: No valid backup found"
        return 1
    fi
    
    # Activate emergency mode
    activate_emergency_mode
    
    # Stop all services
    log "Stopping all services..."
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" down
    
    # Restore from backup
    log "Restoring from backup: $backup_date"
    "$APP_DIR/scripts/restore.sh" --date "$backup_date"
    
    # Deactivate emergency mode
    deactivate_emergency_mode
    
    # Verify recovery
    if check_system_health; then
        log "✓ Full system recovery completed successfully"
        return 0
    else
        log "✗ Full system recovery failed"
        return 1
    fi
}

# Function to perform partial recovery
perform_partial_recovery() {
    log "Performing partial recovery..."
    
    # Check what needs to be recovered
    local needs_db_recovery=false
    local needs_files_recovery=false
    local needs_config_recovery=false
    
    # Check database
    if ! mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1;" >/dev/null 2>&1; then
        needs_db_recovery=true
    fi
    
    # Check application files
    if [ ! -f "$APP_DIR/artisan" ]; then
        needs_files_recovery=true
    fi
    
    # Check configuration
    if [ ! -f "$APP_DIR/.env" ]; then
        needs_config_recovery=true
    fi
    
    # Find latest backup
    local backup_date=$(find_latest_backup)
    if [ -z "$backup_date" ]; then
        log "ERROR: No valid backup found"
        return 1
    fi
    
    # Activate emergency mode
    activate_emergency_mode
    
    # Restore database if needed
    if [ "$needs_db_recovery" = true ]; then
        log "Restoring database..."
        gunzip -c "$BACKUP_DIR/db_backup_$backup_date.sql.gz" | mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME"
    fi
    
    # Restore files if needed
    if [ "$needs_files_recovery" = true ]; then
        log "Restoring application files..."
        tar -xzf "$BACKUP_DIR/files_backup_$backup_date.tar.gz" -C "$APP_DIR/"
        chown -R www-data:www-data "$APP_DIR"
        chmod -R 755 "$APP_DIR"
        chmod -R 775 "$APP_DIR/storage"
        chmod -R 775 "$APP_DIR/bootstrap/cache"
    fi
    
    # Restore configuration if needed
    if [ "$needs_config_recovery" = true ]; then
        log "Restoring configuration..."
        if [ -f "$BACKUP_DIR/env_backup_$backup_date" ]; then
            cp "$BACKUP_DIR/env_backup_$backup_date" "$APP_DIR/.env"
        fi
    fi
    
    # Restart services
    log "Restarting services..."
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" up -d
    
    # Wait for services to be ready
    sleep 30
    
    # Run Laravel commands
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan config:cache
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan route:cache
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan view:cache
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan queue:restart
    
    # Deactivate emergency mode
    deactivate_emergency_mode
    
    # Verify recovery
    if check_system_health; then
        log "✓ Partial recovery completed successfully"
        return 0
    else
        log "✗ Partial recovery failed"
        return 1
    fi
}

# Function to perform quick fix
perform_quick_fix() {
    log "Performing quick fix..."
    
    # Restart services
    log "Restarting services..."
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" restart
    
    # Wait for services to be ready
    sleep 30
    
    # Clear caches
    log "Clearing caches..."
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan cache:clear
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan config:clear
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan route:clear
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan view:clear
    
    # Rebuild caches
    log "Rebuilding caches..."
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan config:cache
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan route:cache
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan view:cache
    
    # Restart queue
    log "Restarting queue..."
    docker-compose -f "$APP_DIR/docker/docker-compose.yml" exec -T app php artisan queue:restart
    
    # Verify fix
    if check_system_health; then
        log "✓ Quick fix completed successfully"
        return 0
    else
        log "✗ Quick fix failed"
        return 1
    fi
}

# Function to send emergency notification
send_emergency_notification() {
    local status=$1
    local message=$2
    
    # Send email notification
    if command -v mail >/dev/null 2>&1; then
        echo "$message" | mail -s "EMERGENCY: $status - Beauty Salon System" admin@beautysalon.com
    fi
    
    # Send Slack notification
    if [ -n "$SLACK_WEBHOOK" ]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"🚨 EMERGENCY: $status - $message\"}" \
            "$SLACK_WEBHOOK"
    fi
    
    # Send SMS notification (if configured)
    if [ -n "$SMS_API_KEY" ] && [ -n "$SMS_PHONE" ]; then
        curl -X POST "https://api.twilio.com/2010-04-01/Accounts/$SMS_API_KEY/Messages.json" \
            --data-urlencode "To=$SMS_PHONE" \
            --data-urlencode "From=+1234567890" \
            --data-urlencode "Body=EMERGENCY: $status - $message" \
            -u "$SMS_API_KEY:$SMS_API_SECRET"
    fi
    
    log "Emergency notification sent: $status"
}

# Function to create recovery report
create_recovery_report() {
    local recovery_type=$1
    local success=$2
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    cat > "$BACKUP_DIR/recovery_report_$(date +%Y%m%d_%H%M%S).txt" << EOF
Beauty Salon Management System - Disaster Recovery Report
Generated: $timestamp
Recovery Type: $recovery_type
Status: $([ "$success" = true ] && echo "SUCCESS" || echo "FAILED")

System Information:
- Hostname: $(hostname)
- OS: $(uname -a)
- Disk Usage: $(df -h /)
- Memory Usage: $(free -h)
- Load Average: $(uptime | awk -F'load average:' '{print $2}')

Recovery Actions Taken:
$(tail -n 50 "$RECOVERY_LOG")

Current System Status:
$(docker-compose -f "$APP_DIR/docker/docker-compose.yml" ps)

EOF
    
    log "Recovery report created"
}

# Main disaster recovery function
main() {
    local action=${1:-"auto"}
    
    log "Starting disaster recovery process..."
    log "Action: $action"
    
    # Check system health
    if check_system_health; then
        log "System is healthy, no recovery needed"
        exit 0
    fi
    
    # Assess disaster impact
    local impact=$(assess_disaster_impact)
    log "Disaster impact level: $impact"
    
    # Send emergency notification
    send_emergency_notification "DISASTER_DETECTED" "System disaster detected with $impact impact level"
    
    # Perform recovery based on action
    case $action in
        "auto")
            case $impact in
                "LOW"|"MEDIUM")
                    if perform_quick_fix; then
                        create_recovery_report "QUICK_FIX" true
                        send_emergency_notification "RECOVERY_SUCCESS" "Quick fix completed successfully"
                    else
                        create_recovery_report "QUICK_FIX" false
                        send_emergency_notification "RECOVERY_FAILED" "Quick fix failed"
                        exit 1
                    fi
                    ;;
                "HIGH")
                    if perform_partial_recovery; then
                        create_recovery_report "PARTIAL_RECOVERY" true
                        send_emergency_notification "RECOVERY_SUCCESS" "Partial recovery completed successfully"
                    else
                        create_recovery_report "PARTIAL_RECOVERY" false
                        send_emergency_notification "RECOVERY_FAILED" "Partial recovery failed"
                        exit 1
                    fi
                    ;;
                "CRITICAL")
                    if perform_full_recovery; then
                        create_recovery_report "FULL_RECOVERY" true
                        send_emergency_notification "RECOVERY_SUCCESS" "Full recovery completed successfully"
                    else
                        create_recovery_report "FULL_RECOVERY" false
                        send_emergency_notification "RECOVERY_FAILED" "Full recovery failed"
                        exit 1
                    fi
                    ;;
            esac
            ;;
        "quick")
            if perform_quick_fix; then
                create_recovery_report "QUICK_FIX" true
                send_emergency_notification "RECOVERY_SUCCESS" "Quick fix completed successfully"
            else
                create_recovery_report "QUICK_FIX" false
                send_emergency_notification "RECOVERY_FAILED" "Quick fix failed"
                exit 1
            fi
            ;;
        "partial")
            if perform_partial_recovery; then
                create_recovery_report "PARTIAL_RECOVERY" true
                send_emergency_notification "RECOVERY_SUCCESS" "Partial recovery completed successfully"
            else
                create_recovery_report "PARTIAL_RECOVERY" false
                send_emergency_notification "RECOVERY_FAILED" "Partial recovery failed"
                exit 1
            fi
            ;;
        "full")
            if perform_full_recovery; then
                create_recovery_report "FULL_RECOVERY" true
                send_emergency_notification "RECOVERY_SUCCESS" "Full recovery completed successfully"
            else
                create_recovery_report "FULL_RECOVERY" false
                send_emergency_notification "RECOVERY_FAILED" "Full recovery failed"
                exit 1
            fi
            ;;
        *)
            log "ERROR: Unknown action: $action"
            log "Available actions: auto, quick, partial, full"
            exit 1
            ;;
    esac
    
    log "Disaster recovery process completed"
}

# Error handling
trap 'log "ERROR: Disaster recovery failed at line $LINENO"; send_emergency_notification "RECOVERY_FAILED" "Disaster recovery failed at line $LINENO"; exit 1' ERR

# Run main function
main "$@"
