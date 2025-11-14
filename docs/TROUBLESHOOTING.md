# BookingFlow - Troubleshooting Guide

## Overview
This guide provides comprehensive troubleshooting procedures for common issues that may occur with the BookingFlow.

## Table of Contents
1. [General Troubleshooting](#general-troubleshooting)
2. [Application Issues](#application-issues)
3. [Database Issues](#database-issues)
4. [Web Server Issues](#web-server-issues)
5. [Performance Issues](#performance-issues)
6. [Security Issues](#security-issues)
7. [Docker Issues](#docker-issues)
8. [SSL/HTTPS Issues](#sslhttps-issues)
9. [Backup and Recovery Issues](#backup-and-recovery-issues)
10. [Monitoring Issues](#monitoring-issues)

## General Troubleshooting

### 1. System Health Check

```bash
# Check system status
systemctl status nginx
systemctl status mysql
systemctl status redis-server
systemctl status php8.2-fpm

# Check Docker containers
docker-compose -f docker/docker-compose.yml ps

# Check system resources
htop
df -h
free -h
uptime
```

### 2. Log Analysis

```bash
# Application logs
tail -f storage/logs/laravel.log
grep -i error storage/logs/laravel.log | tail -20

# System logs
tail -f /var/log/syslog
tail -f /var/log/nginx/error.log
tail -f /var/log/mysql/error.log

# Docker logs
docker-compose -f docker/docker-compose.yml logs app
docker-compose -f docker/docker-compose.yml logs db
docker-compose -f docker/docker-compose.yml logs redis
```

### 3. Network Connectivity

```bash
# Test local connectivity
curl -I http://localhost
curl -I http://localhost:8000

# Test external connectivity
ping google.com
nslookup yourdomain.com

# Check ports
netstat -tulpn | grep :80
netstat -tulpn | grep :443
netstat -tulpn | grep :3306
netstat -tulpn | grep :6379
```

## Application Issues

### 1. Application Not Loading

**Symptoms:**
- Blank page
- 500 Internal Server Error
- Application not responding

**Diagnosis:**
```bash
# Check application status
docker-compose -f docker/docker-compose.yml ps app

# Check application logs
docker-compose -f docker/docker-compose.yml logs app

# Check PHP-FPM status
docker-compose -f docker/docker-compose.yml exec app php-fpm -t

# Check Laravel configuration
docker-compose -f docker/docker-compose.yml exec app php artisan config:show
```

**Solutions:**
```bash
# Restart application
docker-compose -f docker/docker-compose.yml restart app

# Clear caches
docker-compose -f docker/docker-compose.yml exec app php artisan cache:clear
docker-compose -f docker/docker-compose.yml exec app php artisan config:clear
docker-compose -f docker/docker-compose.yml exec app php artisan route:clear
docker-compose -f docker/docker-compose.yml exec app php artisan view:clear

# Rebuild caches
docker-compose -f docker/docker-compose.yml exec app php artisan config:cache
docker-compose -f docker/docker-compose.yml exec app php artisan route:cache
docker-compose -f docker/docker-compose.yml exec app php artisan view:cache

# Check permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 2. Database Connection Errors

**Symptoms:**
- "Connection refused" errors
- Database timeout
- SQL errors

**Diagnosis:**
```bash
# Check database container
docker-compose -f docker/docker-compose.yml ps db

# Test database connection
docker-compose -f docker/docker-compose.yml exec db mysqladmin ping

# Check database logs
docker-compose -f docker/docker-compose.yml logs db

# Test connection from application
docker-compose -f docker/docker-compose.yml exec app php artisan tinker
```

**Solutions:**
```bash
# Restart database
docker-compose -f docker/docker-compose.yml restart db

# Check database configuration
docker-compose -f docker/docker-compose.yml exec app php artisan config:show database

# Test database credentials
docker-compose -f docker/docker-compose.yml exec db mysql -u bookingflow_user -p

# Check database permissions
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW GRANTS FOR 'bookingflow_user'@'%';"
```

### 3. Session and Cache Issues

**Symptoms:**
- Users getting logged out
- Cache not working
- Session data lost

**Diagnosis:**
```bash
# Check Redis status
docker-compose -f docker/docker-compose.yml exec redis redis-cli ping

# Check Redis memory
docker-compose -f docker/docker-compose.yml exec redis redis-cli info memory

# Check session configuration
docker-compose -f docker/docker-compose.yml exec app php artisan config:show session
```

**Solutions:**
```bash
# Restart Redis
docker-compose -f docker/docker-compose.yml restart redis

# Clear all caches
docker-compose -f docker/docker-compose.yml exec app php artisan cache:clear
docker-compose -f docker/docker-compose.yml exec redis redis-cli FLUSHALL

# Check Redis configuration
docker-compose -f docker/docker-compose.yml exec redis redis-cli CONFIG GET "*"
```

## Database Issues

### 1. Database Performance Issues

**Symptoms:**
- Slow queries
- High CPU usage
- Database locks

**Diagnosis:**
```bash
# Check slow queries
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW GLOBAL STATUS LIKE 'Slow_queries';"

# Check process list
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW PROCESSLIST;"

# Check table status
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW TABLE STATUS FROM bookingflow;"
```

**Solutions:**
```bash
# Optimize tables
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "OPTIMIZE TABLE bookingflow.appointments, bookingflow.clients, bookingflow.staff;"

# Analyze tables
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "ANALYZE TABLE bookingflow.appointments, bookingflow.clients, bookingflow.staff;"

# Check indexes
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW INDEX FROM bookingflow.appointments;"
```

### 2. Database Corruption

**Symptoms:**
- Inconsistent data
- Table errors
- Database crashes

**Diagnosis:**
```bash
# Check table integrity
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "CHECK TABLE bookingflow.appointments, bookingflow.clients, bookingflow.staff;"

# Check database status
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW ENGINE INNODB STATUS;"
```

**Solutions:**
```bash
# Repair tables
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "REPAIR TABLE bookingflow.appointments, bookingflow.clients, bookingflow.staff;"

# Restore from backup
./scripts/restore.sh --date <backup-date>
```

## Web Server Issues

### 1. Nginx Configuration Issues

**Symptoms:**
- 502 Bad Gateway
- 504 Gateway Timeout
- Configuration errors

**Diagnosis:**
```bash
# Test Nginx configuration
nginx -t

# Check Nginx status
systemctl status nginx

# Check Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log
```

**Solutions:**
```bash
# Restart Nginx
sudo systemctl restart nginx

# Reload configuration
sudo systemctl reload nginx

# Check PHP-FPM status
systemctl status php8.2-fpm

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### 2. SSL Certificate Issues

**Symptoms:**
- SSL errors
- Certificate expired
- HTTPS not working

**Diagnosis:**
```bash
# Check certificate
openssl x509 -in /etc/nginx/ssl/cert.pem -text -noout

# Test SSL
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Check certificate expiry
openssl x509 -in /etc/nginx/ssl/cert.pem -noout -dates
```

**Solutions:**
```bash
# Renew certificate
sudo ./scripts/ssl-setup.sh renew

# Test SSL configuration
sudo ./scripts/ssl-setup.sh test

# Check SSL status
sudo ./scripts/ssl-setup.sh status
```

## Performance Issues

### 1. Slow Response Times

**Symptoms:**
- Pages loading slowly
- High response times
- Timeout errors

**Diagnosis:**
```bash
# Check system load
uptime
htop

# Check response times
curl -w "@curl-format.txt" -o /dev/null -s http://localhost/

# Check database performance
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW GLOBAL STATUS LIKE 'Questions';"
```

**Solutions:**
```bash
# Run performance optimization
sudo ./scripts/performance-optimization.sh optimize

# Check performance status
sudo ./scripts/performance-optimization.sh status

# Monitor performance
sudo ./scripts/performance-optimization.sh monitor
```

### 2. High Memory Usage

**Symptoms:**
- System running out of memory
- Swap usage high
- OOM errors

**Diagnosis:**
```bash
# Check memory usage
free -h
ps aux --sort=-%mem | head -10

# Check Redis memory
docker-compose -f docker/docker-compose.yml exec redis redis-cli info memory

# Check MySQL memory
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW GLOBAL STATUS LIKE 'Innodb_buffer_pool_pages_data';"
```

**Solutions:**
```bash
# Restart services
docker-compose -f docker/docker-compose.yml restart

# Clear caches
docker-compose -f docker/docker-compose.yml exec redis redis-cli FLUSHALL

# Optimize MySQL
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SET GLOBAL innodb_buffer_pool_size = 1G;"
```

## Security Issues

### 1. Failed Login Attempts

**Symptoms:**
- Multiple failed logins
- Account lockouts
- Suspicious activity

**Diagnosis:**
```bash
# Check failed logins
grep "Failed password" /var/log/auth.log | tail -20

# Check fail2ban status
sudo fail2ban-client status

# Check Nginx access logs
grep "40[0-9]" /var/log/nginx/access.log | tail -20
```

**Solutions:**
```bash
# Check fail2ban jails
sudo fail2ban-client status sshd
sudo fail2ban-client status nginx-http-auth

# Unban IP if needed
sudo fail2ban-client set sshd unbanip <ip-address>

# Check firewall
sudo ufw status
```

### 2. SSL Security Issues

**Symptoms:**
- SSL warnings
- Certificate errors
- Security vulnerabilities

**Diagnosis:**
```bash
# Test SSL configuration
sslscan yourdomain.com:443

# Check SSL headers
curl -I https://yourdomain.com

# Test SSL labs
# Visit: https://www.ssllabs.com/ssltest/
```

**Solutions:**
```bash
# Update SSL configuration
sudo ./scripts/ssl-setup.sh setup

# Test SSL
sudo ./scripts/ssl-setup.sh test

# Check security headers
curl -I https://yourdomain.com | grep -E "(Strict-Transport-Security|X-Frame-Options|X-Content-Type-Options)"
```

## Docker Issues

### 1. Container Won't Start

**Symptoms:**
- Container status "Exited"
- Startup errors
- Port conflicts

**Diagnosis:**
```bash
# Check container status
docker-compose -f docker/docker-compose.yml ps

# Check container logs
docker-compose -f docker/docker-compose.yml logs <container-name>

# Check Docker daemon
systemctl status docker
```

**Solutions:**
```bash
# Restart containers
docker-compose -f docker/docker-compose.yml restart

# Rebuild containers
docker-compose -f docker/docker-compose.yml build --no-cache

# Check port conflicts
netstat -tulpn | grep :80
netstat -tulpn | grep :3306
```

### 2. Docker Volume Issues

**Symptoms:**
- Data not persisting
- Permission errors
- Volume mount failures

**Diagnosis:**
```bash
# Check volumes
docker volume ls

# Check volume details
docker volume inspect <volume-name>

# Check mount points
docker-compose -f docker/docker-compose.yml exec app ls -la /var/www/html
```

**Solutions:**
```bash
# Recreate volumes
docker-compose -f docker/docker-compose.yml down -v
docker-compose -f docker/docker-compose.yml up -d

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## SSL/HTTPS Issues

### 1. Certificate Expired

**Symptoms:**
- Browser SSL warnings
- Certificate expired errors
- HTTPS not working

**Diagnosis:**
```bash
# Check certificate expiry
openssl x509 -in /etc/nginx/ssl/cert.pem -noout -dates

# Test certificate
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com
```

**Solutions:**
```bash
# Renew certificate
sudo ./scripts/ssl-setup.sh renew

# Test renewal
sudo certbot renew --dry-run

# Check renewal status
sudo ./scripts/ssl-setup.sh status
```

### 2. SSL Configuration Errors

**Symptoms:**
- SSL handshake failures
- Protocol errors
- Cipher issues

**Diagnosis:**
```bash
# Test SSL configuration
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Check SSL protocols
nmap --script ssl-enum-ciphers -p 443 yourdomain.com
```

**Solutions:**
```bash
# Update SSL configuration
sudo ./scripts/ssl-setup.sh setup

# Test SSL
sudo ./scripts/ssl-setup.sh test

# Check Nginx SSL config
nginx -t
```

## Backup and Recovery Issues

### 1. Backup Failures

**Symptoms:**
- Backup script errors
- Incomplete backups
- Permission errors

**Diagnosis:**
```bash
# Check backup logs
tail -f /var/log/backup.log

# Test backup script
sudo ./scripts/backup.sh

# Check backup directory
ls -la /var/backups/beauty-salon/
```

**Solutions:**
```bash
# Fix permissions
sudo chown -R www-data:www-data /var/backups/beauty-salon/
sudo chmod -R 755 /var/backups/beauty-salon/

# Test backup
sudo ./scripts/backup.sh

# Check cron job
sudo crontab -l
```

### 2. Recovery Issues

**Symptoms:**
- Restore failures
- Data corruption
- Missing files

**Diagnosis:**
```bash
# List available backups
./scripts/restore.sh --list

# Check backup integrity
gunzip -t /var/backups/beauty-salon/db_backup_*.sql.gz
```

**Solutions:**
```bash
# Test restore
./scripts/restore.sh --date <backup-date>

# Check restore logs
tail -f /var/backups/beauty-salon/restore.log

# Verify restoration
curl -f http://localhost/health
```

## Monitoring Issues

### 1. Monitoring Services Down

**Symptoms:**
- Grafana not accessible
- Prometheus errors
- No metrics

**Diagnosis:**
```bash
# Check monitoring containers
docker-compose -f docker/monitoring/docker-compose.monitoring.yml ps

# Check monitoring logs
docker-compose -f docker/monitoring/docker-compose.monitoring.yml logs prometheus
docker-compose -f docker/monitoring/docker-compose.monitoring.yml logs grafana
```

**Solutions:**
```bash
# Restart monitoring services
docker-compose -f docker/monitoring/docker-compose.monitoring.yml restart

# Check monitoring configuration
docker-compose -f docker/monitoring/docker-compose.monitoring.yml exec prometheus promtool check config /etc/prometheus/prometheus.yml
```

### 2. Alert Issues

**Symptoms:**
- No alerts received
- False alerts
- Alert configuration errors

**Diagnosis:**
```bash
# Check alertmanager status
docker-compose -f docker/monitoring/docker-compose.monitoring.yml exec alertmanager amtool config show

# Check alert rules
docker-compose -f docker/monitoring/docker-compose.monitoring.yml exec prometheus promtool check rules /etc/prometheus/rules/*.yml
```

**Solutions:**
```bash
# Test alert configuration
docker-compose -f docker/monitoring/docker-compose.monitoring.yml exec alertmanager amtool config validate /etc/alertmanager/alertmanager.yml

# Reload alertmanager
docker-compose -f docker/monitoring/docker-compose.monitoring.yml exec alertmanager amtool config reload
```

## Emergency Procedures

### 1. System Down

```bash
# Check system status
systemctl status nginx mysql redis-server

# Restart all services
sudo systemctl restart nginx mysql redis-server
docker-compose -f docker/docker-compose.yml restart

# Check application
curl -f http://localhost/health
```

### 2. Database Down

```bash
# Check database
docker-compose -f docker/docker-compose.yml exec db mysqladmin ping

# Restart database
docker-compose -f docker/docker-compose.yml restart db

# Check database logs
docker-compose -f docker/docker-compose.yml logs db
```

### 3. High Load

```bash
# Check system load
uptime
htop

# Restart services
docker-compose -f docker/docker-compose.yml restart

# Clear caches
docker-compose -f docker/docker-compose.yml exec redis redis-cli FLUSHALL
```

## Prevention

### 1. Regular Maintenance

```bash
# Daily health checks
./scripts/performance-optimization.sh status

# Weekly updates
sudo apt update && sudo apt upgrade -y

# Monthly optimization
sudo ./scripts/performance-optimization.sh optimize
```

### 2. Monitoring

```bash
# Set up monitoring
docker-compose -f docker/monitoring/docker-compose.monitoring.yml up -d

# Configure alerts
# Edit docker/monitoring/alertmanager/alertmanager.yml
```

### 3. Backups

```bash
# Schedule backups
sudo crontab -e
# Add: 0 2 * * * /var/www/beauty-salon/scripts/backup.sh

# Test backups
./scripts/restore.sh --list
```

## Conclusion

This troubleshooting guide covers the most common issues that may occur with the BookingFlow. Always:

1. Check logs first
2. Verify system status
3. Test connectivity
4. Check configurations
5. Restart services if needed
6. Document the issue and solution

For issues not covered in this guide, check the application logs and system logs for more specific error messages.
