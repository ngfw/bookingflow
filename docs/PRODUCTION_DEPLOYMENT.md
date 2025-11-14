# BookingFlow - Production Deployment Guide

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Server Setup](#server-setup)
3. [Application Deployment](#application-deployment)
4. [Database Configuration](#database-configuration)
5. [Environment Configuration](#environment-configuration)
6. [Security Hardening](#security-hardening)
7. [Performance Optimization](#performance-optimization)
8. [Monitoring & Logging](#monitoring--logging)
9. [Backup Configuration](#backup-configuration)
10. [Post-Deployment Testing](#post-deployment-testing)
11. [Automated Deployment (GitHub Actions)](#automated-deployment-github-actions)
12. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Server Requirements

- **OS**: Ubuntu 22.04 LTS or higher
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **PHP**: 8.2 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Cache**: Redis 6.0+
- **Node.js**: 18+ (for asset compilation)
- **Composer**: 2.x
- **SSL Certificate**: Let's Encrypt or commercial

### Minimum Hardware Specifications

- **CPU**: 2 cores minimum (4 cores recommended)
- **RAM**: 4GB minimum (8GB+ recommended)
- **Storage**: 50GB SSD minimum
- **Bandwidth**: 100Mbps

---

## Server Setup

### 1. Update System Packages

```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Install Required Software

```bash
# Install PHP and extensions
sudo apt install -y php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath \
    php8.2-curl php8.2-gd php8.2-zip php8.2-redis php8.2-intl

# Install MySQL
sudo apt install -y mysql-server

# Install Redis
sudo apt install -y redis-server

# Install Nginx
sudo apt install -y nginx

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Configure MySQL

```bash
sudo mysql_secure_installation
```

Create database and user:

```sql
CREATE DATABASE bookingflow_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'salon_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON bookingflow_production.* TO 'salon_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Configure PHP-FPM

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M
max_execution_time = 300
date.timezone = UTC
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

### 5. Configure Nginx

Create site configuration `/etc/nginx/sites-available/salon`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/salon/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/salon /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## Application Deployment

### 1. Clone Repository

```bash
sudo mkdir -p /var/www/salon
cd /var/www/salon
sudo git clone <your-repository-url> .
```

### 2. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/salon
sudo find /var/www/salon -type f -exec chmod 644 {} \;
sudo find /var/www/salon -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/salon/storage
sudo chmod -R 775 /var/www/salon/bootstrap/cache
```

### 3. Install Dependencies

```bash
cd /var/www/salon
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 4. Environment Configuration

```bash
cp .env.production.example .env
php artisan key:generate
```

Edit `.env` with production values (see [Environment Configuration](#environment-configuration)).

### 5. Database Migration

```bash
php artisan migrate --force
php artisan db:seed --force  # If needed
```

### 6. Cache Optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 7. Storage Link

```bash
php artisan storage:link
```

---

## Environment Configuration

Key environment variables for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=bookingflow_production
DB_USERNAME=salon_user
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password

# Twilio for SMS
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=+1234567890

# Error tracking
SENTRY_LARAVEL_DSN=your_sentry_dsn

# Payment gateway
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

---

## Security Hardening

### 1. Firewall Configuration

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 2. Fail2Ban

```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. Disable Debug Mode

Ensure `.env` has:

```env
APP_DEBUG=false
```

### 4. Secure File Permissions

```bash
sudo find /var/www/salon -type f -exec chmod 644 {} \;
sudo find /var/www/salon -type d -exec chmod 755 {} \;
sudo chmod 600 /var/www/salon/.env
```

### 5. MySQL Security

```bash
sudo mysql_secure_installation
```

- Remove anonymous users
- Disallow root login remotely
- Remove test database

---

## Performance Optimization

### 1. Enable OPcache

Verify OPcache is enabled in `php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### 2. Redis Configuration

Edit `/etc/redis/redis.conf`:

```conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

Restart Redis:

```bash
sudo systemctl restart redis-server
```

### 3. Queue Workers

Create systemd service `/etc/systemd/system/salon-worker.service`:

```ini
[Unit]
Description=Salon Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/salon
ExecStart=/usr/bin/php /var/www/salon/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl enable salon-worker
sudo systemctl start salon-worker
```

### 4. Task Scheduler

Add to crontab:

```bash
sudo crontab -e -u www-data
```

Add:

```cron
* * * * * cd /var/www/salon && php artisan schedule:run >> /dev/null 2>&1
```

---

## Monitoring & Logging

### 1. Log Rotation

Create `/etc/logrotate.d/salon`:

```
/var/www/salon/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    missingok
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 2. Application Monitoring

Install Laravel Telescope (optional, development only):

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 3. Error Tracking (Sentry)

Already configured via `.env`. Verify with:

```bash
php artisan tinker
>>> app(\Sentry\Laravel\Integration::class)->captureMessage('Test message');
```

---

## Backup Configuration

### 1. Automated Database Backups

Create backup script `/usr/local/bin/salon-backup.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/salon"
DB_NAME="bookingflow_production"
DB_USER="salon_user"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Application backup
tar -czf $BACKUP_DIR/app_$DATE.tar.gz /var/www/salon

# Remove backups older than 30 days
find $BACKUP_DIR -type f -mtime +30 -delete
```

Make executable:

```bash
sudo chmod +x /usr/local/bin/salon-backup.sh
```

Add to cron:

```bash
0 2 * * * /usr/local/bin/salon-backup.sh
```

### 2. Laravel Backup Package

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
php artisan backup:run
```

---

## Post-Deployment Testing

### 1. Verify Application

```bash
curl -I https://yourdomain.com
```

Should return 200 OK.

### 2. Test Key Features

- [ ] Homepage loads
- [ ] Login works
- [ ] Book appointment
- [ ] Admin dashboard accessible
- [ ] POS system functional
- [ ] Reports generate
- [ ] Email notifications send
- [ ] SMS notifications send (if configured)

### 3. Run Tests

```bash
php artisan test
```

### 4. Performance Test

```bash
ab -n 100 -c 10 https://yourdomain.com/
```


---

## Automated Deployment (GitHub Actions)

### Overview

The project includes an automated deployment workflow that deploys on version tag pushes. This is the recommended method for production deployments.

### Setup GitHub Secrets

Before using automated deployment, configure these secrets in your GitHub repository:

**Settings → Secrets and variables → Actions → New repository secret**

| Secret Name | Description | Example |
|------------|-------------|---------|
| `SERVER_HOST` | Your production server IP or domain | `203.0.113.42` or `yourdomain.com` |
| `SERVER_USER` | SSH username (usually `ubuntu` or `www-data`) | `ubuntu` |
| `SERVER_SSH_KEY` | Private SSH key for authentication | Contents of `~/.ssh/id_rsa` |

### Generate SSH Key Pair (if needed)

On your local machine:

```bash
# Generate SSH key pair
ssh-keygen -t rsa -b 4096 -C "deployment@yourdomain.com" -f ~/.ssh/salon_deploy_key

# Copy public key to server
ssh-copy-id -i ~/.ssh/salon_deploy_key.pub ubuntu@your-server-ip

# Test connection
ssh -i ~/.ssh/salon_deploy_key ubuntu@your-server-ip

# Add private key to GitHub Secrets
cat ~/.ssh/salon_deploy_key
# Copy the entire output and paste into SERVER_SSH_KEY secret
```

### Deployment Workflow

The automated deployment (`.github/workflows/deploy.yml`) performs these steps:

1. **Checkout Code** - Fetches the tagged version
2. **SSH to Server** - Connects to production server
3. **Fetch & Checkout Tag** - Downloads and switches to the tagged version
4. **Install Dependencies** - Runs `composer install` (production mode)
5. **Build Assets** - Runs `npm install` and `npm run build`
6. **Run Migrations** - Applies database changes (if `.env` exists)
7. **Clear Caches** - Clears Laravel caches and optimizes
8. **Set Permissions** - Ensures correct file permissions
9. **Reload PHP-FPM** - Applies changes

### How to Deploy

#### 1. Create a Version Tag

```bash
# Ensure all changes are committed
git add .
git commit -m "Prepare release v1.0.0"

# Create and push tag
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

#### 2. Monitor Deployment

- Go to GitHub → Actions tab
- Watch the deployment workflow run
- Check for any errors

#### 3. Verify Deployment

```bash
# SSH to server
ssh ubuntu@your-server-ip

# Check deployed version
cd /var/www/salon
git describe --tags

# Check application
curl -I https://yourdomain.com
```

### Deployment Configuration

The workflow expects:

- **Application Directory**: `/var/www/salon` (customize in `deploy.yml` if different)
- **Web Server User**: `www-data` (Ubuntu/Nginx default)
- **PHP Version**: 8.2 (adjust in workflow if using different version)

### Customize Deployment Path

If your application is in a different directory, edit `.github/workflows/deploy.yml`:

```yaml
script: |
  APP_DIR=/your/custom/path  # Change this line
  cd "$APP_DIR" || exit 1
  ...
```

### Rollback to Previous Version

If a deployment fails:

```bash
# SSH to server
ssh ubuntu@your-server-ip
cd /var/www/salon

# List available tags
git tag -l

# Rollback to previous tag
git fetch --all --tags
git checkout v1.0.0  # Replace with previous version

# Reinstall dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Clear caches
php artisan optimize

# Set permissions
sudo chown -R www-data:www-data /var/www/salon
sudo chmod -R 775 /var/www/salon/storage
```

### Manual Deployment (Alternative)

If you prefer manual deployment without GitHub Actions:

```bash
# SSH to server
ssh ubuntu@your-server-ip
cd /var/www/salon

# Pull latest changes
git fetch --all
git checkout main  # or your branch
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

# Set permissions
sudo chown -R www-data:www-data /var/www/salon
sudo find /var/www/salon -type f -exec chmod 664 {} \;
sudo find /var/www/salon -type d -exec chmod 775 {} \;
sudo chmod -R ug+rwx /var/www/salon/storage
sudo chmod -R ug+rwx /var/www/salon/bootstrap/cache

# Reload PHP-FPM
sudo systemctl reload php8.2-fpm
```

### Zero-Downtime Deployment (Advanced)

For mission-critical deployments with zero downtime:

1. **Use Laravel Envoyer or Deployer** - Professional deployment tools
2. **Implement Blue-Green Deployment** - Two identical environments
3. **Use Symlink Strategy** - Point web server to different releases

Example symlink strategy:

```bash
/var/www/
├── salon-releases/
│   ├── 20241215-120000/
│   ├── 20241215-130000/
│   └── 20241215-140000/
├── salon -> salon-releases/20241215-140000  # Symlink
└── salon-shared/
    ├── .env
    └── storage/
```

### Deployment Best Practices

- ✅ **Always tag releases** - Use semantic versioning (v1.0.0, v1.1.0)
- ✅ **Test in staging first** - Deploy to staging before production
- ✅ **Run tests before deploying** - CI/CD should pass all tests
- ✅ **Keep rollback option ready** - Know how to revert quickly
- ✅ **Monitor after deployment** - Watch logs and error tracking
- ✅ **Deploy during low traffic** - Minimize user impact
- ✅ **Communicate with team** - Notify stakeholders of deployments
- ✅ **Document changes** - Maintain a changelog

### Deployment Checklist

Before deploying:

- [ ] All tests pass (`php artisan test`)
- [ ] Code reviewed and approved
- [ ] Database migrations tested
- [ ] `.env.production.example` updated if needed
- [ ] Changelog updated
- [ ] Team notified
- [ ] Backup created
- [ ] Staging deployment successful

After deploying:

- [ ] Application accessible
- [ ] No errors in logs
- [ ] Database migrations applied
- [ ] Caches cleared
- [ ] Critical features tested
- [ ] Performance acceptable
- [ ] Monitoring shows healthy status

---

## Troubleshooting

### 500 Internal Server Error

Check logs:

```bash
tail -f /var/www/salon/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

Clear cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Connection Issues

Test connection:

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### Permission Issues

Reset permissions:

```bash
sudo chown -R www-data:www-data /var/www/salon
sudo chmod -R 775 /var/www/salon/storage
sudo chmod -R 775 /var/www/salon/bootstrap/cache
```

### Queue Not Processing

Check worker status:

```bash
sudo systemctl status salon-worker
sudo systemctl restart salon-worker
```

View queue logs:

```bash
tail -f /var/www/salon/storage/logs/laravel.log | grep queue
```

---

## Maintenance Mode

Enable:

```bash
php artisan down --message="Scheduled maintenance" --retry=60
```

Disable:

```bash
php artisan up
```

---

## Rollback Procedure

If deployment fails:

```bash
# 1. Restore database backup
gunzip < /var/backups/salon/db_TIMESTAMP.sql.gz | mysql -u salon_user -p bookingflow_production

# 2. Restore application
tar -xzf /var/backups/salon/app_TIMESTAMP.tar.gz -C /

# 3. Clear cache
php artisan cache:clear
php artisan config:clear

# 4. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## Support

For production issues:
- Check logs: `/var/www/salon/storage/logs/`
- Review documentation: `/var/www/salon/docs/`
- Contact development team

---

**Last Updated**: December 2024
**Version**: 1.0.0
