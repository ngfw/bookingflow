<?php

namespace Tests\Dusk;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends BaseTestCase
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
    public function user_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('LOGIN')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Dashboard');
        });
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'wrongpassword')
                    ->press('LOGIN')
                    ->assertPathIs('/login')
                    ->assertSee('These credentials do not match our records');
        });
    }

    /** @test */
    public function user_can_register_successfully()
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                    ->type('name', 'John Doe')
                    ->type('email', 'john@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->select('role', 'client')
                    ->press('REGISTER')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Welcome, John Doe');
        });
    }

    /** @test */
    public function user_cannot_register_with_existing_email()
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $this->browse(function ($browser) {
            $browser->visit('/register')
                    ->type('name', 'Jane Doe')
                    ->type('email', 'existing@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->select('role', 'client')
                    ->press('REGISTER')
                    ->assertPathIs('/register')
                    ->assertSee('The email has already been taken');
        });
    }

    /** @test */
    public function user_can_logout_successfully()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->click('@logout-button')
                    ->assertPathIs('/login')
                    ->assertSee('Login');
        });
    }

    /** @test */
    public function user_can_request_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/forgot-password')
                    ->type('email', $user->email)
                    ->press('SEND PASSWORD RESET LINK')
                    ->assertPathIs('/forgot-password')
                    ->assertSee('We have emailed your password reset link');
        });
    }

    /** @test */
    public function user_can_reset_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = app('auth.password.broker')->createToken($user);

        $this->browse(function ($browser) use ($user, $token) {
            $browser->visit('/reset-password/' . $token)
                    ->type('email', $user->email)
                    ->type('password', 'newpassword123')
                    ->type('password_confirmation', 'newpassword123')
                    ->press('RESET PASSWORD')
                    ->assertPathIs('/login')
                    ->assertSee('Your password has been reset');
        });
    }

    /** @test */
    public function user_can_view_profile()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertSee('Profile')
                    ->assertSee($user->name)
                    ->assertSee($user->email);
        });
    }

    /** @test */
    public function user_can_update_profile()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->type('name', 'Updated Name')
                    ->type('phone', '1234567890')
                    ->press('UPDATE PROFILE')
                    ->assertSee('Profile updated successfully')
                    ->assertSee('Updated Name');
        });
    }

    /** @test */
    public function user_can_change_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@change-password-tab')
                    ->type('current_password', 'oldpassword')
                    ->type('password', 'newpassword123')
                    ->type('password_confirmation', 'newpassword123')
                    ->press('CHANGE PASSWORD')
                    ->assertSee('Password changed successfully');
        });
    }

    /** @test */
    public function user_can_enable_two_factor_authentication()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@two-factor-tab')
                    ->press('ENABLE TWO-FACTOR AUTHENTICATION')
                    ->assertSee('Two-factor authentication enabled')
                    ->assertSee('Recovery Codes');
        });
    }

    /** @test */
    public function user_can_disable_two_factor_authentication()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'two_factor_secret' => 'secret',
            'two_factor_confirmed_at' => now(),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@two-factor-tab')
                    ->press('DISABLE TWO-FACTOR AUTHENTICATION')
                    ->assertSee('Two-factor authentication disabled');
        });
    }

    /** @test */
    public function user_can_create_api_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@api-tokens-tab')
                    ->type('name', 'Test Token')
                    ->check('abilities[read]')
                    ->press('CREATE TOKEN')
                    ->assertSee('Token created successfully')
                    ->assertSee('Test Token');
        });
    }

    /** @test */
    public function user_can_delete_api_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $token = $user->createToken('Test Token');

        $this->browse(function ($browser) use ($user, $token) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@api-tokens-tab')
                    ->press('@delete-token-' . $token->accessToken->id)
                    ->assertSee('Token deleted successfully');
        });
    }

    /** @test */
    public function user_can_view_notifications()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/notifications')
                    ->assertSee('Notifications')
                    ->assertSee('Test notification');
        });
    }

    /** @test */
    public function user_can_mark_notification_as_read()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $notification = $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $this->browse(function ($browser) use ($user, $notification) {
            $browser->loginAs($user)
                    ->visit('/notifications')
                    ->press('@mark-read-' . $notification->id)
                    ->assertSee('Notification marked as read');
        });
    }

    /** @test */
    public function user_can_delete_notification()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $notification = $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $this->browse(function ($browser) use ($user, $notification) {
            $browser->loginAs($user)
                    ->visit('/notifications')
                    ->press('@delete-notification-' . $notification->id)
                    ->assertSee('Notification deleted');
        });
    }

    /** @test */
    public function user_can_view_sessions()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@sessions-tab')
                    ->assertSee('Browser Sessions')
                    ->assertSee('Current Session');
        });
    }

    /** @test */
    public function user_can_logout_other_sessions()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@sessions-tab')
                    ->press('LOGOUT OTHER BROWSER SESSIONS')
                    ->assertSee('Other browser sessions have been logged out');
        });
    }

    /** @test */
    public function user_can_delete_account()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@delete-account-tab')
                    ->type('password', 'password')
                    ->press('DELETE ACCOUNT')
                    ->assertPathIs('/login')
                    ->assertSee('Your account has been deleted');
        });
    }

    /** @test */
    public function user_cannot_delete_account_with_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->click('@delete-account-tab')
                    ->type('password', 'wrongpassword')
                    ->press('DELETE ACCOUNT')
                    ->assertSee('The password is incorrect');
        });
    }

    /** @test */
    public function user_can_view_help_documentation()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/help')
                    ->assertSee('Help & Documentation')
                    ->assertSee('Getting Started')
                    ->assertSee('User Guide');
        });
    }

    /** @test */
    public function user_can_search_help_documentation()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/help')
                    ->type('search', 'appointments')
                    ->press('SEARCH')
                    ->assertSee('Search Results')
                    ->assertSee('appointments');
        });
    }

    /** @test */
    public function user_can_contact_support()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/support')
                    ->type('subject', 'Test Support Request')
                    ->type('message', 'This is a test support request')
                    ->select('priority', 'medium')
                    ->press('SEND MESSAGE')
                    ->assertSee('Support request sent successfully');
        });
    }

    /** @test */
    public function user_can_view_support_tickets()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/support/tickets')
                    ->assertSee('Support Tickets')
                    ->assertSee('Create New Ticket');
        });
    }

    /** @test */
    public function user_can_view_support_ticket_details()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $ticket = \App\Models\SupportTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->browse(function ($browser) use ($user, $ticket) {
            $browser->loginAs($user)
                    ->visit('/support/tickets/' . $ticket->id)
                    ->assertSee('Ticket Details')
                    ->assertSee($ticket->subject);
        });
    }

    /** @test */
    public function user_can_reply_to_support_ticket()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $ticket = \App\Models\SupportTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->browse(function ($browser) use ($user, $ticket) {
            $browser->loginAs($user)
                    ->visit('/support/tickets/' . $ticket->id)
                    ->type('message', 'This is a reply to the support ticket')
                    ->press('SEND REPLY')
                    ->assertSee('Reply sent successfully');
        });
    }

    /** @test */
    public function user_can_close_support_ticket()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $ticket = \App\Models\SupportTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $this->browse(function ($browser) use ($user, $ticket) {
            $browser->loginAs($user)
                    ->visit('/support/tickets/' . $ticket->id)
                    ->press('CLOSE TICKET')
                    ->assertSee('Ticket closed successfully');
        });
    }
}
