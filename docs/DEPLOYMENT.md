# BookingFlow - Deployment Guide

## Overview
This guide provides comprehensive instructions for deploying the BookingFlow to production environments.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Server Requirements](#server-requirements)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [SSL Setup](#ssl-setup)
6. [Performance Optimization](#performance-optimization)
7. [Monitoring Setup](#monitoring-setup)
8. [Backup Configuration](#backup-configuration)
9. [Security Hardening](#security-hardening)
10. [Troubleshooting](#troubleshooting)

## Prerequisites

### System Requirements
- **Operating System**: Ubuntu 20.04 LTS or later
- **CPU**: 2 cores minimum, 4 cores recommended
- **RAM**: 4GB minimum, 8GB recommended
- **Storage**: 50GB minimum, 100GB recommended
- **Network**: Stable internet connection

### Software Requirements
- Docker 20.10+
- Docker Compose 2.0+
- Git
- curl
- wget
- unzip

### Domain and DNS
- Domain name pointing to your server
- SSL certificate (Let's Encrypt recommended)
- DNS records configured

## Server Requirements

### Minimum Requirements
```
CPU: 2 cores
RAM: 4GB
Storage: 50GB SSD
Network: 100Mbps
```

### Recommended Requirements
```
CPU: 4 cores
RAM: 8GB
Storage: 100GB SSD
Network: 1Gbps
```

### Production Requirements
```
CPU: 8 cores
RAM: 16GB
Storage: 200GB SSD
Network: 1Gbps
Load Balancer: Yes
CDN: Recommended
```

## Installation

### 1. Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y curl wget git unzip software-properties-common

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Add user to docker group
sudo usermod -aG docker $USER
```

### 2. Clone Repository

```bash
# Clone the repository
git clone https://github.com/your-username/beauty-salon-management.git
cd beauty-salon-management

# Checkout production branch
git checkout main
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Edit environment file
nano .env
```

### 4. Docker Setup

```bash
# Build and start containers
docker-compose -f docker/docker-compose.yml up -d

# Check container status
docker-compose -f docker/docker-compose.yml ps
```

### 5. Application Setup

```bash
# Install dependencies
docker-compose -f docker/docker-compose.yml exec app composer install --optimize-autoloader --no-dev

# Generate application key
docker-compose -f docker/docker-compose.yml exec app php artisan key:generate

# Run database migrations
docker-compose -f docker/docker-compose.yml exec app php artisan migrate --force

# Seed database
docker-compose -f docker/docker-compose.yml exec app php artisan db:seed --force

# Create storage symlink
docker-compose -f docker/docker-compose.yml exec app php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Configuration

### 1. Environment Variables

Edit the `.env` file with your production settings:

```env
APP_NAME="BookingFlow Management"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=bookingflow
DB_USERNAME=bookingflow_user
DB_PASSWORD=your-secure-password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### 2. Database Configuration

```bash
# Create database user
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p
```

```sql
CREATE DATABASE bookingflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bookingflow_user'@'%' IDENTIFIED BY 'your-secure-password';
GRANT ALL PRIVILEGES ON bookingflow.* TO 'bookingflow_user'@'%';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Nginx Configuration

The Nginx configuration is already optimized in the Docker setup. For custom domains, update the `docker/default.conf` file:

```nginx
server_name yourdomain.com www.yourdomain.com;
```

## SSL Setup

### 1. Automatic SSL with Let's Encrypt

```bash
# Run SSL setup script
sudo ./scripts/ssl-setup.sh letsencrypt
```

### 2. Manual SSL Configuration

```bash
# Generate self-signed certificate
sudo ./scripts/ssl-setup.sh setup

# Or configure Let's Encrypt
sudo ./scripts/ssl-setup.sh letsencrypt
```

### 3. SSL Renewal

The SSL renewal is automatically configured via cron job. To manually renew:

```bash
sudo ./scripts/ssl-setup.sh renew
```

## Performance Optimization

### 1. Run Performance Optimization

```bash
# Run full optimization
sudo ./scripts/performance-optimization.sh optimize

# Test performance
sudo ./scripts/performance-optimization.sh test

# Check status
sudo ./scripts/performance-optimization.sh status
```

### 2. Manual Optimizations

#### PHP Optimization
- OPcache enabled
- Memory limit: 512M
- Max execution time: 300s
- Session handling via Redis

#### Nginx Optimization
- Gzip compression enabled
- Static file caching
- FastCGI caching
- Rate limiting

#### MySQL Optimization
- InnoDB buffer pool optimized
- Query cache enabled
- Slow query logging
- Connection pooling

#### Redis Optimization
- Memory optimization
- Persistence configuration
- Connection limits

## Monitoring Setup

### 1. Start Monitoring Stack

```bash
# Start monitoring services
docker-compose -f docker/monitoring/docker-compose.monitoring.yml up -d
```

### 2. Access Monitoring Tools

- **Grafana**: http://yourdomain.com:3000 (admin/admin123)
- **Prometheus**: http://yourdomain.com:9090
- **Kibana**: http://yourdomain.com:5601
- **Uptime Kuma**: http://yourdomain.com:3001

### 3. Configure Alerts

Edit the alert configuration in `docker/monitoring/alertmanager/alertmanager.yml`:

```yaml
receivers:
  - name: 'critical-alerts'
    email_configs:
      - to: 'admin@yourdomain.com'
        subject: 'CRITICAL: {{ .GroupLabels.alertname }}'
```

## Backup Configuration

### 1. Configure Backup

```bash
# Set backup directory
export BACKUP_DIR="/var/backups/beauty-salon"

# Create backup directory
sudo mkdir -p "$BACKUP_DIR"

# Set permissions
sudo chown -R www-data:www-data "$BACKUP_DIR"
```

### 2. Schedule Backups

```bash
# Add to crontab
sudo crontab -e

# Add backup schedule (daily at 2 AM)
0 2 * * * /var/www/beauty-salon/scripts/backup.sh
```

### 3. Test Backup and Restore

```bash
# Test backup
sudo ./scripts/backup.sh

# Test restore
sudo ./scripts/restore.sh --date 20241220_143000
```

## Security Hardening

### 1. Firewall Configuration

```bash
# Configure firewall
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 3306/tcp
sudo ufw deny 6379/tcp
```

### 2. Fail2ban Setup

```bash
# Install fail2ban
sudo apt install -y fail2ban

# Configure fail2ban
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. Security Headers

The security headers are already configured in the Nginx setup:

- X-Frame-Options
- X-XSS-Protection
- X-Content-Type-Options
- Strict-Transport-Security
- Content-Security-Policy

### 4. Regular Security Updates

```bash
# Create security update script
sudo nano /usr/local/bin/security-updates.sh
```

```bash
#!/bin/bash
apt update && apt upgrade -y
docker-compose -f /var/www/beauty-salon/docker/docker-compose.yml pull
docker-compose -f /var/www/beauty-salon/docker/docker-compose.yml up -d
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/security-updates.sh

# Schedule weekly updates
sudo crontab -e
# Add: 0 3 * * 0 /usr/local/bin/security-updates.sh
```

## Troubleshooting

### Common Issues

#### 1. Container Won't Start

```bash
# Check logs
docker-compose -f docker/docker-compose.yml logs app

# Check container status
docker-compose -f docker/docker-compose.yml ps

# Restart containers
docker-compose -f docker/docker-compose.yml restart
```

#### 2. Database Connection Issues

```bash
# Check database container
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p

# Test connection
docker-compose -f docker/docker-compose.yml exec app php artisan tinker
```

#### 3. Permission Issues

```bash
# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### 4. SSL Certificate Issues

```bash
# Check certificate
openssl x509 -in /etc/nginx/ssl/cert.pem -text -noout

# Test SSL
curl -I https://yourdomain.com
```

#### 5. Performance Issues

```bash
# Check system resources
htop
df -h
free -h

# Check application performance
sudo ./scripts/performance-optimization.sh status
```

### Log Files

- **Application logs**: `storage/logs/laravel.log`
- **Nginx logs**: `/var/log/nginx/`
- **MySQL logs**: `/var/log/mysql/`
- **System logs**: `/var/log/syslog`

### Health Checks

```bash
# Application health
curl http://localhost/health

# Database health
docker-compose -f docker/docker-compose.yml exec db mysqladmin ping

# Redis health
docker-compose -f docker/docker-compose.yml exec redis redis-cli ping
```

## Maintenance

### Daily Tasks
- Monitor system resources
- Check application logs
- Verify backup completion
- Monitor SSL certificate expiry

### Weekly Tasks
- Review security logs
- Update system packages
- Clean up old logs
- Performance review

### Monthly Tasks
- Security audit
- Backup restoration test
- Performance optimization review
- Capacity planning

## Support

For technical support:
- Check the troubleshooting section
- Review log files
- Contact system administrator
- Submit issue on GitHub

## Conclusion

This deployment guide provides comprehensive instructions for setting up the BookingFlow in a production environment. Follow the steps carefully and ensure all security measures are implemented.

Remember to:
- Keep the system updated
- Monitor performance regularly
- Maintain backups
- Follow security best practices
- Document any custom configurations
