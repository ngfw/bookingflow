#!/bin/bash

# Beauty Salon Management System - Performance Optimization Script
# This script optimizes system performance for production deployment

set -e

# Configuration
APP_DIR="/var/www/beauty-salon"
NGINX_CONF="/etc/nginx/nginx.conf"
PHP_CONF="/etc/php/8.2/fpm/php.ini"
MYSQL_CONF="/etc/mysql/mysql.conf.d/mysqld.cnf"
REDIS_CONF="/etc/redis/redis.conf"

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "/var/log/performance-optimization.log"
}

# Function to check if running as root
check_root() {
    if [ "$EUID" -ne 0 ]; then
        log "ERROR: This script must be run as root"
        exit 1
    fi
}

# Function to optimize PHP configuration
optimize_php() {
    log "Optimizing PHP configuration..."
    
    # Backup original configuration
    cp "$PHP_CONF" "$PHP_CONF.backup"
    
    # Optimize PHP settings
    cat >> "$PHP_CONF" << 'EOF'

; Performance optimizations
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.fast_shutdown=1
opcache.enable_file_override=1

; Memory and execution limits
memory_limit=512M
max_execution_time=300
max_input_time=300
post_max_size=100M
upload_max_filesize=100M
max_file_uploads=20

; Session optimization
session.save_handler=redis
session.save_path="tcp://127.0.0.1:6379"
session.gc_probability=1
session.gc_divisor=1000
session.gc_maxlifetime=1440

; Realpath cache
realpath_cache_size=4096K
realpath_cache_ttl=600

; Output buffering
output_buffering=4096
implicit_flush=Off

; Error handling
log_errors=On
display_errors=Off
html_errors=Off
EOF
    
    # Restart PHP-FPM
    systemctl restart php8.2-fpm
    
    log "PHP configuration optimized"
}

# Function to optimize Nginx configuration
optimize_nginx() {
    log "Optimizing Nginx configuration..."
    
    # Backup original configuration
    cp "$NGINX_CONF" "$NGINX_CONF.backup"
    
    # Optimize Nginx settings
    cat > "$NGINX_CONF" << 'EOF'
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 2048;
    use epoll;
    multi_accept on;
    worker_rlimit_nofile 65535;
}

http {
    # Basic settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    client_header_buffer_size 1k;
    large_client_header_buffers 4 4k;
    output_buffers 1 32k;
    postpone_output 1460;

    # MIME types
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logging
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for" '
                    '$request_time $upstream_response_time';

    access_log /var/log/nginx/access.log main;
    error_log /var/log/nginx/error.log warn;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_min_length 1000;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml
        application/x-font-ttf
        application/vnd.ms-fontobject
        font/opentype;

    # Brotli compression (if available)
    # brotli on;
    # brotli_comp_level 6;
    # brotli_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    limit_req_zone $binary_remote_addr zone=general:10m rate=20r/s;

    # Connection limiting
    limit_conn_zone $binary_remote_addr zone=conn_limit_per_ip:10m;
    limit_conn_zone $server_name zone=conn_limit_per_server:10m;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # File caching
    open_file_cache max=10000 inactive=20s;
    open_file_cache_valid 30s;
    open_file_cache_min_uses 2;
    open_file_cache_errors on;

    # FastCGI caching
    fastcgi_cache_path /var/cache/nginx/fastcgi levels=1:2 keys_zone=WORDPRESS:100m inactive=60m;
    fastcgi_cache_key "$scheme$request_method$host$request_uri";
    fastcgi_cache_use_stale error timeout invalid_header http_500;
    fastcgi_ignore_headers Cache-Control Expires Set-Cookie;

    # Upstream for PHP-FPM
    upstream php-fpm {
        server unix:/var/run/php/php8.2-fpm.sock;
        keepalive 32;
    }

    # Include server configurations
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
EOF
    
    # Create cache directory
    mkdir -p /var/cache/nginx/fastcgi
    chown -R www-data:www-data /var/cache/nginx
    
    # Test configuration
    nginx -t
    
    # Reload Nginx
    systemctl reload nginx
    
    log "Nginx configuration optimized"
}

# Function to optimize MySQL configuration
optimize_mysql() {
    log "Optimizing MySQL configuration..."
    
    # Backup original configuration
    cp "$MYSQL_CONF" "$MYSQL_CONF.backup"
    
    # Get system memory
    local total_memory=$(free -m | awk 'NR==2{printf "%.0f", $2}')
    local innodb_buffer_pool_size=$((total_memory * 70 / 100))
    local key_buffer_size=$((total_memory * 10 / 100))
    local max_connections=200
    
    # Optimize MySQL settings
    cat >> "$MYSQL_CONF" << EOF

# Performance optimizations
[mysqld]
# Memory settings
innodb_buffer_pool_size = ${innodb_buffer_pool_size}M
key_buffer_size = ${key_buffer_size}M
max_connections = $max_connections
thread_cache_size = 16
table_open_cache = 4000
table_definition_cache = 2000

# InnoDB settings
innodb_log_file_size = 256M
innodb_log_buffer_size = 16M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1
innodb_read_io_threads = 4
innodb_write_io_threads = 4
innodb_io_capacity = 2000
innodb_io_capacity_max = 4000

# Query cache
query_cache_type = 1
query_cache_size = 64M
query_cache_limit = 2M

# Temporary tables
tmp_table_size = 64M
max_heap_table_size = 64M

# Connection settings
wait_timeout = 600
interactive_timeout = 600
connect_timeout = 10

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
log_queries_not_using_indexes = 1

# Binary logging
expire_logs_days = 7
max_binlog_size = 100M

# Security
local_infile = 0
symbolic-links = 0
EOF
    
    # Restart MySQL
    systemctl restart mysql
    
    log "MySQL configuration optimized"
}

# Function to optimize Redis configuration
optimize_redis() {
    log "Optimizing Redis configuration..."
    
    # Backup original configuration
    cp "$REDIS_CONF" "$REDIS_CONF.backup"
    
    # Optimize Redis settings
    cat >> "$REDIS_CONF" << 'EOF'

# Performance optimizations
maxmemory 256mb
maxmemory-policy allkeys-lru
tcp-keepalive 60
timeout 300
tcp-backlog 511

# Persistence
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir /var/lib/redis

# Logging
loglevel notice
logfile /var/log/redis/redis-server.log

# Slow log
slowlog-log-slower-than 10000
slowlog-max-len 128

# Client output buffer limits
client-output-buffer-limit normal 0 0 0
client-output-buffer-limit replica 256mb 64mb 60
client-output-buffer-limit pubsub 32mb 8mb 60

# Advanced config
hash-max-ziplist-entries 512
hash-max-ziplist-value 64
list-max-ziplist-size -2
list-compress-depth 0
set-max-intset-entries 512
zset-max-ziplist-entries 128
zset-max-ziplist-value 64
hll-sparse-max-bytes 3000
stream-node-max-bytes 4096
stream-node-max-entries 100
activerehashing yes
client-output-buffer-limit normal 0 0 0
client-output-buffer-limit replica 256mb 64mb 60
client-output-buffer-limit pubsub 32mb 8mb 60
hz 10
dynamic-hz yes
aof-rewrite-incremental-fsync yes
rdb-save-incremental-fsync yes
EOF
    
    # Restart Redis
    systemctl restart redis-server
    
    log "Redis configuration optimized"
}

# Function to optimize Laravel application
optimize_laravel() {
    log "Optimizing Laravel application..."
    
    cd "$APP_DIR"
    
    # Clear all caches
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    # Optimize autoloader
    composer dump-autoload --optimize --no-dev
    
    # Cache configuration
    php artisan config:cache
    
    # Cache routes
    php artisan route:cache
    
    # Cache views
    php artisan view:cache
    
    # Optimize database
    php artisan optimize
    
    # Set proper permissions
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
    
    log "Laravel application optimized"
}

# Function to configure system limits
configure_system_limits() {
    log "Configuring system limits..."
    
    # Configure system limits
    cat >> "/etc/security/limits.conf" << 'EOF'

# Beauty Salon Management System limits
www-data soft nofile 65535
www-data hard nofile 65535
nginx soft nofile 65535
nginx hard nofile 65535
mysql soft nofile 65535
mysql hard nofile 65535
redis soft nofile 65535
redis hard nofile 65535
EOF
    
    # Configure systemd limits
    mkdir -p /etc/systemd/system/nginx.service.d
    cat > "/etc/systemd/system/nginx.service.d/limits.conf" << 'EOF'
[Service]
LimitNOFILE=65535
EOF
    
    mkdir -p /etc/systemd/system/php8.2-fpm.service.d
    cat > "/etc/systemd/system/php8.2-fpm.service.d/limits.conf" << 'EOF'
[Service]
LimitNOFILE=65535
EOF
    
    # Reload systemd
    systemctl daemon-reload
    
    log "System limits configured"
}

# Function to configure kernel parameters
configure_kernel_parameters() {
    log "Configuring kernel parameters..."
    
    # Configure kernel parameters
    cat >> "/etc/sysctl.conf" << 'EOF'

# Beauty Salon Management System kernel optimizations
# Network optimizations
net.core.rmem_default = 262144
net.core.rmem_max = 16777216
net.core.wmem_default = 262144
net.core.wmem_max = 16777216
net.core.netdev_max_backlog = 5000
net.core.somaxconn = 65535
net.ipv4.tcp_rmem = 4096 65536 16777216
net.ipv4.tcp_wmem = 4096 65536 16777216
net.ipv4.tcp_congestion_control = bbr
net.ipv4.tcp_slow_start_after_idle = 0
net.ipv4.tcp_tw_reuse = 1
net.ipv4.tcp_fin_timeout = 15
net.ipv4.tcp_keepalive_time = 1200
net.ipv4.tcp_keepalive_intvl = 30
net.ipv4.tcp_keepalive_probes = 3
net.ipv4.tcp_max_syn_backlog = 8192
net.ipv4.tcp_max_tw_buckets = 2000000
net.ipv4.tcp_fastopen = 3
net.ipv4.tcp_mtu_probing = 1
net.ipv4.tcp_no_metrics_save = 1
net.ipv4.tcp_sack = 1
net.ipv4.tcp_timestamps = 1
net.ipv4.tcp_window_scaling = 1

# File system optimizations
fs.file-max = 2097152
fs.nr_open = 1048576

# Memory optimizations
vm.swappiness = 10
vm.dirty_ratio = 15
vm.dirty_background_ratio = 5
vm.overcommit_memory = 1
vm.max_map_count = 262144

# Security
kernel.dmesg_restrict = 1
kernel.kptr_restrict = 2
net.ipv4.conf.all.send_redirects = 0
net.ipv4.conf.default.send_redirects = 0
net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.all.secure_redirects = 0
net.ipv4.conf.default.secure_redirects = 0
net.ipv4.conf.all.log_martians = 1
net.ipv4.conf.default.log_martians = 1
net.ipv4.icmp_echo_ignore_broadcasts = 1
net.ipv4.icmp_ignore_bogus_error_responses = 1
net.ipv4.conf.all.rp_filter = 1
net.ipv4.conf.default.rp_filter = 1
net.ipv4.tcp_syncookies = 1
EOF
    
    # Apply kernel parameters
    sysctl -p
    
    log "Kernel parameters configured"
}

# Function to configure log rotation
configure_log_rotation() {
    log "Configuring log rotation..."
    
    # Configure logrotate for application logs
    cat > "/etc/logrotate.d/beauty-salon" << 'EOF'
/var/www/beauty-salon/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        /usr/bin/docker-compose -f /var/www/beauty-salon/docker/docker-compose.yml exec -T app php artisan log:clear
    endscript
}

/var/log/nginx/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        /bin/kill -USR1 `cat /run/nginx.pid 2>/dev/null` 2>/dev/null || true
    endscript
}

/var/log/mysql/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 640 mysql mysql
    postrotate
        /bin/kill -HUP `cat /var/run/mysqld/mysqld.pid 2>/dev/null` 2>/dev/null || true
    endscript
}

/var/log/redis/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 640 redis redis
    postrotate
        /bin/kill -USR1 `cat /var/run/redis/redis-server.pid 2>/dev/null` 2>/dev/null || true
    endscript
}
EOF
    
    log "Log rotation configured"
}

# Function to create performance monitoring script
create_performance_monitor() {
    log "Creating performance monitoring script..."
    
    cat > "/usr/local/bin/performance-monitor.sh" << 'EOF'
#!/bin/bash

# Performance Monitoring Script
LOG_FILE="/var/log/performance-monitor.log"
ALERT_EMAIL="admin@beautysalon.com"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Check system performance
check_performance() {
    local cpu_usage=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | awk -F'%' '{print $1}')
    local memory_usage=$(free | awk 'NR==2{printf "%.0f", $3*100/$2}')
    local disk_usage=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
    local load_average=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
    
    # Check if performance is degraded
    local alerts=()
    
    if (( $(echo "$cpu_usage > 80" | bc -l) )); then
        alerts+=("High CPU usage: ${cpu_usage}%")
    fi
    
    if [ "$memory_usage" -gt 85 ]; then
        alerts+=("High memory usage: ${memory_usage}%")
    fi
    
    if [ "$disk_usage" -gt 85 ]; then
        alerts+=("High disk usage: ${disk_usage}%")
    fi
    
    if (( $(echo "$load_average > 4" | bc -l) )); then
        alerts+=("High load average: $load_average")
    fi
    
    # Send alerts if any
    if [ ${#alerts[@]} -gt 0 ]; then
        local alert_message="Performance alerts detected:\n"
        for alert in "${alerts[@]}"; do
            alert_message+="- $alert\n"
        done
        
        log "Performance alerts: ${alerts[*]}"
        
        if command -v mail >/dev/null 2>&1; then
            echo -e "$alert_message" | mail -s "Performance Alert - Beauty Salon System" "$ALERT_EMAIL"
        fi
    else
        log "System performance is normal (CPU: ${cpu_usage}%, Memory: ${memory_usage}%, Disk: ${disk_usage}%, Load: $load_average)"
    fi
}

# Check application performance
check_application_performance() {
    local response_time=$(curl -o /dev/null -s -w '%{time_total}' http://localhost/health 2>/dev/null || echo "0")
    local http_status=$(curl -o /dev/null -s -w '%{http_code}' http://localhost/health 2>/dev/null || echo "000")
    
    if [ "$http_status" != "200" ]; then
        log "Application health check failed (HTTP: $http_status)"
        if command -v mail >/dev/null 2>&1; then
            echo "Application health check failed with HTTP status $http_status" | mail -s "Application Alert - Beauty Salon System" "$ALERT_EMAIL"
        fi
    elif (( $(echo "$response_time > 2" | bc -l) )); then
        log "Application response time is slow: ${response_time}s"
        if command -v mail >/dev/null 2>&1; then
            echo "Application response time is slow: ${response_time}s" | mail -s "Performance Alert - Beauty Salon System" "$ALERT_EMAIL"
        fi
    else
        log "Application performance is normal (Response time: ${response_time}s)"
    fi
}

# Check database performance
check_database_performance() {
    local slow_queries=$(mysql -u root -p"$DB_PASSWORD" -e "SHOW GLOBAL STATUS LIKE 'Slow_queries';" 2>/dev/null | awk 'NR==2 {print $2}')
    local connections=$(mysql -u root -p"$DB_PASSWORD" -e "SHOW GLOBAL STATUS LIKE 'Threads_connected';" 2>/dev/null | awk 'NR==2 {print $2}')
    local max_connections=$(mysql -u root -p"$DB_PASSWORD" -e "SHOW VARIABLES LIKE 'max_connections';" 2>/dev/null | awk 'NR==2 {print $2}')
    
    if [ -n "$slow_queries" ] && [ "$slow_queries" -gt 100 ]; then
        log "High number of slow queries: $slow_queries"
        if command -v mail >/dev/null 2>&1; then
            echo "High number of slow queries detected: $slow_queries" | mail -s "Database Alert - Beauty Salon System" "$ALERT_EMAIL"
        fi
    fi
    
    if [ -n "$connections" ] && [ -n "$max_connections" ]; then
        local connection_usage=$((connections * 100 / max_connections))
        if [ "$connection_usage" -gt 80 ]; then
            log "High database connection usage: ${connection_usage}%"
            if command -v mail >/dev/null 2>&1; then
                echo "High database connection usage: ${connection_usage}%" | mail -s "Database Alert - Beauty Salon System" "$ALERT_EMAIL"
            fi
        fi
    fi
}

# Main monitoring function
main() {
    log "Starting performance monitoring..."
    
    check_performance
    check_application_performance
    check_database_performance
    
    log "Performance monitoring completed"
}

# Run main function
main "$@"
EOF
    
    # Make script executable
    chmod +x "/usr/local/bin/performance-monitor.sh"
    
    # Add to crontab (run every 5 minutes)
    (crontab -l 2>/dev/null; echo "*/5 * * * * /usr/local/bin/performance-monitor.sh") | crontab -
    
    log "Performance monitoring script created"
}

# Function to test performance optimizations
test_performance() {
    log "Testing performance optimizations..."
    
    # Test PHP
    if php -m | grep -q "Zend OPcache"; then
        log "✓ PHP OPcache is enabled"
    else
        log "✗ PHP OPcache is not enabled"
    fi
    
    # Test Nginx
    if nginx -t >/dev/null 2>&1; then
        log "✓ Nginx configuration is valid"
    else
        log "✗ Nginx configuration is invalid"
    fi
    
    # Test MySQL
    if systemctl is-active --quiet mysql; then
        log "✓ MySQL is running"
    else
        log "✗ MySQL is not running"
    fi
    
    # Test Redis
    if systemctl is-active --quiet redis-server; then
        log "✓ Redis is running"
    else
        log "✗ Redis is not running"
    fi
    
    # Test application
    if curl -f -s "http://localhost/health" >/dev/null; then
        log "✓ Application is responding"
    else
        log "✗ Application is not responding"
    fi
    
    # Performance test
    local response_time=$(curl -o /dev/null -s -w '%{time_total}' http://localhost/health 2>/dev/null || echo "0")
    log "Application response time: ${response_time}s"
    
    log "Performance optimization test completed"
}

# Function to show performance status
show_performance_status() {
    log "Performance Optimization Status:"
    echo ""
    echo "System Information:"
    echo "CPU Usage: $(top -bn1 | grep "Cpu(s)" | awk '{print $2}')"
    echo "Memory Usage: $(free | awk 'NR==2{printf "%.1f%%", $3*100/$2}')"
    echo "Disk Usage: $(df / | awk 'NR==2 {print $5}')"
    echo "Load Average: $(uptime | awk -F'load average:' '{print $2}')"
    echo ""
    echo "Service Status:"
    echo "Nginx: $(systemctl is-active nginx)"
    echo "PHP-FPM: $(systemctl is-active php8.2-fpm)"
    echo "MySQL: $(systemctl is-active mysql)"
    echo "Redis: $(systemctl is-active redis-server)"
    echo ""
    echo "Application Status:"
    local response_time=$(curl -o /dev/null -s -w '%{time_total}' http://localhost/health 2>/dev/null || echo "0")
    echo "Response Time: ${response_time}s"
    echo "HTTP Status: $(curl -o /dev/null -s -w '%{http_code}' http://localhost/health 2>/dev/null || echo "000")"
    echo ""
    echo "Cache Status:"
    echo "OPcache: $(php -r 'echo opcache_get_status()["opcache_enabled"] ? "Enabled" : "Disabled";')"
    echo "Redis: $(redis-cli ping 2>/dev/null || echo "Not responding")"
    echo ""
    echo "Database Status:"
    echo "Connections: $(mysql -u root -p"$DB_PASSWORD" -e "SHOW GLOBAL STATUS LIKE 'Threads_connected';" 2>/dev/null | awk 'NR==2 {print $2}' || echo "N/A")"
    echo "Slow Queries: $(mysql -u root -p"$DB_PASSWORD" -e "SHOW GLOBAL STATUS LIKE 'Slow_queries';" 2>/dev/null | awk 'NR==2 {print $2}' || echo "N/A")"
}

# Main function
main() {
    local action=${1:-"optimize"}
    
    log "Starting performance optimization process..."
    log "Action: $action"
    
    # Check if running as root
    check_root
    
    case $action in
        "optimize")
            # Optimize PHP
            optimize_php
            
            # Optimize Nginx
            optimize_nginx
            
            # Optimize MySQL
            optimize_mysql
            
            # Optimize Redis
            optimize_redis
            
            # Optimize Laravel
            optimize_laravel
            
            # Configure system limits
            configure_system_limits
            
            # Configure kernel parameters
            configure_kernel_parameters
            
            # Configure log rotation
            configure_log_rotation
            
            # Create performance monitor
            create_performance_monitor
            
            # Test performance
            test_performance
            
            log "Performance optimization completed successfully"
            ;;
        "test")
            # Test performance
            test_performance
            ;;
        "status")
            # Show performance status
            show_performance_status
            ;;
        "monitor")
            # Run performance monitor
            /usr/local/bin/performance-monitor.sh
            ;;
        *)
            log "ERROR: Unknown action: $action"
            log "Available actions: optimize, test, status, monitor"
            exit 1
            ;;
    esac
}

# Error handling
trap 'log "ERROR: Performance optimization failed at line $LINENO"; exit 1' ERR

# Run main function
main "$@"
