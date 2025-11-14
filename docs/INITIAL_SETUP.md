# Initial Server Setup Guide

**⚠️ IMPORTANT**: Run this setup ONCE before using automated deployments.

This guide walks you through the one-time initial setup required before GitHub Actions automated deployments will work.

---

## 🎯 Overview

The automated deployment workflow (`.github/workflows/deploy.yml`) assumes:
- Application directory exists at `/var/www/book.vai.me`
- Git repository is cloned
- `.env` file is configured
- Database is set up
- Web server is configured

This guide helps you prepare your server for the first time.

---

## 📋 Prerequisites

Before starting, ensure you have:

- [ ] Ubuntu 22.04 LTS server (or similar)
- [ ] SSH access with sudo privileges
- [ ] Domain name pointing to your server (optional but recommended)
- [ ] Server meets minimum requirements:
  - 2 CPU cores, 4GB RAM, 50GB storage
  - PHP 8.2+, MySQL 8.0+, Redis, Nginx/Apache

---

## 🚀 Quick Start (Automated)

### Option A: Use the Setup Script

The easiest method is to use our automated setup script:

```bash
# SSH to your server
ssh ubuntu@your-server-ip

# Download the setup script
wget https://raw.githubusercontent.com/your-username/bookingflow/main/scripts/initial-setup.sh

# Make it executable
chmod +x initial-setup.sh

# Edit the script to set your repository URL
nano initial-setup.sh
# Change: REPO_URL="https://github.com/your-username/bookingflow.git"

# Run the script
bash initial-setup.sh
```

The script will:
1. Create `/var/www/book.vai.me` directory
2. Clone your repository
3. Install Composer dependencies
4. Install NPM packages and build assets
5. Create and configure `.env` file
6. Generate application key
7. Run database migrations
8. Seed database (optional)
9. Create storage link
10. Optimize and cache configuration
11. Set correct file permissions

**After the script completes, skip to [Post-Setup Configuration](#post-setup-configuration)**

---

## 🔧 Manual Setup (Step-by-Step)

If you prefer manual installation or the script fails, follow these steps:

### Step 1: Install Required Software

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install -y php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath \
    php8.2-curl php8.2-gd php8.2-zip php8.2-redis php8.2-intl

# Install MySQL
sudo apt install -y mysql-server

# Install Redis
sudo apt install -y redis-server

# Install Nginx
sudo apt install -y nginx

# Install Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verify installations
php -v
mysql --version
redis-cli --version
nginx -v
node -v
npm -v
composer --version
```

### Step 2: Configure MySQL

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

In MySQL console:

```sql
CREATE DATABASE beauty_salon_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'salon_user'@'localhost' IDENTIFIED BY 'your_strong_password_here';
GRANT ALL PRIVILEGES ON beauty_salon_production.* TO 'salon_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Create Application Directory

```bash
# Create directory
sudo mkdir -p /var/www/book.vai.me

# Set ownership
sudo chown ubuntu:www-data /var/www/book.vai.me

# Navigate to directory
cd /var/www/book.vai.me
```

### Step 4: Clone Repository

```bash
# Clone your repository
git clone https://github.com/your-username/bookingflow.git /var/www/book.vai.me

# Or if already in the directory:
git clone https://github.com/your-username/bookingflow.git .

# Checkout main branch
git checkout main
```

### Step 5: Install Dependencies

```bash
cd /var/www/book.vai.me

# Install Composer dependencies (production mode)
composer install --no-dev --optimize-autoloader

# Install NPM packages
npm install

# Build assets
npm run build
```

### Step 6: Configure Environment

```bash
# Copy environment file
cp .env.production.example .env

# Or if .env.production.example doesn't exist:
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit environment file
nano .env
```

**Required .env settings**:

```env
APP_NAME="Your Salon Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=beauty_salon_production
DB_USERNAME=salon_user
DB_PASSWORD=your_strong_password_here

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Twilio (for SMS notifications)
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=+1234567890

# Stripe (if using payments)
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

### Step 7: Run Database Migrations

```bash
# Run migrations
php artisan migrate --force

# Seed database (optional - adds sample data)
php artisan db:seed --force
```

### Step 8: Create Storage Link

```bash
php artisan storage:link
```

### Step 9: Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache
```

### Step 10: Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/book.vai.me

# Set file permissions
sudo find /var/www/book.vai.me -type f -exec chmod 644 {} \;
sudo find /var/www/book.vai.me -type d -exec chmod 755 {} \;

# Fix node_modules executables
sudo chmod +x /var/www/book.vai.me/node_modules/.bin/*

# Set storage and cache permissions
sudo chgrp -R www-data /var/www/book.vai.me/storage /var/www/book.vai.me/bootstrap/cache
sudo chmod -R ug+rwx /var/www/book.vai.me/storage /var/www/book.vai.me/bootstrap/cache

# Secure .env file
sudo chmod 600 /var/www/book.vai.me/.env
```

---

## 🌐 Post-Setup Configuration

### Configure Nginx

Create Nginx site configuration:

```bash
sudo nano /etc/nginx/sites-available/salon
```

Add configuration:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/book.vai.me/public;

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
}
```

Enable site and reload Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/salon /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Set Up SSL Certificate

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Verify auto-renewal
sudo certbot renew --dry-run
```

### Configure Queue Worker

Create systemd service:

```bash
sudo nano /etc/systemd/system/salon-worker.service
```

Add:

```ini
[Unit]
Description=Salon Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/book.vai.me
ExecStart=/usr/bin/php /var/www/book.vai.me/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl enable salon-worker
sudo systemctl start salon-worker
sudo systemctl status salon-worker
```

### Set Up Task Scheduler

```bash
# Edit crontab for www-data user
sudo crontab -e -u www-data
```

Add:

```cron
* * * * * cd /var/www/book.vai.me && php artisan schedule:run >> /dev/null 2>&1
```

### Configure Automated Backups

```bash
# Create backup directory
sudo mkdir -p /var/backups/salon

# Create backup script
sudo nano /usr/local/bin/salon-backup.sh
```

Add the backup script (see `docs/PRODUCTION_DEPLOYMENT.md` for full script).

Make executable and add to cron:

```bash
sudo chmod +x /usr/local/bin/salon-backup.sh
sudo crontab -e
```

Add:

```cron
0 2 * * * /usr/local/bin/salon-backup.sh
```

---

## ✅ Verify Installation

Test the installation:

```bash
# Check application
curl -I http://your-server-ip
# Should return 200 OK

# Check specific pages
curl http://your-server-ip/
curl http://your-server-ip/login

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Check queue worker
sudo systemctl status salon-worker

# Check scheduler
cd /var/www/book.vai.me && php artisan schedule:list
```

### Browser Testing

Open in browser:
- Homepage: `https://yourdomain.com`
- Login: `https://yourdomain.com/login`
- Book: `https://yourdomain.com/book`
- Admin: `https://yourdomain.com/admin/dashboard`

### Create Admin User

If needed:

```bash
php artisan tinker
```

```php
$user = new App\Models\User();
$user->name = 'Admin User';
$user->email = 'admin@yourdomain.com';
$user->password = bcrypt('your-secure-password');
$user->is_active = true;
$user->save();

$user->assignRole('admin');  // or 'super-admin'
exit
```

---

## 🚀 Enable Automated Deployments

Now that initial setup is complete, configure GitHub Actions for automated deployments:

### 1. Generate SSH Key for Deployment

On your local machine:

```bash
ssh-keygen -t rsa -b 4096 -C "deployment@yourdomain.com" -f ~/.ssh/salon_deploy_key
```

### 2. Add Public Key to Server

```bash
ssh-copy-id -i ~/.ssh/salon_deploy_key.pub ubuntu@your-server-ip

# Test connection
ssh -i ~/.ssh/salon_deploy_key ubuntu@your-server-ip
```

### 3. Add GitHub Secrets

Go to: **GitHub Repo → Settings → Secrets and variables → Actions**

Add these secrets:

| Secret Name | Value |
|------------|-------|
| `SERVER_HOST` | Your server IP or domain |
| `SERVER_USER` | `ubuntu` (or your SSH user) |
| `SERVER_SSH_KEY` | Contents of `~/.ssh/salon_deploy_key` (private key) |

### 4. Test Automated Deployment

```bash
# Create and push a tag
git tag -a v1.0.0 -m "Initial production release"
git push origin v1.0.0

# Watch deployment in GitHub Actions
# github.com/your-username/bookingflow/actions
```

---

## 📋 Post-Deployment Checklist

After setup is complete:

- [ ] Application loads in browser
- [ ] Login works
- [ ] Admin dashboard accessible
- [ ] Booking form works
- [ ] Email notifications send
- [ ] SMS notifications send (if configured)
- [ ] Queue worker is running
- [ ] Scheduler is configured
- [ ] Backups are configured
- [ ] SSL certificate is active
- [ ] Automated deployment works

---

## 🐛 Troubleshooting

### Application Shows 500 Error

```bash
# Check logs
tail -f /var/www/book.vai.me/storage/logs/laravel.log

# Check permissions
sudo chmod -R 775 /var/www/book.vai.me/storage
sudo chmod -R 775 /var/www/book.vai.me/bootstrap/cache
```

### Database Connection Fails

```bash
# Test database connection
mysql -u salon_user -p beauty_salon_production

# Check .env database credentials
nano /var/www/book.vai.me/.env
```

### Assets Not Loading

```bash
# Rebuild assets
cd /var/www/book.vai.me
npm run build

# Clear cache
php artisan cache:clear
php artisan view:clear
```

### Queue Jobs Not Processing

```bash
# Check worker status
sudo systemctl status salon-worker

# Restart worker
sudo systemctl restart salon-worker

# Check logs
tail -f /var/www/book.vai.me/storage/logs/laravel.log
```

---

## 📚 Additional Resources

- [Full Deployment Guide](PRODUCTION_DEPLOYMENT.md)
- [Production Checklist](../PRODUCTION_CHECKLIST.md)
- [Test Execution Guide](../tests/Feature/CriticalPath/README.md)
- [User Guide](USER_GUIDE.md)
- [Admin Manual](ADMIN_MANUAL.md)

---

## 🆘 Getting Help

If you encounter issues:
1. Check the [Troubleshooting Guide](TROUBLESHOOTING.md)
2. Review the [FAQ](FAQ.md)
3. Check application logs: `/var/www/book.vai.me/storage/logs/laravel.log`
4. Check web server logs: `/var/log/nginx/error.log`

---

**Last Updated**: December 2024
**Version**: 1.0.0
