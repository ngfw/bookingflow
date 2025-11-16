# BookingFlow - Deployment Guide

**Version:** 1.0.0
**Last Updated:** 2025-11-16
**Application:** BookingFlow Laravel Application
**Target Server:** bookingflow.gm-sunshine.com

---

## Table of Contents

1. [Overview](#overview)
2. [System Requirements](#system-requirements)
3. [Pre-deployment Checklist](#pre-deployment-checklist)
4. [Initial Server Setup](#initial-server-setup)
5. [Application Deployment](#application-deployment)
6. [Configuration](#configuration)
7. [Database Management](#database-management)
8. [Monitoring & Logging](#monitoring--logging)
9. [Backup & Recovery](#backup--recovery)
10. [Troubleshooting](#troubleshooting)
11. [Rollback Procedures](#rollback-procedures)
12. [Performance Optimization](#performance-optimization)
13. [Security Hardening](#security-hardening)

---

## Overview

BookingFlow is a comprehensive salon management system built with Laravel 11, designed for high-performance production environments. This guide covers deployment, configuration, monitoring, and maintenance procedures.

### Architecture

- **Web Server:** Nginx 1.24+
- **Application:** PHP 8.3-FPM
- **Framework:** Laravel 11
- **Database:** MySQL 8.0 / MariaDB 10.6+
- **Cache/Queue:** Redis 7.0+
- **Frontend:** Vite + TailwindCSS
- **Process Manager:** Supervisor

---

## System Requirements

### Hardware Requirements

**Minimum:**
- CPU: 2 cores
- RAM: 4GB
- Storage: 20GB SSD
- Network: 100 Mbps

**Recommended:**
- CPU: 4+ cores
- RAM: 8GB+
- Storage: 50GB+ SSD
- Network: 1 Gbps

### Software Requirements

**Operating System:**
- Ubuntu 22.04 LTS or 24.04 LTS (recommended)
- Debian 12+
- CentOS 8+ / Rocky Linux 9+

**Required Software:**
```bash
- PHP 8.3+ with extensions:
  - mbstring, xml, ctype, iconv, intl
  - pdo_mysql, gd, zip, bcmath
  - redis, opcache, apcu
- MySQL 8.0+ or MariaDB 10.6+
- Redis 7.0+
- Nginx 1.24+
- Composer 2.7+
- Node.js 20+ and npm
- Git
- Supervisor
```

---

## Pre-deployment Checklist

### Before Deployment

- [ ] Server meets all system requirements
- [ ] DNS records configured and propagated
- [ ] SSL certificate obtained (Let's Encrypt or commercial)
- [ ] Firewall rules configured (ports 80, 443, 22)
- [ ] Database created with proper user credentials
- [ ] Redis instance configured and secured
- [ ] Email service configured (SMTP/SendGrid/SES)
- [ ] Backup strategy defined and tested
- [ ] Monitoring tools installed (optional: New Relic, Sentry)
- [ ] CI/CD secrets configured in GitHub

### GitHub Secrets Required

Configure these secrets in GitHub repository settings:

```
SERVER_HOST=bookingflow.gm-sunshine.com
SERVER_USER=deploy
SERVER_SSH_KEY=<private-ssh-key>
MAINTENANCE_SECRET=<random-secret-for-maintenance-mode>
SLACK_WEBHOOK_URL=<slack-webhook-for-notifications>
```

---

## Initial Server Setup

### 1. Server Preparation

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y \
    nginx \
    mysql-server \
    redis-server \
    supervisor \
    git \
    curl \
    wget \
    unzip \
    software-properties-common

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 and extensions
sudo apt install -y \
    php8.3-fpm \
    php8.3-cli \
    php8.3-mysql \
    php8.3-redis \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-bcmath \
    php8.3-zip \
    php8.3-gd \
    php8.3-curl \
    php8.3-intl \
    php8.3-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Create Application User

```bash
# Create deployment user
sudo adduser --disabled-password --gecos "" deploy

# Add to www-data group
sudo usermod -aG www-data deploy

# Configure SSH access
sudo mkdir -p /home/deploy/.ssh
sudo nano /home/deploy/.ssh/authorized_keys
# Paste your public SSH key

# Set permissions
sudo chown -R deploy:deploy /home/deploy/.ssh
sudo chmod 700 /home/deploy/.ssh
sudo chmod 600 /home/deploy/.ssh/authorized_keys
```

### 3. Configure MySQL

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p

# In MySQL prompt:
CREATE DATABASE bookingflow_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bookingflow_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON bookingflow_production.* TO 'bookingflow_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Configure Redis

```bash
# Edit Redis configuration
sudo nano /etc/redis/redis.conf

# Key settings:
# maxmemory 256mb
# maxmemory-policy allkeys-lru
# bind 127.0.0.1
# protected-mode yes

# Restart Redis
sudo systemctl restart redis-server
sudo systemctl enable redis-server
```

### 5. Configure Nginx

Create Nginx configuration:

```bash
sudo nano /etc/nginx/sites-available/bookingflow
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name bookingflow.gm-sunshine.com;

    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name bookingflow.gm-sunshine.com;

    root /var/www/bookingflow.gm-sunshine.com/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/bookingflow.gm-sunshine.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/bookingflow.gm-sunshine.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Logging
    access_log /var/log/nginx/bookingflow-access.log;
    error_log /var/log/nginx/bookingflow-error.log;

    # File upload size
    client_max_body_size 100M;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;

        # PHP settings
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_read_timeout 600;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/bookingflow /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl enable nginx
```

### 6. Configure PHP-FPM

```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

Key settings:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

```bash
sudo nano /etc/php/8.3/fpm/php.ini
```

Key settings:
```ini
memory_limit = 256M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl enable php8.3-fpm
```

### 7. Configure Supervisor

```bash
sudo nano /etc/supervisor/conf.d/bookingflow.conf
```

```ini
[program:bookingflow-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bookingflow.gm-sunshine.com/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/bookingflow.gm-sunshine.com/storage/logs/queue-worker.log
stopwaitsecs=3600

[program:bookingflow-scheduler]
process_name=%(program_name)s
command=/bin/bash -c "while [ true ]; do (php /var/www/bookingflow.gm-sunshine.com/artisan schedule:run --verbose --no-interaction & ); sleep 60; done"
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/bookingflow.gm-sunshine.com/storage/logs/scheduler.log
```

Reload Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

---

## Application Deployment

### Manual Deployment

```bash
# Switch to application directory
cd /var/www/bookingflow.gm-sunshine.com

# Enable maintenance mode
php artisan down

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --only=production
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Restart services
sudo supervisorctl restart all
sudo systemctl reload php8.3-fpm

# Disable maintenance mode
php artisan up
```

### Automated Deployment via GitHub Actions

**Tag-based deployment:**

```bash
# Create and push a tag
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

The deployment workflow will automatically:
1. Run pre-deployment checks
2. Create backup
3. Deploy application
4. Run post-deployment health checks
5. Send notifications

---

## Configuration

### Environment Configuration

Copy and configure production environment:

```bash
cp .env.production.example .env
nano .env
```

**Critical variables:**

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generate-with-php-artisan-key:generate>
APP_URL=https://bookingflow.gm-sunshine.com

DB_DATABASE=bookingflow_production
DB_USERNAME=bookingflow_user
DB_PASSWORD=<secure-password>

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=<your-smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<your-username>
MAIL_PASSWORD=<your-password>

SENTRY_LARAVEL_DSN=<your-sentry-dsn>
```

### Storage Setup

```bash
# Create storage link
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## Database Management

### Migrations

```bash
# Check migration status
php artisan migrate:status

# Run migrations
php artisan migrate --force

# Rollback last batch
php artisan migrate:rollback

# Reset database (DANGER!)
php artisan migrate:fresh --force
```

### Seeders

```bash
# Seed database
php artisan db:seed --force

# Seed specific seeder
php artisan db:seed --class=UserSeeder --force
```

---

## Monitoring & Logging

### Health Checks

**Application health endpoint:**
```bash
curl https://bookingflow.gm-sunshine.com/health
```

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2025-11-16T12:00:00Z",
  "checks": {
    "database": {"healthy": true, "response_time_ms": 5.2},
    "cache": {"healthy": true, "response_time_ms": 2.1},
    "queue": {"healthy": true, "pending_jobs": 0},
    "storage": {"healthy": true},
    "redis": {"healthy": true, "response_time_ms": 1.8}
  }
}
```

### Log Files

**Application logs:**
```bash
tail -f /var/www/bookingflow.gm-sunshine.com/storage/logs/laravel.log
tail -f /var/www/bookingflow.gm-sunshine.com/storage/logs/performance.log
tail -f /var/www/bookingflow.gm-sunshine.com/storage/logs/security.log
```

**System logs:**
```bash
tail -f /var/log/nginx/bookingflow-access.log
tail -f /var/log/nginx/bookingflow-error.log
tail -f /var/log/php8.3-fpm.log
```

### Error Tracking (Sentry)

1. Sign up at [sentry.io](https://sentry.io)
2. Create a new Laravel project
3. Copy DSN to `.env`:
   ```env
   SENTRY_LARAVEL_DSN=https://xxx@sentry.io/xxx
   ```
4. Test error tracking:
   ```bash
   php artisan sentry:test
   ```

### Performance Monitoring

**Monitor slow requests:**
```bash
grep "Slow request" storage/logs/performance.log
```

**Monitor queue workers:**
```bash
sudo supervisorctl status bookingflow-queue:*
```

**Monitor Redis:**
```bash
redis-cli info
redis-cli monitor
```

---

## Backup & Recovery

### Automated Backups

The backup script is located at `/var/www/bookingflow.gm-sunshine.com/scripts/backup.sh`

**Set up cron job:**
```bash
sudo crontab -e
```

Add:
```cron
# Daily backup at 2 AM
0 2 * * * /var/www/bookingflow.gm-sunshine.com/scripts/backup.sh

# Weekly full backup on Sunday at 3 AM
0 3 * * 0 /var/www/bookingflow.gm-sunshine.com/scripts/backup.sh --keep-local
```

### Manual Backup

```bash
cd /var/www/bookingflow.gm-sunshine.com
./scripts/backup.sh
```

**Options:**
- `--db-only`: Backup database only
- `--files-only`: Backup files only
- `--no-upload`: Skip S3 upload
- `--keep-local`: Keep local backup after S3 upload

### Restore from Backup

**List available backups:**
```bash
./scripts/restore.sh --list
```

**Restore from specific backup:**
```bash
./scripts/restore.sh --date 20251116_020000
```

**Emergency restore:**
```bash
# 1. Stop application
php artisan down

# 2. Restore database
mysql -u bookingflow_user -p bookingflow_production < /var/backups/bookingflow/db_backup_YYYYMMDD_HHMMSS.sql

# 3. Restore files
tar -xzf /var/backups/bookingflow/files_backup_YYYYMMDD_HHMMSS.tar.gz -C /var/www/bookingflow.gm-sunshine.com/

# 4. Set permissions
sudo chown -R www-data:www-data /var/www/bookingflow.gm-sunshine.com
sudo chmod -R 775 storage bootstrap/cache

# 5. Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Restart services
sudo supervisorctl restart all
sudo systemctl reload php8.3-fpm

# 7. Enable application
php artisan up
```

---

## Troubleshooting

### Common Issues

**Issue: 500 Internal Server Error**

```bash
# Check error logs
tail -100 storage/logs/laravel.log
tail -100 /var/log/nginx/bookingflow-error.log

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check permissions
ls -la storage/
ls -la bootstrap/cache/
```

**Issue: Database Connection Failed**

```bash
# Test database connection
mysql -u bookingflow_user -p bookingflow_production

# Check .env configuration
cat .env | grep DB_

# Verify MySQL is running
sudo systemctl status mysql
```

**Issue: Queue Workers Not Processing Jobs**

```bash
# Check supervisor status
sudo supervisorctl status bookingflow-queue:*

# Restart queue workers
sudo supervisorctl restart bookingflow-queue:*

# Check queue status
php artisan queue:work --once --verbose

# Monitor queue
php artisan queue:listen --verbose
```

**Issue: High Memory Usage**

```bash
# Check PHP-FPM processes
ps aux | grep php-fpm

# Check memory usage
free -h

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

**Issue: Slow Page Load Times**

```bash
# Check slow queries
grep "Slow request" storage/logs/performance.log

# Enable query logging temporarily
php artisan db:monitor

# Check opcache status
php -i | grep opcache

# Clear all caches
php artisan optimize:clear
php artisan optimize
```

---

## Rollback Procedures

### Immediate Rollback

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Checkout previous tag
git fetch --all --tags
git checkout tags/v1.0.0  # Previous working version

# 3. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --only=production
npm run build

# 4. Rollback migrations if needed
php artisan migrate:rollback --step=1

# 5. Clear and rebuild cache
php artisan optimize:clear
php artisan optimize

# 6. Restart services
sudo supervisorctl restart all
sudo systemctl reload php8.3-fpm

# 7. Disable maintenance mode
php artisan up
```

### Database Rollback

```bash
# Rollback last migration batch
php artisan migrate:rollback

# Rollback specific number of migrations
php artisan migrate:rollback --step=5

# View migration status
php artisan migrate:status
```

---

## Performance Optimization

### Caching Strategy

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache

# Full optimization
php artisan optimize
```

### Database Optimization

```sql
-- Optimize tables
OPTIMIZE TABLE users, appointments, bookings;

-- Analyze tables
ANALYZE TABLE users, appointments, bookings;

-- Check table status
SHOW TABLE STATUS;
```

### Redis Optimization

```bash
# Monitor Redis
redis-cli monitor

# Check memory usage
redis-cli info memory

# Clear specific cache
redis-cli FLUSHDB

# Set memory limit (in redis.conf)
maxmemory 512mb
maxmemory-policy allkeys-lru
```

---

## Security Hardening

### SSL/TLS Configuration

```bash
# Install Let's Encrypt
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d bookingflow.gm-sunshine.com

# Auto-renewal
sudo certbot renew --dry-run

# Cron for auto-renewal
0 0 * * * certbot renew --quiet
```

### Firewall Configuration

```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Check status
sudo ufw status verbose
```

### Application Security

```bash
# Set secure permissions
find /var/www/bookingflow.gm-sunshine.com -type f -exec chmod 644 {} \;
find /var/www/bookingflow.gm-sunshine.com -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache

# Secure .env file
chmod 600 .env

# Disable directory listing in Nginx
# Already configured in server block
```

---

## Additional Resources

### Useful Commands

```bash
# Check application version
php artisan --version

# List all routes
php artisan route:list

# Clear all caches
php artisan optimize:clear

# Run queue worker in debug mode
php artisan queue:work --verbose

# Test email configuration
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# Generate application key
php artisan key:generate

# Create symbolic link for storage
php artisan storage:link
```

### Maintenance Tasks

**Daily:**
- Monitor error logs
- Check queue workers status
- Review performance metrics

**Weekly:**
- Review backup integrity
- Check disk space usage
- Update dependencies (security patches)

**Monthly:**
- Full security audit
- Performance optimization review
- Database cleanup and optimization
- Review and rotate logs

---

## Support & Documentation

**Documentation:**
- Laravel Documentation: https://laravel.com/docs
- Deployment Guide: This file
- API Documentation: `/docs/api`

**Monitoring:**
- Application: https://bookingflow.gm-sunshine.com/health
- Sentry: https://sentry.io
- Server Monitoring: Configure tools like New Relic or DataDog

**Emergency Contacts:**
- DevOps Team: devops@bookingflow.com
- On-call: +1-XXX-XXX-XXXX

---

**Document Version:** 1.0.0
**Last Review:** 2025-11-16
**Next Review:** 2025-12-16
