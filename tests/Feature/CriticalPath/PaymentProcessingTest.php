<?php

namespace Tests\Feature\CriticalPath;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * Critical Path Test: Payment Processing
 *
 * This test ensures the entire payment flow works end-to-end:
 * 1. Invoice is generated for appointment
 * 2. Payment can be processed
 * 3. Payment confirmation is stored
 * 4. Receipt can be generated
 * 5. Refunds work correctly
 */
class PaymentProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected $client;
    protected $staff;
    protected $service;
    protected $appointment;
    protected $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $user = User::factory()->create();
        $this->client = Client::factory()->create(['user_id' => $user->id]);

        $staffUser = User::factory()->create();
        $this->staff = Staff::factory()->create(['user_id' => $staffUser->id]);

        $this->service = Service::factory()->create([
            'name' => 'Haircut',
            'price' => 50.00,
            'duration_minutes' => 60,
        ]);

        $this->appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'staff_id' => $this->staff->id,
            'service_id' => $this->service->id,
            'appointment_date' => Carbon::now()->addDay(),
            'status' => 'confirmed',
        ]);

        $this->invoice = Invoice::factory()->create([
            'appointment_id' => $this->appointment->id,
            'client_id' => $this->client->id,
            'total_amount' => 50.00,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function invoice_is_automatically_created_for_appointment()
    {
        $newAppointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'staff_id' => $this->staff->id,
            'service_id' => $this->service->id,
            'appointment_date' => Carbon::now()->addDays(2),
            'status' => 'confirmed',
        ]);

        // Invoice should be created automatically (via observer or event)
        $this->assertDatabaseHas('invoices', [
            'appointment_id' => $newAppointment->id,
            'client_id' => $this->client->id,
        ]);
    }

    /** @test */
    public function payment_can_be_processed_with_cash()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        $response = $this->post('/admin/payments/process', [
            'invoice_id' => $this->invoice->id,
            'amount' => 50.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        // Verify payment was created
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
            'amount' => 50.00,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        // Verify invoice was marked as paid
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'paid',
        ]);
    }

    /** @test */
    public function payment_can_be_processed_with_card()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        $response = $this->post('/admin/payments/process', [
            'invoice_id' => $this->invoice->id,
            'amount' => 50.00,
            'payment_method' => 'card',
            'payment_date' => now()->format('Y-m-d'),
            'transaction_id' => 'TXN_' . uniqid(),
            'card_last_four' => '4242',
        ]);

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'card',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function partial_payment_is_supported()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        // Pay $30 out of $50
        $response = $this->post('/admin/payments/process', [
            'invoice_id' => $this->invoice->id,
            'amount' => 30.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        // Verify partial payment
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
            'amount' => 30.00,
            'status' => 'completed',
        ]);

        // Invoice should still be partially paid
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'partially_paid',
        ]);
    }

    /** @test */
    public function multiple_payments_complete_invoice()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        // First payment: $30
        $this->post('/admin/payments/process', [
            'invoice_id' => $this->invoice->id,
            'amount' => 30.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        // Second payment: $20
        $this->post('/admin/payments/process', [
            'invoice_id' => $this->invoice->id,
            'amount' => 20.00,
            'payment_method' => 'card',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        // Invoice should now be fully paid
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'paid',
        ]);

        // Two payments should exist
        $this->assertEquals(2, Payment::where('invoice_id', $this->invoice->id)->count());
    }

    /** @test */
    public function refund_can_be_processed()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        // Create payment first
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 50.00,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        // Update invoice status
        $this->invoice->update(['status' => 'paid']);

        // Process refund
        $response = $this->post("/admin/payments/{$payment->id}/refund", [
            'refund_amount' => 50.00,
            'refund_reason' => 'Customer request',
        ]);

        // Verify refund was recorded
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'refunded',
        ]);

        // Invoice should be marked as refunded
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'refunded',
        ]);
    }

    /** @test */
    public function partial_refund_is_supported()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 50.00,
            'payment_method' => 'card',
            'status' => 'completed',
        ]);

        $this->invoice->update(['status' => 'paid']);

        // Refund $20 out of $50
        $response = $this->post("/admin/payments/{$payment->id}/refund", [
            'refund_amount' => 20.00,
            'refund_reason' => 'Service issue',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'partially_refunded',
        ]);
    }

    /** @test */
    public function receipt_can_be_generated_for_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 50.00,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        // Generate receipt PDF
        $response = $this->get("/admin/payments/{$payment->id}/receipt");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function payment_validates_amount()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        // Try to pay more than invoice amount
        $response = $this->post('/admin/payments/process', [
            'invoice_id' => $this->invoice->id,
            'amount' => 100.00, // Invoice is only $50
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function payment_validates_payment_method()
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        $response = $this->post('/admin/payments/process', [
            'invoice_id' => $this->invoice->id,
            'amount' => 50.00,
            'payment_method' => 'invalid_method',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('payment_method');
    }

    /** @test */
    public function payment_history_can_be_viewed()
    {
        // Create multiple payments
        Payment::factory()->count(3)->create([
            'invoice_id' => $this->invoice->id,
        ]);

        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        $response = $this->get("/admin/invoices/{$this->invoice->id}");

        $response->assertStatus(200);
        $response->assertSee('Payment History');
    }

    /** @test */
    public function client_can_view_their_invoices()
    {
        $this->actingAs($this->client->user);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee($this->invoice->total_amount);
    }
}
