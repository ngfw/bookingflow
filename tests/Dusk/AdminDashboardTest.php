<?php

namespace Tests\Dusk;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Location;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AdminDashboardTest extends BaseTestCase
{
    use DatabaseMigrations;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }

    /** @test */
    public function admin_can_access_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Admin Dashboard')
                    ->assertSee('Overview')
                    ->assertSee('Statistics')
                    ->assertSee('Recent Activity');
        });
    }

    /** @test */
    public function admin_can_view_dashboard_statistics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $clients = Client::factory()->count(5)->create();
        $staff = Staff::factory()->count(3)->create();
        $services = Service::factory()->count(8)->create();
        $appointments = Appointment::factory()->count(12)->create();

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Total Clients')
                    ->assertSee('Total Staff')
                    ->assertSee('Total Services')
                    ->assertSee('Total Appointments')
                    ->assertSee('5')
                    ->assertSee('3')
                    ->assertSee('8')
                    ->assertSee('12');
        });
    }

    /** @test */
    public function admin_can_view_recent_appointments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointments = Appointment::factory()->count(5)->create();

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Recent Appointments')
                    ->assertSee('View All Appointments');
        });
    }

    /** @test */
    public function admin_can_view_recent_clients()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $clients = Client::factory()->count(5)->create();

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Recent Clients')
                    ->assertSee('View All Clients');
        });
    }

    /** @test */
    public function admin_can_view_revenue_chart()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Revenue Chart')
                    ->assertSee('This Month')
                    ->assertSee('Last Month')
                    ->assertSee('This Year');
        });
    }

    /** @test */
    public function admin_can_view_appointment_status_chart()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Appointment Status')
                    ->assertSee('Scheduled')
                    ->assertSee('Completed')
                    ->assertSee('Cancelled');
        });
    }

    /** @test */
    public function admin_can_view_popular_services()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['is_popular' => true]);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Popular Services')
                    ->assertSee($service->name);
        });
    }

    /** @test */
    public function admin_can_view_staff_performance()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create();

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Staff Performance')
                    ->assertSee('Top Performers');
        });
    }

    /** @test */
    public function admin_can_view_system_alerts()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('System Alerts')
                    ->assertSee('No alerts');
        });
    }

    /** @test */
    public function admin_can_view_quick_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Quick Actions')
                    ->assertSee('Add Client')
                    ->assertSee('Add Staff')
                    ->assertSee('Add Service')
                    ->assertSee('Book Appointment');
        });
    }

    /** @test */
    public function admin_can_navigate_to_clients()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@clients-menu')
                    ->assertPathIs('/admin/clients')
                    ->assertSee('Clients');
        });
    }

    /** @test */
    public function admin_can_navigate_to_staff()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@staff-menu')
                    ->assertPathIs('/admin/staff')
                    ->assertSee('Staff');
        });
    }

    /** @test */
    public function admin_can_navigate_to_services()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@services-menu')
                    ->assertPathIs('/admin/services')
                    ->assertSee('Services');
        });
    }

    /** @test */
    public function admin_can_navigate_to_appointments()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@appointments-menu')
                    ->assertPathIs('/admin/appointments')
                    ->assertSee('Appointments');
        });
    }

    /** @test */
    public function admin_can_navigate_to_reports()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@reports-menu')
                    ->assertPathIs('/admin/reports')
                    ->assertSee('Reports');
        });
    }

    /** @test */
    public function admin_can_navigate_to_settings()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@settings-menu')
                    ->assertPathIs('/admin/settings')
                    ->assertSee('Settings');
        });
    }

    /** @test */
    public function admin_can_view_notifications()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@notifications-bell')
                    ->assertSee('Notifications')
                    ->assertSee('Mark All as Read');
        });
    }

    /** @test */
    public function admin_can_view_profile_menu()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@profile-menu')
                    ->assertSee('Profile')
                    ->assertSee('Settings')
                    ->assertSee('Logout');
        });
    }

    /** @test */
    public function admin_can_search_globally()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $service = Service::factory()->create();

        $this->browse(function ($browser) use ($admin, $client, $service) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->type('@global-search', $client->user->name)
                    ->press('@search-button')
                    ->assertSee('Search Results')
                    ->assertSee($client->user->name);
        });
    }

    /** @test */
    public function admin_can_view_activity_feed()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Activity Feed')
                    ->assertSee('Recent Activity');
        });
    }

    /** @test */
    public function admin_can_view_system_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('System Status')
                    ->assertSee('Online')
                    ->assertSee('Database')
                    ->assertSee('Storage');
        });
    }

    /** @test */
    public function admin_can_view_backup_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Backup Status')
                    ->assertSee('Last Backup')
                    ->assertSee('Next Backup');
        });
    }

    /** @test */
    public function admin_can_view_security_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Security Status')
                    ->assertSee('2FA Enabled')
                    ->assertSee('SSL Certificate');
        });
    }

    /** @test */
    public function admin_can_view_license_info()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('License Information')
                    ->assertSee('Version')
                    ->assertSee('Expires');
        });
    }

    /** @test */
    public function admin_can_view_help_resources()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Help & Resources')
                    ->assertSee('Documentation')
                    ->assertSee('Support')
                    ->assertSee('Training');
        });
    }

    /** @test */
    public function admin_can_view_announcements()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Announcements')
                    ->assertSee('System Updates');
        });
    }

    /** @test */
    public function admin_can_view_tips_and_tricks()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Tips & Tricks')
                    ->assertSee('Productivity Tips');
        });
    }

    /** @test */
    public function admin_can_view_weather_widget()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Weather')
                    ->assertSee('Current Weather');
        });
    }

    /** @test */
    public function admin_can_view_calendar_widget()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Calendar')
                    ->assertSee('Today')
                    ->assertSee('This Week');
        });
    }

    /** @test */
    public function admin_can_view_news_widget()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Industry News')
                    ->assertSee('Latest Updates');
        });
    }

    /** @test */
    public function admin_can_customize_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@customize-dashboard')
                    ->assertSee('Customize Dashboard')
                    ->assertSee('Widgets')
                    ->assertSee('Layout');
        });
    }

    /** @test */
    public function admin_can_add_widget_to_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@customize-dashboard')
                    ->click('@add-widget')
                    ->select('widget_type', 'revenue_chart')
                    ->press('ADD WIDGET')
                    ->assertSee('Widget added successfully');
        });
    }

    /** @test */
    public function admin_can_remove_widget_from_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@remove-widget-revenue')
                    ->assertSee('Widget removed successfully');
        });
    }

    /** @test */
    public function admin_can_reorder_dashboard_widgets()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@customize-dashboard')
                    ->drag('@widget-revenue', '@widget-appointments')
                    ->press('SAVE LAYOUT')
                    ->assertSee('Layout saved successfully');
        });
    }

    /** @test */
    public function admin_can_export_dashboard_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@export-dashboard')
                    ->assertSee('Export Dashboard Data')
                    ->select('format', 'pdf')
                    ->press('EXPORT')
                    ->assertSee('Export completed successfully');
        });
    }

    /** @test */
    public function admin_can_share_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@share-dashboard')
                    ->assertSee('Share Dashboard')
                    ->assertSee('Generate Link')
                    ->assertSee('Share via Email');
        });
    }

    /** @test */
    public function admin_can_view_dashboard_analytics()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@dashboard-analytics')
                    ->assertSee('Dashboard Analytics')
                    ->assertSee('Page Views')
                    ->assertSee('User Engagement');
        });
    }

    /** @test */
    public function admin_can_set_dashboard_refresh_interval()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@dashboard-settings')
                    ->select('refresh_interval', '30')
                    ->press('SAVE SETTINGS')
                    ->assertSee('Settings saved successfully');
        });
    }

    /** @test */
    public function admin_can_view_dashboard_help()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('@dashboard-help')
                    ->assertSee('Dashboard Help')
                    ->assertSee('Getting Started')
                    ->assertSee('Widget Guide');
        });
    }
}
