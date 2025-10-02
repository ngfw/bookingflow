# Beauty Salon Management System - Maintenance Guide

## Overview
This guide provides comprehensive maintenance procedures for the Beauty Salon Management System to ensure optimal performance, security, and reliability.

## Table of Contents
1. [Daily Maintenance](#daily-maintenance)
2. [Weekly Maintenance](#weekly-maintenance)
3. [Monthly Maintenance](#monthly-maintenance)
4. [Quarterly Maintenance](#quarterly-maintenance)
5. [Emergency Procedures](#emergency-procedures)
6. [Performance Monitoring](#performance-monitoring)
7. [Security Maintenance](#security-maintenance)
8. [Backup and Recovery](#backup-and-recovery)
9. [Log Management](#log-management)
10. [Troubleshooting](#troubleshooting)

## Daily Maintenance

### 1. System Health Check

```bash
# Check system resources
./scripts/performance-optimization.sh status

# Check application health
curl -f http://localhost/health

# Check container status
docker-compose -f docker/docker-compose.yml ps

# Check disk space
df -h

# Check memory usage
free -h

# Check load average
uptime
```

### 2. Application Logs Review

```bash
# Check application logs
tail -f storage/logs/laravel.log

# Check error logs
grep -i error storage/logs/laravel.log | tail -20

# Check nginx logs
tail -f /var/log/nginx/error.log

# Check system logs
tail -f /var/log/syslog
```

### 3. Database Health Check

```bash
# Check database status
docker-compose -f docker/docker-compose.yml exec db mysqladmin ping

# Check database connections
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW PROCESSLIST;"

# Check slow queries
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW GLOBAL STATUS LIKE 'Slow_queries';"

# Check database size
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema = 'beauty_salon' GROUP BY table_schema;"
```

### 4. Cache and Session Check

```bash
# Check Redis status
docker-compose -f docker/docker-compose.yml exec redis redis-cli ping

# Check Redis memory usage
docker-compose -f docker/docker-compose.yml exec redis redis-cli info memory

# Check cache hit rate
docker-compose -f docker/docker-compose.yml exec redis redis-cli info stats | grep keyspace
```

### 5. Backup Verification

```bash
# Check backup status
ls -la /var/backups/beauty-salon/

# Verify latest backup
./scripts/backup.sh

# Check backup integrity
./scripts/restore.sh --list
```

## Weekly Maintenance

### 1. System Updates

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update Docker images
docker-compose -f docker/docker-compose.yml pull

# Restart services
docker-compose -f docker/docker-compose.yml restart
```

### 2. Security Audit

```bash
# Check failed login attempts
grep "Failed password" /var/log/auth.log | tail -20

# Check fail2ban status
sudo fail2ban-client status

# Check SSL certificate expiry
openssl x509 -in /etc/nginx/ssl/cert.pem -noout -dates

# Check for security updates
sudo apt list --upgradable | grep security
```

### 3. Performance Analysis

```bash
# Run performance monitoring
./scripts/performance-optimization.sh monitor

# Check slow queries
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SELECT * FROM mysql.slow_log ORDER BY start_time DESC LIMIT 10;"

# Analyze application performance
curl -w "@curl-format.txt" -o /dev/null -s http://localhost/

# Check memory usage trends
free -h
```

### 4. Log Rotation and Cleanup

```bash
# Check log rotation status
sudo logrotate -d /etc/logrotate.d/beauty-salon

# Force log rotation
sudo logrotate -f /etc/logrotate.d/beauty-salon

# Clean up old logs
find /var/log -name "*.log.*" -mtime +30 -delete
find storage/logs -name "*.log.*" -mtime +30 -delete
```

### 5. Database Maintenance

```bash
# Optimize database tables
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "OPTIMIZE TABLE beauty_salon.appointments, beauty_salon.clients, beauty_salon.staff, beauty_salon.services;"

# Check table fragmentation
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)', ROUND((data_free / 1024 / 1024), 2) AS 'Free Space (MB)' FROM information_schema.tables WHERE table_schema = 'beauty_salon' AND data_free > 0;"

# Analyze tables
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "ANALYZE TABLE beauty_salon.appointments, beauty_salon.clients, beauty_salon.staff, beauty_salon.services;"
```

## Monthly Maintenance

### 1. Comprehensive Security Review

```bash
# Run security scan
sudo ./scripts/ssl-setup.sh test

# Check for vulnerabilities
composer audit

# Review access logs
grep -E "(40[0-9]|50[0-9])" /var/log/nginx/access.log | tail -50

# Check user permissions
ls -la storage/
ls -la bootstrap/cache/
```

### 2. Performance Optimization Review

```bash
# Run full performance optimization
sudo ./scripts/performance-optimization.sh optimize

# Review performance metrics
./scripts/performance-optimization.sh status

# Check for bottlenecks
htop
iotop
netstat -tulpn
```

### 3. Backup and Recovery Testing

```bash
# Test backup restoration
./scripts/restore.sh --list

# Create test restore point
./scripts/backup.sh

# Test disaster recovery
./scripts/disaster-recovery.sh auto
```

### 4. Capacity Planning

```bash
# Check disk usage trends
df -h
du -sh /var/www/beauty-salon/storage/logs/
du -sh /var/lib/mysql/
du -sh /var/lib/redis/

# Check database growth
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema = 'beauty_salon' GROUP BY table_schema;"

# Check log file sizes
find /var/log -name "*.log" -exec ls -lh {} \; | sort -k5 -hr | head -10
```

### 5. Application Updates

```bash
# Check for application updates
git fetch origin
git log HEAD..origin/main --oneline

# Review changelog
cat CHANGELOG.md

# Plan update deployment
git checkout main
git pull origin main
docker-compose -f docker/docker-compose.yml exec app composer install --optimize-autoloader --no-dev
docker-compose -f docker/docker-compose.yml exec app php artisan migrate --force
docker-compose -f docker/docker-compose.yml exec app php artisan config:cache
docker-compose -f docker/docker-compose.yml exec app php artisan route:cache
docker-compose -f docker/docker-compose.yml exec app php artisan view:cache
```

## Quarterly Maintenance

### 1. Security Audit and Penetration Testing

```bash
# Run comprehensive security scan
nmap -sS -O localhost

# Check for open ports
netstat -tulpn | grep LISTEN

# Review firewall rules
sudo ufw status verbose

# Check SSL configuration
sslscan localhost:443
```

### 2. Performance Benchmarking

```bash
# Run load tests
artillery run tests/performance/load-test.yml

# Benchmark database performance
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SELECT BENCHMARK(1000000, MD5('test'));"

# Test application response times
for i in {1..100}; do curl -w "%{time_total}\n" -o /dev/null -s http://localhost/; done | awk '{sum+=$1} END {print "Average:", sum/NR}'
```

### 3. Disaster Recovery Testing

```bash
# Test full system recovery
./scripts/disaster-recovery.sh full

# Test partial recovery
./scripts/disaster-recovery.sh partial

# Test quick fix procedures
./scripts/disaster-recovery.sh quick
```

### 4. Documentation Review

- Update deployment documentation
- Review maintenance procedures
- Update troubleshooting guides
- Document any custom configurations

## Emergency Procedures

### 1. System Down

```bash
# Check system status
systemctl status nginx
systemctl status mysql
systemctl status redis-server

# Restart services
sudo systemctl restart nginx
sudo systemctl restart mysql
sudo systemctl restart redis-server

# Check application
docker-compose -f docker/docker-compose.yml ps
docker-compose -f docker/docker-compose.yml restart
```

### 2. Database Issues

```bash
# Check database status
docker-compose -f docker/docker-compose.yml exec db mysqladmin ping

# Restart database
docker-compose -f docker/docker-compose.yml restart db

# Check database logs
docker-compose -f docker/docker-compose.yml logs db
```

### 3. High Load

```bash
# Check system load
htop
uptime

# Check processes
ps aux --sort=-%cpu | head -10
ps aux --sort=-%mem | head -10

# Restart services
docker-compose -f docker/docker-compose.yml restart
```

### 4. Security Incident

```bash
# Check for suspicious activity
grep -i "failed\|error\|attack" /var/log/auth.log
grep -i "40[0-9]\|50[0-9]" /var/log/nginx/access.log

# Block suspicious IPs
sudo ufw deny from <suspicious-ip>

# Check fail2ban
sudo fail2ban-client status
```

## Performance Monitoring

### 1. Real-time Monitoring

```bash
# Monitor system resources
htop
iotop
nethogs

# Monitor application
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/access.log
```

### 2. Performance Metrics

```bash
# Check response times
curl -w "@curl-format.txt" -o /dev/null -s http://localhost/

# Check database performance
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW GLOBAL STATUS LIKE 'Questions';"

# Check cache performance
docker-compose -f docker/docker-compose.yml exec redis redis-cli info stats
```

### 3. Alerting

```bash
# Check monitoring alerts
./scripts/performance-optimization.sh monitor

# Review alert logs
tail -f /var/log/performance-monitor.log
```

## Security Maintenance

### 1. Regular Security Updates

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update Docker images
docker-compose -f docker/docker-compose.yml pull

# Update application dependencies
docker-compose -f docker/docker-compose.yml exec app composer update
```

### 2. SSL Certificate Management

```bash
# Check certificate expiry
openssl x509 -in /etc/nginx/ssl/cert.pem -noout -dates

# Renew certificate
sudo ./scripts/ssl-setup.sh renew

# Test SSL configuration
sudo ./scripts/ssl-setup.sh test
```

### 3. Access Control Review

```bash
# Review user accounts
cat /etc/passwd | grep -E "(www-data|mysql|redis)"

# Check file permissions
find /var/www/beauty-salon -type f -perm /o+w
find /var/www/beauty-salon -type d -perm /o+w
```

## Backup and Recovery

### 1. Backup Verification

```bash
# Check backup status
ls -la /var/backups/beauty-salon/

# Verify backup integrity
./scripts/backup.sh

# Test restore process
./scripts/restore.sh --list
```

### 2. Recovery Testing

```bash
# Test disaster recovery
./scripts/disaster-recovery.sh auto

# Test backup restoration
./scripts/restore.sh --date <backup-date>
```

## Log Management

### 1. Log Rotation

```bash
# Check log rotation configuration
sudo logrotate -d /etc/logrotate.d/beauty-salon

# Force log rotation
sudo logrotate -f /etc/logrotate.d/beauty-salon
```

### 2. Log Analysis

```bash
# Analyze error logs
grep -i error storage/logs/laravel.log | tail -50

# Analyze access logs
awk '{print $1}' /var/log/nginx/access.log | sort | uniq -c | sort -nr | head -10

# Analyze slow queries
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SELECT * FROM mysql.slow_log ORDER BY start_time DESC LIMIT 10;"
```

## Troubleshooting

### 1. Common Issues

#### Application Not Responding
```bash
# Check container status
docker-compose -f docker/docker-compose.yml ps

# Check application logs
docker-compose -f docker/docker-compose.yml logs app

# Restart application
docker-compose -f docker/docker-compose.yml restart app
```

#### Database Connection Issues
```bash
# Check database status
docker-compose -f docker/docker-compose.yml exec db mysqladmin ping

# Check database logs
docker-compose -f docker/docker-compose.yml logs db

# Restart database
docker-compose -f docker/docker-compose.yml restart db
```

#### High Memory Usage
```bash
# Check memory usage
free -h
ps aux --sort=-%mem | head -10

# Check Redis memory
docker-compose -f docker/docker-compose.yml exec redis redis-cli info memory

# Restart services
docker-compose -f docker/docker-compose.yml restart
```

### 2. Performance Issues

#### Slow Response Times
```bash
# Check system load
uptime
htop

# Check database performance
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SHOW PROCESSLIST;"

# Check slow queries
docker-compose -f docker/docker-compose.yml exec db mysql -u root -p -e "SELECT * FROM mysql.slow_log ORDER BY start_time DESC LIMIT 10;"
```

#### High CPU Usage
```bash
# Check CPU usage
top
ps aux --sort=-%cpu | head -10

# Check for runaway processes
ps aux | grep -E "(php|mysql|redis|nginx)"
```

### 3. Security Issues

#### Suspicious Activity
```bash
# Check failed login attempts
grep "Failed password" /var/log/auth.log

# Check for brute force attacks
grep "40[0-9]" /var/log/nginx/access.log | awk '{print $1}' | sort | uniq -c | sort -nr

# Check fail2ban status
sudo fail2ban-client status
```

## Maintenance Checklist

### Daily
- [ ] Check system health
- [ ] Review application logs
- [ ] Verify backup completion
- [ ] Check disk space
- [ ] Monitor performance

### Weekly
- [ ] Update system packages
- [ ] Review security logs
- [ ] Analyze performance metrics
- [ ] Clean up old logs
- [ ] Optimize database tables

### Monthly
- [ ] Comprehensive security review
- [ ] Performance optimization
- [ ] Backup and recovery testing
- [ ] Capacity planning
- [ ] Application updates

### Quarterly
- [ ] Security audit
- [ ] Performance benchmarking
- [ ] Disaster recovery testing
- [ ] Documentation review
- [ ] Penetration testing

## Conclusion

Regular maintenance is essential for the optimal performance and security of the Beauty Salon Management System. Follow this guide to ensure your system remains healthy, secure, and performant.

Remember to:
- Document all maintenance activities
- Keep maintenance logs
- Test procedures in staging environment
- Have rollback plans ready
- Monitor system after maintenance
