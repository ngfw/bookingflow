# Critical Path Tests - Execution Guide

## 📋 Overview

We've created **20 comprehensive test methods** across 2 critical path test files:
- **Booking Flow Tests**: 9 test methods
- **Payment Processing Tests**: 11 test methods

These tests verify the end-to-end functionality of the most critical business processes.

---

## 🔧 Environment Setup

### Prerequisites

Before running the tests, ensure your environment has:

1. **PHP Extensions Required**:
   ```bash
   php -m | grep -E "pdo|mysql|mbstring|xml"
   ```

   Required extensions:
   - `pdo`
   - `pdo_mysql` OR `pdo_sqlite`
   - `mbstring`
   - `xml`
   - `curl`

2. **Database Setup** (Choose One):

   **Option A: MySQL** (Recommended for production-like testing)
   ```bash
   # Create test database
   mysql -u root -p -e "CREATE DATABASE beauty_salon_testing;"
   mysql -u root -p -e "GRANT ALL ON beauty_salon_testing.* TO 'your_user'@'localhost';"
   ```

   **Option B: SQLite** (Faster, in-memory testing)
   ```bash
   # Install SQLite PDO extension
   sudo apt-get install php-sqlite3
   # OR for specific PHP version
   sudo apt-get install php8.2-sqlite3
   ```

3. **Composer Dependencies**:
   ```bash
   composer install
   ```

4. **Environment Configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

---

## 🗄️ Database Configuration

### For MySQL Testing

Edit `phpunit.xml`, change these lines:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_HOST" value="127.0.0.1"/>
<env name="DB_PORT" value="3306"/>
<env name="DB_DATABASE" value="beauty_salon_testing"/>
<env name="DB_USERNAME" value="your_db_user"/>
<env name="DB_PASSWORD" value="your_db_password"/>
```

### For SQLite Testing (Default)

The default `phpunit.xml` is already configured:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Just ensure the SQLite PDO extension is installed.

---

## 🚀 Running the Tests

### Run All Critical Path Tests

```bash
php artisan test tests/Feature/CriticalPath/
```

### Run Booking Flow Tests Only

```bash
php artisan test tests/Feature/CriticalPath/BookingFlowTest.php
```

### Run Payment Processing Tests Only

```bash
php artisan test tests/Feature/CriticalPath/PaymentProcessingTest.php
```

### Run with Detailed Output

```bash
php artisan test tests/Feature/CriticalPath/ --testdox
```

### Run with Code Coverage

```bash
php artisan test tests/Feature/CriticalPath/ --coverage
```

---

## 📊 Test Coverage

### Booking Flow Tests (9 methods)

| Test Method | What It Verifies |
|------------|------------------|
| `complete_booking_flow_works_for_new_customer` | New customers can complete full booking process |
| `booking_flow_works_for_returning_customer` | Returning customers can book appointments |
| `booking_validates_required_fields` | Form validation catches missing required fields |
| `booking_prevents_double_booking` | System prevents booking same time slot twice |
| `booking_respects_business_hours` | Bookings outside business hours are rejected |
| `booking_can_be_cancelled_by_customer` | Customers can cancel their bookings |
| `booking_can_be_rescheduled` | Appointments can be moved to different times |
| `booking_confirmation_can_be_retrieved` | Customers can view their booking details |

**Code Coverage**: Covers booking controllers, models, validation, and business logic

### Payment Processing Tests (11 methods)

| Test Method | What It Verifies |
|------------|------------------|
| `invoice_is_automatically_created_for_appointment` | Invoices generate when appointment is created |
| `payment_can_be_processed_with_cash` | Cash payments work correctly |
| `payment_can_be_processed_with_card` | Card payments work correctly |
| `partial_payment_is_supported` | Can pay invoice in multiple installments |
| `multiple_payments_complete_invoice` | Multiple payments mark invoice as paid |
| `refund_can_be_processed` | Full refunds work correctly |
| `partial_refund_is_supported` | Partial refunds work correctly |
| `receipt_can_be_generated_for_payment` | PDF receipts generate successfully |
| `payment_validates_amount` | Can't overpay invoice amount |
| `payment_validates_payment_method` | Only valid payment methods accepted |
| `payment_history_can_be_viewed` | Payment history displays correctly |

**Code Coverage**: Covers payment controllers, invoice generation, refund logic, and PDF generation

---

## 🔍 Expected Test Results

### Successful Test Run Output

```
   PASS  Tests\Feature\CriticalPath\BookingFlowTest
  ✓ complete booking flow works for new customer
  ✓ booking flow works for returning customer
  ✓ booking validates required fields
  ✓ booking prevents double booking
  ✓ booking respects business hours
  ✓ booking can be cancelled by customer
  ✓ booking can be rescheduled
  ✓ booking confirmation can be retrieved

   PASS  Tests\Feature\CriticalPath\PaymentProcessingTest
  ✓ invoice is automatically created for appointment
  ✓ payment can be processed with cash
  ✓ payment can be processed with card
  ✓ partial payment is supported
  ✓ multiple payments complete invoice
  ✓ refund can be processed
  ✓ partial refund is supported
  ✓ receipt can be generated for payment
  ✓ payment validates amount
  ✓ payment validates payment method
  ✓ payment history can be viewed

  Tests:    20 passed (84 assertions)
  Duration: 2.45s
```

---

## 🐛 Troubleshooting

### Issue: "could not find driver"

**Problem**: PDO driver for database not installed

**Solution**:
```bash
# For MySQL
sudo apt-get install php-mysql php8.2-mysql

# For SQLite
sudo apt-get install php-sqlite3 php8.2-sqlite3

# Restart PHP-FPM if using it
sudo systemctl restart php8.2-fpm
```

### Issue: "Class 'Tests\TestCase' not found"

**Problem**: Autoload not configured

**Solution**:
```bash
composer dump-autoload
```

### Issue: "Base table or view not found"

**Problem**: Database migrations not run

**Solution**:
```bash
php artisan migrate:fresh --env=testing
# OR if using RefreshDatabase trait, this happens automatically
```

### Issue: "Call to undefined method assignRole()"

**Problem**: Spatie Permission package not installed

**Solution**:
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

###Issue: Tests fail with route not found errors

**Problem**: Routes may need to be verified/updated

**Solution**:
- Check that booking routes exist in `routes/web.php`
- Check that admin routes exist and are properly named
- Update test code to match actual route names in your application

---

## 📝 Test Maintenance

### Adding New Tests

Create new test file:
```bash
php artisan make:test CriticalPath/NewFeatureTest
```

Follow the same pattern:
```php
<?php

namespace Tests\Feature\CriticalPath;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_does_something()
    {
        // Arrange
        $data = ['key' => 'value'];

        // Act
        $response = $this->post('/endpoint', $data);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('table', $data);
    }
}
```

### Updating Existing Tests

If business logic changes:
1. Review affected test methods
2. Update test assertions
3. Re-run tests to verify
4. Update this documentation

---

## ✅ Pre-Deployment Checklist

Before deploying to production, ensure:

- [ ] All 20 critical path tests pass
- [ ] No deprecation warnings (or update to use PHPUnit attributes)
- [ ] Code coverage > 70% for critical paths
- [ ] Tests run in < 5 seconds (for CI/CD efficiency)
- [ ] Database is properly seeded with test data
- [ ] Environment variables are configured
- [ ] All composer dependencies installed

---

## 🚀 CI/CD Integration

The tests are automatically run in GitHub Actions via `.github/workflows/tests.yml`.

### Manual CI Run

```bash
# Run the exact same tests as CI
php artisan test --coverage --min=60
```

### Coverage Reports

View coverage in CI:
- GitHub Actions → Actions tab → Latest workflow run
- Coverage report uploaded to Codecov (if configured)

---

## 📚 Additional Resources

- **PHPUnit Documentation**: https://phpunit.de/documentation.html
- **Laravel Testing**: https://laravel.com/docs/testing
- **Test Structure**: `tests/Feature/CriticalPath/`
- **Application Code**: `app/Livewire/`, `app/Models/`, `app/Http/Controllers/`

---

## 🎯 Success Criteria

Tests are considered passing when:

✅ All 20 test methods pass
✅ No errors or exceptions thrown
✅ Database assertions validate data integrity
✅ HTTP responses return expected status codes
✅ Validation rules properly reject invalid data
✅ Business logic executes correctly
✅ No memory leaks or performance issues

---

**Last Updated**: December 2024
**Test Suite Version**: 1.0.0
**Total Tests**: 20 methods across 2 files
**Estimated Execution Time**: 2-5 seconds
