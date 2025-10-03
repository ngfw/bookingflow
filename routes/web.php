<?php

use Illuminate\Support\Facades\Route;

// Dynamic homepage
Route::get('/', [App\Http\Controllers\DynamicPageController::class, 'homepage'])->name('home');

// Mobile-optimized booking route  
Route::get('/book', \App\Livewire\Public\MobileBooking::class)->name('booking');

// Separate routes for mobile and desktop booking
Route::get('/mobile/book', \App\Livewire\Public\MobileBooking::class)->name('mobile.booking');
Route::get('/desktop/book', \App\Livewire\Public\Booking::class)->name('desktop.booking');

// Mobile-optimized manage booking route
Route::get('/manage-booking', function () {
    if (isMobile()) {
        return view('livewire.public.mobile-manage-booking');
    }
    return view('livewire.public.manage-booking');
})->name('manage-booking');

// Mobile check-in route
Route::get('/check-in', function () {
    if (isMobile()) {
        return view('livewire.public.mobile-check-in');
    }
    return view('livewire.public.mobile-check-in'); // Use mobile version for all devices
})->name('check-in');

// Mobile payment route
Route::get('/payment', \App\Livewire\Public\MobilePayment::class)->name('payment');

// Mobile location services route
Route::get('/location', \App\Livewire\Public\MobileLocationServices::class)->name('location');

// Redirect authenticated users to appropriate dashboard based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user && $user->hasRole('staff')) {
        return redirect()->route('staff.dashboard');
    }
    // Client dashboard
    return redirect()->route('client.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Client Dashboard
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Client\Dashboard::class)->name('dashboard');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Admin Routes
// Staff Portal Routes
Route::middleware(['auth', 'role:staff|admin|super_admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Staff\Dashboard::class)->name('dashboard')->middleware('permission:view_dashboard');
    Route::get('/appointments', \App\Livewire\Staff\Appointments::class)->name('appointments')->middleware('permission:view_appointments');
    Route::get('/schedule', \App\Livewire\Staff\Schedule::class)->name('schedule')->middleware('permission:view_appointments');
    Route::get('/clients', \App\Livewire\Staff\Clients::class)->name('clients')->middleware('permission:view_clients');
    Route::get('/performance', \App\Livewire\Staff\Performance::class)->name('performance')->middleware('permission:view_dashboard');
    Route::get('/pos', \App\Livewire\Staff\POS::class)->name('pos')->middleware('permission:use_pos');
});

Route::middleware(['auth', 'role:admin|super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard')->middleware('permission:view_dashboard');
    
    // Client Management
    Route::get('/clients', \App\Livewire\Admin\Clients\Index::class)->name('clients.index')->middleware('permission:view_clients');
    Route::get('/clients/create', \App\Livewire\Admin\Clients\Create::class)->name('clients.create')->middleware('permission:create_clients');
    Route::get('/clients/{client}/edit', \App\Livewire\Admin\Clients\Edit::class)->name('clients.edit')->middleware('permission:edit_clients');
    
    // Service Management
    Route::get('/services', \App\Livewire\Admin\Services\Index::class)->name('services.index')->middleware('permission:view_services');
    Route::get('/services/create', \App\Livewire\Admin\Services\Create::class)->name('services.create')->middleware('permission:create_services');
    Route::get('/services/{service}/edit', \App\Livewire\Admin\Services\Edit::class)->name('services.edit')->middleware('permission:edit_services');
    
    // Category Management
    Route::get('/categories', \App\Livewire\Admin\Categories\Index::class)->name('categories.index')->middleware('permission:view_services');
    
    // Staff Management
    Route::get('/staff', \App\Livewire\Admin\Staff\Index::class)->name('staff.index')->middleware('permission:view_staff');
    Route::get('/staff/create', \App\Livewire\Admin\Staff\Create::class)->name('staff.create')->middleware('permission:create_staff');
    Route::get('/staff/{staff}/edit', \App\Livewire\Admin\Staff\Edit::class)->name('staff.edit')->middleware('permission:edit_staff');
    // Staff schedule and performance routes
    Route::get('/staff/schedule', \App\Livewire\Admin\Staff\StaffSchedule::class)->name('staff.schedule')->middleware('permission:manage_staff_schedules');
    Route::get('/staff/schedule/mobile', \App\Livewire\Admin\Staff\MobileStaffSchedule::class)->name('staff.schedule.mobile')->middleware('permission:manage_staff_schedules');
    Route::get('/staff/performance', \App\Livewire\Admin\Staff\StaffPerformanceTracking::class)->name('staff.performance')->middleware('permission:view_staff_performance');
    Route::get('/staff/performance/mobile', \App\Livewire\Admin\Staff\MobileStaffPerformance::class)->name('staff.performance.mobile')->middleware('permission:view_staff_performance');
    Route::get('/staff/commission', \App\Livewire\Admin\Staff\StaffCommissionSettings::class)->name('staff.commission')->middleware('permission:manage_staff_payroll');
    Route::get('/staff/payroll', \App\Livewire\Admin\Staff\StaffPayrollManagement::class)->name('staff.payroll')->middleware('permission:manage_staff_payroll');
    
        // Appointment Management
        Route::get('/appointments', \App\Livewire\Admin\Appointments\Index::class)->name('appointments.index');
        Route::get('/appointments/create', \App\Livewire\Admin\Appointments\Create::class)->name('appointments.create');
        Route::get('/appointments/{appointment}/edit', \App\Livewire\Admin\Appointments\Edit::class)->name('appointments.edit');
        Route::get('/appointments/calendar', \App\Livewire\Admin\Appointments\Calendar::class)->name('appointments.calendar');
        Route::get('/appointments/reminders', \App\Livewire\Admin\Appointments\Reminders::class)->name('appointments.reminders');

        // Waitlist Management
        Route::get('/waitlist', \App\Livewire\Admin\Waitlist\Index::class)->name('waitlist.index');

        // Inventory Management
        Route::get('/inventory', \App\Livewire\Admin\Inventory\Index::class)->name('inventory.index');
        Route::get('/inventory/create', \App\Livewire\Admin\Inventory\Create::class)->name('inventory.create');
        Route::get('/inventory/{product}/edit', \App\Livewire\Admin\Inventory\Edit::class)->name('inventory.edit');
        Route::get('/inventory/usage', \App\Livewire\Admin\Inventory\ProductUsageTracking::class)->name('inventory.usage');
        Route::get('/inventory/reports', \App\Livewire\Admin\Inventory\Reports::class)->name('inventory.reports');
        Route::get('/inventory/scanner', \App\Livewire\Admin\Inventory\BarcodeScanner::class)->name('inventory.scanner');

        // Supplier Management
        Route::get('/suppliers', \App\Livewire\Admin\Suppliers\Index::class)->name('suppliers.index');
        Route::get('/suppliers/create', \App\Livewire\Admin\Suppliers\Create::class)->name('suppliers.create');
        Route::get('/suppliers/{supplier}/edit', \App\Livewire\Admin\Suppliers\Edit::class)->name('suppliers.edit');

        // Purchase Order Management
        Route::get('/purchase-orders', \App\Livewire\Admin\PurchaseOrders\Index::class)->name('purchase-orders.index');
        Route::get('/purchase-orders/create', \App\Livewire\Admin\PurchaseOrders\Create::class)->name('purchase-orders.create');

        // Invoice Management
        Route::get('/invoices', \App\Livewire\Admin\Invoices\Index::class)->name('invoices.index');
        Route::get('/invoices/create', \App\Livewire\Admin\Invoices\Create::class)->name('invoices.create');

        // Payment Management
        Route::get('/payments', \App\Livewire\Admin\Payments\Index::class)->name('payments.index');
        Route::get('/payments/process', \App\Livewire\Admin\Payments\Process::class)->name('payments.process');
        Route::get('/payments/receipt', \App\Livewire\Admin\Payments\Receipt::class)->name('payments.receipt');
        Route::get('/payments/refunds', \App\Livewire\Admin\Payments\Refunds::class)->name('payments.refunds');

        // Locations
        Route::get('/locations', \App\Livewire\Admin\Locations\Index::class)->name('locations.index');
        
        // Franchises
        Route::get('/franchises', \App\Livewire\Admin\Franchises\Index::class)->name('franchises.index');
        
        // Page Management
        Route::get('/pages', \App\Livewire\Admin\Pages\Index::class)->name('pages.index');
        Route::get('/pages/create', \App\Livewire\Admin\Pages\Create::class)->name('pages.create');
        Route::get('/pages/{page}/edit', \App\Livewire\Admin\Pages\Edit::class)->name('pages.edit');
        
        // Settings (Super Admin Only)
        Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings.index')->middleware('role:super_admin');
        
        // Reports & Analytics (Super Admin Only)
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/reports', \App\Livewire\Admin\Reports\Dashboard::class)->name('reports.dashboard');
            Route::get('/reports/financial', \App\Livewire\Admin\Reports\Financial::class)->name('reports.financial');
            Route::get('/reports/appointments', \App\Livewire\Admin\Reports\Appointments::class)->name('reports.appointments');
            Route::get('/reports/clients', \App\Livewire\Admin\Reports\ClientAnalytics::class)->name('reports.clients');
            Route::get('/reports/staff', \App\Livewire\Admin\Reports\StaffPerformance::class)->name('reports.staff');
            Route::get('/reports/business-intelligence', \App\Livewire\Admin\Reports\BusinessIntelligence::class)->name('reports.business-intelligence');
            Route::get('/reports/predictive-analytics', \App\Livewire\Admin\Reports\PredictiveAnalytics::class)->name('reports.predictive-analytics');
            Route::get('/reports/custom', \App\Livewire\Admin\Reports\CustomReports::class)->name('reports.custom');
            Route::get('/reports/export', \App\Livewire\Admin\Reports\DataExport::class)->name('reports.export');
        });

        // Point of Sale (POS)
        Route::get('/pos', \App\Livewire\Admin\POS\Index::class)->name('pos.index');
        Route::get('/pos/mobile', \App\Livewire\Admin\POS\MobilePOS::class)->name('pos.mobile');
        Route::get('/pos/catalog', \App\Livewire\Admin\POS\ProductCatalog::class)->name('pos.catalog');
        Route::get('/pos/promotions', \App\Livewire\Admin\POS\Promotions::class)->name('pos.promotions');
        Route::get('/pos/receipt', \App\Livewire\Admin\POS\Receipt::class)->name('pos.receipt');
        Route::get('/pos/cash-drawer', \App\Livewire\Admin\POS\CashDrawerManagement::class)->name('pos.cash-drawer');
        Route::get('/pos/daily-sales', \App\Livewire\Admin\POS\DailySalesReporting::class)->name('pos.daily-sales');

        // Promotions
        Route::get('/promotions/campaigns', \App\Livewire\Admin\Promotions\PromotionalCampaignManagement::class)->name('promotions.campaigns');

        // Notifications
        Route::get('/notifications', \App\Livewire\Admin\Notifications\NotificationManagement::class)->name('notifications.index');
        Route::get('/notifications/sms', \App\Livewire\Admin\Notifications\SmsManagement::class)->name('notifications.sms');
        Route::get('/notifications/reminders', \App\Livewire\Admin\Notifications\ReminderManagement::class)->name('notifications.reminders');
        Route::get('/notifications/campaigns', \App\Livewire\Admin\Notifications\EmailCampaignManagement::class)->name('notifications.campaigns');
        Route::get('/notifications/push', \App\Livewire\Admin\Notifications\PushNotificationManagement::class)->name('notifications.push');
        Route::get('/communication/history', \App\Livewire\Admin\Communication\ClientCommunicationHistoryManagement::class)->name('communication.history');
        Route::get('/notifications/preferences', \App\Livewire\Admin\Notifications\NotificationPreferencesManagement::class)->name('notifications.preferences');
        Route::get('/loyalty/points', \App\Livewire\Admin\Loyalty\LoyaltyPointsManagement::class)->name('loyalty.points');
        Route::get('/loyalty/rewards', \App\Livewire\Admin\Loyalty\RewardRedemptionManagement::class)->name('loyalty.rewards');
        Route::get('/referrals', \App\Livewire\Admin\Referrals\ReferralManagement::class)->name('referrals');
        Route::get('/membership/tiers', \App\Livewire\Admin\Membership\MembershipTierManagement::class)->name('membership.tiers');
        Route::get('/specials/birthday-anniversary', \App\Livewire\Admin\Specials\BirthdayAnniversarySpecialManagement::class)->name('specials.birthday-anniversary');
        
        // Content Management
        Route::get('/pages', \App\Livewire\Admin\PageManager::class)->name('pages.index');
        Route::get('/pages/create', \App\Livewire\Admin\PageEditor::class)->name('pages.create');
        Route::get('/pages/{page}/edit', \App\Livewire\Admin\PageEditor::class)->name('pages.edit');
        Route::get('/content', \App\Livewire\Admin\ContentManager::class)->name('content.index');
        Route::get('/seo', \App\Livewire\Admin\SEOManager::class)->name('seo.index');
        Route::get('/gallery', \App\Livewire\Admin\GalleryManager::class)->name('gallery.index');
        Route::get('/blog', \App\Livewire\Admin\BlogManager::class)->name('blog.index');
        Route::get('/social-media', \App\Livewire\Admin\SocialMediaManager::class)->name('social-media.index');
        Route::get('/analytics', \App\Livewire\Admin\AnalyticsDashboard::class)->name('analytics.index');

        // Contact Submissions
        Route::get('/contact-submissions', \App\Livewire\Admin\ContactSubmissions::class)->name('contact-submissions.index');

        // About Us Management
        Route::get('/about-us', \App\Livewire\Admin\AboutUsManager::class)->name('about-us.index');
});

// Dynamic pages
Route::get('/{slug}', [App\Http\Controllers\DynamicPageController::class, 'show'])->where('slug', '^(?!admin|api|book|manage-booking|check-in|payment|location|services|gallery|blog|contact|login|register|logout|forgot-password|reset-password|verify-email|confirm-password).*$');

// Static pages
Route::get('/services', [App\Http\Controllers\DynamicPageController::class, 'services'])->name('services');
Route::get('/gallery', [App\Http\Controllers\DynamicPageController::class, 'gallery'])->name('gallery');
Route::get('/blog', [App\Http\Controllers\DynamicPageController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [App\Http\Controllers\DynamicPageController::class, 'blogPost'])->name('blog.post');
Route::get('/contact', [App\Http\Controllers\DynamicPageController::class, 'contact'])->name('contact');
Route::get('/about', [App\Http\Controllers\DynamicPageController::class, 'about'])->name('about');

require __DIR__.'/auth.php';
