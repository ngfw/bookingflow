#!/bin/bash
#
# BookingFlow - Initial Server Setup
#
# This script sets up the application directory and performs the first deployment
# Run this ONCE on your production server before using automated deployments
#
# Usage: bash initial-setup.sh
#

set -e  # Exit on any error

echo "🚀 BookingFlow - Initial Setup"
echo "=================================================="
echo ""

# Configuration
APP_DIR="/var/www/book.vai.me"
REPO_URL="https://github.com/your-username/bookingflow.git"  # UPDATE THIS!
BRANCH="main"
WEB_USER="www-data"
APP_USER="ubuntu"  # Change if different

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -eq 0 ]; then
   echo -e "${RED}❌ Please do not run this script as root${NC}"
   echo "Run as: bash initial-setup.sh"
   exit 1
fi

# Check if repository URL is configured
if [[ "$REPO_URL" == *"your-username"* ]]; then
    echo -e "${RED}❌ Please update REPO_URL in this script with your actual repository URL${NC}"
    exit 1
fi

echo "📋 Configuration:"
echo "  Application Directory: $APP_DIR"
echo "  Repository: $REPO_URL"
echo "  Branch: $BRANCH"
echo "  Web User: $WEB_USER"
echo "  App User: $APP_USER"
echo ""

read -p "Continue with this configuration? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 1
fi

# Check if directory already exists
if [ -d "$APP_DIR" ]; then
    echo -e "${YELLOW}⚠️  Directory $APP_DIR already exists${NC}"
    read -p "Remove and reinstall? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        sudo rm -rf "$APP_DIR"
    else
        exit 1
    fi
fi

echo ""
echo "📦 Step 1: Creating application directory..."
sudo mkdir -p "$APP_DIR"
sudo chown $APP_USER:$WEB_USER "$APP_DIR"

echo ""
echo "📥 Step 2: Cloning repository..."
git clone "$REPO_URL" "$APP_DIR"
cd "$APP_DIR"

echo ""
echo "🔀 Step 3: Checking out branch: $BRANCH..."
git checkout "$BRANCH"

echo ""
echo "📚 Step 4: Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo ""
echo "📦 Step 5: Installing NPM dependencies..."
npm install

echo ""
echo "🏗️  Step 6: Building assets..."
npm run build

echo ""
echo "⚙️  Step 7: Setting up environment..."
if [ ! -f "$APP_DIR/.env" ]; then
    if [ -f "$APP_DIR/.env.production.example" ]; then
        sudo cp "$APP_DIR/.env.production.example" "$APP_DIR/.env"
        echo -e "${YELLOW}⚠️  .env file created from .env.production.example${NC}"
        echo "    You MUST edit it with your production credentials!"
    else
        sudo cp "$APP_DIR/.env.example" "$APP_DIR/.env"
        echo -e "${YELLOW}⚠️  .env file created from .env.example${NC}"
        echo "    You MUST edit it with your production credentials!"
    fi

    echo ""
    echo "🔑 Generating application key..."
    php artisan key:generate

    echo ""
    echo -e "${RED}⚠️  IMPORTANT: Edit .env file NOW before continuing${NC}"
    echo "    Required settings:"
    echo "      - DB_DATABASE, DB_USERNAME, DB_PASSWORD"
    echo "      - APP_URL"
    echo "      - MAIL_* settings"
    echo "      - TWILIO_* settings (if using SMS)"
    echo ""
    read -p "Press Enter after editing .env file..."
else
    echo -e "${GREEN}✓ .env file already exists${NC}"
fi

echo ""
echo "🗄️  Step 8: Setting up database..."
echo "Make sure your database exists before continuing!"
echo "Run: mysql -u root -p -e \"CREATE DATABASE your_database_name;\""
echo ""
read -p "Database created? Press Enter to continue..."

php artisan migrate --force

echo ""
echo "🌱 Step 9: Seeding database (optional)..."
read -p "Seed database with sample data? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
fi

echo ""
echo "🔗 Step 10: Creating storage link..."
php artisan storage:link

echo ""
echo "⚡ Step 11: Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ""
echo "🔒 Step 12: Setting permissions..."
sudo chown -R $WEB_USER:$WEB_USER "$APP_DIR"
sudo find "$APP_DIR" -type f -exec chmod 644 {} \;
sudo find "$APP_DIR" -type d -exec chmod 755 {} \;
sudo chmod +x "$APP_DIR"/node_modules/.bin/* 2>/dev/null || true
sudo chgrp -R $WEB_USER "$APP_DIR"/storage "$APP_DIR"/bootstrap/cache
sudo chmod -R ug+rwx "$APP_DIR"/storage "$APP_DIR"/bootstrap/cache
sudo chmod 600 "$APP_DIR/.env"

echo ""
echo "✅ Initial setup complete!"
echo ""
echo "📋 Next Steps:"
echo "1. ✅ Verify your .env configuration"
echo "2. ✅ Test the application: curl -I http://your-server-ip"
echo "3. ✅ Configure Nginx/Apache (see docs/PRODUCTION_DEPLOYMENT.md)"
echo "4. ✅ Set up SSL certificate (certbot --nginx)"
echo "5. ✅ Configure queue workers (systemd service)"
echo "6. ✅ Set up cron job for scheduler"
echo "7. ✅ Configure automated backups"
echo ""
echo "🚀 For future deployments, just push tags:"
echo "   git tag -a v1.0.0 -m \"Release 1.0.0\""
echo "   git push origin v1.0.0"
echo ""
echo "📖 Full documentation: docs/PRODUCTION_DEPLOYMENT.md"
