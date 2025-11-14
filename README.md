# Beauty Salon Management System

A comprehensive, enterprise-grade web application for managing beauty salons, spas, and similar service-based businesses. Built with Laravel 12, Livewire 3, and modern web technologies.

## 🌟 Features

### Core Functionality
- **📅 Appointment Management** - Online booking, calendar views, conflict resolution, recurring appointments
- **💰 Point of Sale (POS)** - Complete retail and service sales with receipt generation
- **👥 Client Management** - Comprehensive profiles, service history, loyalty points
- **👨‍💼 Staff Management** - Scheduling, performance tracking, payroll, commissions
- **📦 Inventory System** - Stock management, barcode scanning, purchase orders
- **💳 Payment Processing** - Multiple payment methods, invoicing, refunds
- **📊 Analytics & Reporting** - Business intelligence, predictive analytics, custom reports

### Advanced Features
- **🎁 Loyalty Programs** - Points system, membership tiers, rewards redemption
- **📧 Communication** - Email/SMS notifications, appointment reminders, campaigns
- **🏢 Multi-Location Support** - Franchise management, location-specific operations
- **🔐 Security** - Role-based access control, audit trails, GDPR compliance
- **📱 Mobile Optimized** - Responsive design, mobile POS, mobile check-in
- **🌐 CMS Features** - Page builder, blog, SEO management, dynamic content
- **🔌 API** - RESTful API with Sanctum authentication

## 🚀 Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Database**: MySQL 8.0
- **Authentication**: Laravel Breeze/Sanctum
- **Notifications**: Twilio (SMS), Email
- **PDF Generation**: DomPDF
- **Additional**: Spatie Permissions, Spatie Backup, Activity Log

## 📋 System Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer 2.x
- Node.js 18+ and NPM
- Redis (optional, for caching)

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd bookingflow
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=beauty_salon_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Assets

```bash
npm run build
```

### 6. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 👤 Default User Accounts

After seeding the database, you can log in with:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@beautysalon.com | password |
| Staff | staff@beautysalon.com | password |
| Client | client@beautysalon.com | password |

**⚠️ Important**: Change these passwords before deploying to production!

## 📱 Application Routes

### Public Access
- **Home**: `/`
- **Book Appointment**: `/book`
- **Manage Booking**: `/manage-booking`
- **Login**: `/login`
- **Register**: `/register`

### Admin Dashboard
- **Dashboard**: `/admin/dashboard`
- **Appointments**: `/admin/appointments`
- **Clients**: `/admin/clients`
- **Services**: `/admin/services`
- **Staff**: `/admin/staff`
- **Inventory**: `/admin/inventory`
- **POS**: `/admin/pos`
- **Reports**: `/admin/reports`
- **Settings**: `/admin/settings`

### Staff Portal
- **Dashboard**: `/staff/dashboard`
- **My Appointments**: `/staff/appointments`
- **My Schedule**: `/staff/schedule`
- **My Performance**: `/staff/performance`
- **POS**: `/staff/pos`

### Client Portal
- **Dashboard**: `/dashboard`
- **My Appointments**: `/appointments`
- **Profile**: `/profile`

## 📊 Key Statistics

- **62 Models** - Comprehensive data structure
- **110 Livewire Components** - Interactive UI
- **77 Database Migrations** - Complex schema
- **182 Blade Views** - Extensive UI coverage
- **40 Test Files** - Unit, Feature, Security, Performance tests
- **18 Service Classes** - Business logic separation
- **10 Documentation Files** - Complete guides

## 🔧 Configuration

### Salon Settings

Access `/admin/settings` to configure:
- Business information (name, address, contact)
- Branding (logo, colors)
- Booking settings (time slots, advance booking limits)
- Payment methods
- Notification preferences
- Email/SMS templates

### Permissions

The system uses Spatie Permission package for role-based access control:
- **Super Admin** - Full system access
- **Admin** - Salon management
- **Staff** - Service delivery and POS
- **Client** - Personal portal access

## 📖 Documentation

Comprehensive documentation is available in the `/docs` directory:

- [User Guide](docs/USER_GUIDE.md) - End-user instructions
- [Admin Manual](docs/ADMIN_MANUAL.md) - Administrative guide
- [API Documentation](docs/API_DOCUMENTATION.md) - REST API reference
- [Training Materials](docs/TRAINING_MATERIALS.md) - Staff training resources
- [FAQ](docs/FAQ.md) - Frequently asked questions
- [Troubleshooting](docs/TROUBLESHOOTING.md) - Common issues and solutions
- [Deployment Guide](docs/DEPLOYMENT.md) - Production deployment
- [Maintenance](docs/MAINTENANCE.md) - System maintenance tasks

## 🧪 Testing

Run the test suite:

```bash
# All tests
php artisan test

# Specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# With coverage
php artisan test --coverage
```

## 🔐 Security

This application includes:
- ✅ CSRF Protection
- ✅ SQL Injection Prevention
- ✅ XSS Protection
- ✅ Data Encryption
- ✅ Audit Trails
- ✅ GDPR Compliance Tools
- ✅ Two-Factor Authentication
- ✅ Role-Based Access Control

## 🌐 API

The application includes a RESTful API at `/api/v1`.

API documentation is available at `/api/documentation` after installation.

### Authentication

The API uses Laravel Sanctum for authentication. Generate a token:

```bash
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "admin@beautysalon.com",
    "password": "password"
}
```

## 📦 Production Deployment

See [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) for detailed deployment instructions.

### Quick Checklist
- [ ] Update `.env` with production values
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Configure database with production credentials
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set up SSL certificate
- [ ] Configure cron jobs for scheduled tasks
- [ ] Set up automated backups
- [ ] Configure error monitoring (Sentry, Bugsnag)
- [ ] Change default passwords

## 🤝 Contributing

This is a private project. For authorized contributors:

1. Create a feature branch
2. Make your changes
3. Write/update tests
4. Submit a pull request

## 📝 License

Proprietary - All rights reserved.

## 🆘 Support

For support inquiries:
- Check the [FAQ](docs/FAQ.md)
- Review [Troubleshooting Guide](docs/TROUBLESHOOTING.md)
- Contact the development team

## 🎯 Project Status

**Current Version**: 1.0.0
**Status**: Production Ready (90% Complete)

### Completed Features ✅
- Core booking and scheduling system
- Client and staff management
- Point of Sale (POS) system
- Inventory management
- Payment processing and invoicing
- Comprehensive reporting and analytics
- Loyalty programs and marketing tools
- Multi-location and franchise support
- Security and compliance features
- Complete documentation

### In Progress 🔄
- Enhanced mobile features
- Additional chart visualizations
- Extended test coverage

## 🏆 Credits

Developed with Laravel, Livewire, and modern web technologies.

---

**Made with ❤️ for beauty and wellness professionals**
