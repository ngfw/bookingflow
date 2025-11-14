#!/bin/bash

# BookingFlow - SSL Setup Script
# This script configures SSL certificates and security settings

set -e

# Configuration
DOMAIN="bookingflow.com"
EMAIL="admin@bookingflow.com"
SSL_DIR="/etc/nginx/ssl"
NGINX_CONF="/etc/nginx/conf.d/default.conf"
APP_DIR="/var/www/beauty-salon"

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "/var/log/ssl-setup.log"
}

# Function to check if running as root
check_root() {
    if [ "$EUID" -ne 0 ]; then
        log "ERROR: This script must be run as root"
        exit 1
    fi
}

# Function to install Certbot
install_certbot() {
    log "Installing Certbot..."
    
    # Update package list
    apt-get update
    
    # Install Certbot and Nginx plugin
    apt-get install -y certbot python3-certbot-nginx
    
    log "Certbot installed successfully"
}

# Function to generate self-signed certificate
generate_self_signed() {
    log "Generating self-signed SSL certificate..."
    
    # Create SSL directory
    mkdir -p "$SSL_DIR"
    
    # Generate private key
    openssl genrsa -out "$SSL_DIR/private.key" 2048
    
    # Generate certificate signing request
    openssl req -new -key "$SSL_DIR/private.key" -out "$SSL_DIR/cert.csr" \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=$DOMAIN"
    
    # Generate self-signed certificate
    openssl x509 -req -days 365 -in "$SSL_DIR/cert.csr" \
        -signkey "$SSL_DIR/private.key" -out "$SSL_DIR/cert.pem"
    
    # Set permissions
    chmod 600 "$SSL_DIR/private.key"
    chmod 644 "$SSL_DIR/cert.pem"
    
    # Clean up CSR
    rm "$SSL_DIR/cert.csr"
    
    log "Self-signed certificate generated successfully"
}

# Function to obtain Let's Encrypt certificate
obtain_letsencrypt() {
    log "Obtaining Let's Encrypt certificate..."
    
    # Stop nginx temporarily
    systemctl stop nginx
    
    # Obtain certificate
    certbot certonly --standalone \
        --email "$EMAIL" \
        --agree-tos \
        --no-eff-email \
        --domains "$DOMAIN" \
        --non-interactive
    
    # Create symlinks
    ln -sf "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" "$SSL_DIR/cert.pem"
    ln -sf "/etc/letsencrypt/live/$DOMAIN/privkey.pem" "$SSL_DIR/private.key"
    
    # Start nginx
    systemctl start nginx
    
    log "Let's Encrypt certificate obtained successfully"
}

# Function to configure Nginx for SSL
configure_nginx_ssl() {
    log "Configuring Nginx for SSL..."
    
    # Backup original configuration
    cp "$NGINX_CONF" "$NGINX_CONF.backup"
    
    # Create SSL configuration
    cat > "$NGINX_CONF" << EOF
# HTTP to HTTPS redirect
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    return 301 https://\$server_name\$request_uri;
}

# HTTPS configuration
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DOMAIN www.$DOMAIN;
    root $APP_DIR/public;
    index index.php index.html index.htm;

    # SSL configuration
    ssl_certificate $SSL_DIR/cert.pem;
    ssl_certificate_key $SSL_DIR/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;

    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;

    # Rate limiting
    limit_req_zone \$binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone \$binary_remote_addr zone=api:10m rate=10r/s;

    # Handle Laravel routes
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Handle PHP files
    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        
        # Security
        fastcgi_param HTTP_PROXY "";
        fastcgi_read_timeout 300;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Deny access to sensitive files
    location ~ /(\.env|\.git|composer\.(json|lock)|package\.(json|lock)|yarn\.lock|webpack\.mix\.js)\$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Rate limiting for login
    location /login {
        limit_req zone=login burst=5 nodelay;
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Rate limiting for API
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Health check endpoint
    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }

    # Error pages
    error_page 404 /404.html;
    error_page 500 502 503 504 /50x.html;
    
    location = /50x.html {
        root /usr/share/nginx/html;
    }
}
EOF
    
    # Test nginx configuration
    nginx -t
    
    # Reload nginx
    systemctl reload nginx
    
    log "Nginx SSL configuration completed"
}

# Function to configure SSL renewal
configure_ssl_renewal() {
    log "Configuring SSL certificate renewal..."
    
    # Create renewal script
    cat > "/usr/local/bin/ssl-renewal.sh" << 'EOF'
#!/bin/bash

# SSL Certificate Renewal Script
LOG_FILE="/var/log/ssl-renewal.log"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "Starting SSL certificate renewal check..."

# Check if certificate needs renewal (within 30 days)
if certbot certificates | grep -q "VALID: 30 days"; then
    log "Certificate needs renewal"
    
    # Renew certificate
    if certbot renew --quiet; then
        log "Certificate renewed successfully"
        
        # Reload nginx
        systemctl reload nginx
        log "Nginx reloaded"
        
        # Send notification
        if command -v mail >/dev/null 2>&1; then
            echo "SSL certificate renewed successfully for $DOMAIN" | mail -s "SSL Renewal Success" admin@bookingflow.com
        fi
    else
        log "ERROR: Certificate renewal failed"
        
        # Send error notification
        if command -v mail >/dev/null 2>&1; then
            echo "SSL certificate renewal failed for $DOMAIN" | mail -s "SSL Renewal Failed" admin@bookingflow.com
        fi
    fi
else
    log "Certificate does not need renewal"
fi

log "SSL certificate renewal check completed"
EOF
    
    # Make script executable
    chmod +x "/usr/local/bin/ssl-renewal.sh"
    
    # Add to crontab (run daily at 2 AM)
    (crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/ssl-renewal.sh") | crontab -
    
    log "SSL renewal configuration completed"
}

# Function to configure firewall
configure_firewall() {
    log "Configuring firewall..."
    
    # Install ufw if not present
    if ! command -v ufw >/dev/null 2>&1; then
        apt-get install -y ufw
    fi
    
    # Reset firewall rules
    ufw --force reset
    
    # Set default policies
    ufw default deny incoming
    ufw default allow outgoing
    
    # Allow SSH
    ufw allow ssh
    
    # Allow HTTP and HTTPS
    ufw allow 80/tcp
    ufw allow 443/tcp
    
    # Allow MySQL (only from localhost)
    ufw allow from 127.0.0.1 to any port 3306
    
    # Allow Redis (only from localhost)
    ufw allow from 127.0.0.1 to any port 6379
    
    # Enable firewall
    ufw --force enable
    
    log "Firewall configured successfully"
}

# Function to configure fail2ban
configure_fail2ban() {
    log "Configuring fail2ban..."
    
    # Install fail2ban if not present
    if ! command -v fail2ban-client >/dev/null 2>&1; then
        apt-get install -y fail2ban
    fi
    
    # Create fail2ban configuration
    cat > "/etc/fail2ban/jail.local" << EOF
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5
backend = systemd

[sshd]
enabled = true
port = ssh
logpath = /var/log/auth.log
maxretry = 3

[nginx-http-auth]
enabled = true
filter = nginx-http-auth
port = http,https
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
filter = nginx-limit-req
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 10

[nginx-botsearch]
enabled = true
filter = nginx-botsearch
port = http,https
logpath = /var/log/nginx/access.log
maxretry = 2

[php-url-fopen]
enabled = true
filter = php-url-fopen
port = http,https
logpath = /var/log/nginx/access.log
maxretry = 1
EOF
    
    # Create nginx filters
    cat > "/etc/fail2ban/filter.d/nginx-http-auth.conf" << 'EOF'
[Definition]
failregex = ^ \[error\] \d+#\d+: \*\d+ user "(?:[^"]+|.*?)":? (?:password mismatch|was not found in "[^"]*"), client: <HOST>, server: \S+, request: "\S+ \S+ HTTP/\d+\.\d+", host: "\S+"(?:, referrer: "\S+")?$
            ^ \[error\] \d+#\d+: \*\d+ no user/password was provided for basic authentication, client: <HOST>, server: \S+, request: "\S+ \S+ HTTP/\d+\.\d+", host: "\S+"(?:, referrer: "\S+")?$
ignoreregex =
EOF
    
    cat > "/etc/fail2ban/filter.d/nginx-limit-req.conf" << 'EOF'
[Definition]
failregex = limiting requests, excess: .* by zone .*, client: <HOST>
ignoreregex =
EOF
    
    cat > "/etc/fail2ban/filter.d/nginx-botsearch.conf" << 'EOF'
[Definition]
failregex = ^<HOST> -.*"(GET|POST).*HTTP.*" (404|444|403|400) .*$
ignoreregex =
EOF
    
    cat > "/etc/fail2ban/filter.d/php-url-fopen.conf" << 'EOF'
[Definition]
failregex = ^<HOST> -.*"(GET|POST).*\.php.*HTTP.*" (200|404) .*$
ignoreregex =
EOF
    
    # Start and enable fail2ban
    systemctl start fail2ban
    systemctl enable fail2ban
    
    log "Fail2ban configured successfully"
}

# Function to configure security headers
configure_security_headers() {
    log "Configuring security headers..."
    
    # Create security headers configuration
    cat > "/etc/nginx/conf.d/security-headers.conf" << 'EOF'
# Security Headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;

# Hide server information
server_tokens off;
more_clear_headers 'Server';
more_clear_headers 'X-Powered-By';

# Prevent access to sensitive files
location ~ /\. {
    deny all;
    access_log off;
    log_not_found off;
}

location ~ /(\.env|\.git|composer\.(json|lock)|package\.(json|lock)|yarn\.lock|webpack\.mix\.js)$ {
    deny all;
    access_log off;
    log_not_found off;
}
EOF
    
    # Reload nginx
    systemctl reload nginx
    
    log "Security headers configured"
}

# Function to test SSL configuration
test_ssl() {
    log "Testing SSL configuration..."
    
    # Test SSL certificate
    if openssl s_client -connect "$DOMAIN:443" -servername "$DOMAIN" </dev/null 2>/dev/null | grep -q "Verify return code: 0"; then
        log "✓ SSL certificate is valid"
    else
        log "✗ SSL certificate validation failed"
        return 1
    fi
    
    # Test HTTPS redirect
    if curl -s -o /dev/null -w "%{http_code}" "http://$DOMAIN" | grep -q "301"; then
        log "✓ HTTP to HTTPS redirect is working"
    else
        log "✗ HTTP to HTTPS redirect failed"
        return 1
    fi
    
    # Test security headers
    local headers=$(curl -s -I "https://$DOMAIN")
    if echo "$headers" | grep -q "Strict-Transport-Security"; then
        log "✓ HSTS header is present"
    else
        log "✗ HSTS header is missing"
    fi
    
    if echo "$headers" | grep -q "X-Frame-Options"; then
        log "✓ X-Frame-Options header is present"
    else
        log "✗ X-Frame-Options header is missing"
    fi
    
    log "SSL configuration test completed"
}

# Function to show SSL status
show_ssl_status() {
    log "SSL Configuration Status:"
    echo ""
    echo "Domain: $DOMAIN"
    echo "SSL Certificate: $SSL_DIR/cert.pem"
    echo "Private Key: $SSL_DIR/private.key"
    echo ""
    echo "Certificate Details:"
    openssl x509 -in "$SSL_DIR/cert.pem" -text -noout | grep -E "(Subject:|Issuer:|Not Before:|Not After:)"
    echo ""
    echo "Firewall Status:"
    ufw status
    echo ""
    echo "Fail2ban Status:"
    fail2ban-client status
    echo ""
    echo "SSL Test:"
    test_ssl
}

# Main function
main() {
    local action=${1:-"setup"}
    
    log "Starting SSL setup process..."
    log "Action: $action"
    
    # Check if running as root
    check_root
    
    case $action in
        "setup")
            # Install Certbot
            install_certbot
            
            # Generate self-signed certificate
            generate_self_signed
            
            # Configure Nginx for SSL
            configure_nginx_ssl
            
            # Configure SSL renewal
            configure_ssl_renewal
            
            # Configure firewall
            configure_firewall
            
            # Configure fail2ban
            configure_fail2ban
            
            # Configure security headers
            configure_security_headers
            
            # Test SSL configuration
            test_ssl
            
            log "SSL setup completed successfully"
            ;;
        "letsencrypt")
            # Obtain Let's Encrypt certificate
            obtain_letsencrypt
            
            # Configure Nginx for SSL
            configure_nginx_ssl
            
            # Configure SSL renewal
            configure_ssl_renewal
            
            # Test SSL configuration
            test_ssl
            
            log "Let's Encrypt setup completed successfully"
            ;;
        "renew")
            # Run SSL renewal
            /usr/local/bin/ssl-renewal.sh
            ;;
        "status")
            # Show SSL status
            show_ssl_status
            ;;
        "test")
            # Test SSL configuration
            test_ssl
            ;;
        *)
            log "ERROR: Unknown action: $action"
            log "Available actions: setup, letsencrypt, renew, status, test"
            exit 1
            ;;
    esac
}

# Error handling
trap 'log "ERROR: SSL setup failed at line $LINENO"; exit 1' ERR

# Run main function
main "$@"
