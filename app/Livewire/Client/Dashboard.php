<?php

namespace App\Livewire\Client;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Service;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $client;
    public $upcomingAppointments;
    public $recentAppointments;
    public $recentPayments;
    public $recommendedServices;
    public $loyaltyPoints = 0;
    public $totalSpent = 0;
    public $visitCount = 0;

    public function mount()
    {
        $user = Auth::user();

        // Get or create client record
        $this->client = Client::where('user_id', $user->id)->first();

        if (!$this->client) {
            $this->client = Client::create([
                'user_id' => $user->id,
                'loyalty_points' => 0,
                'total_spent' => 0,
                'visit_count' => 0,
            ]);
        }

        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Upcoming appointments
        $this->upcomingAppointments = Appointment::where('client_id', $this->client->id)
            ->where('appointment_date', '>=', now())
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['service', 'staff.user'])
            ->orderBy('appointment_date', 'asc')
            ->limit(5)
            ->get();

        // Recent appointment history
        $this->recentAppointments = Appointment::where('client_id', $this->client->id)
            ->where('appointment_date', '<', now())
            ->whereIn('status', ['completed', 'confirmed'])
            ->with(['service', 'staff.user'])
            ->orderBy('appointment_date', 'desc')
            ->limit(5)
            ->get();

        // Recent payments
        $this->recentPayments = Payment::where('client_id', $this->client->id)
            ->with('invoice')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        // Get service recommendations based on previous appointments
        $previousServiceIds = Appointment::where('client_id', $this->client->id)
            ->where('status', 'completed')
            ->pluck('service_id')
            ->unique()
            ->toArray();

        if (count($previousServiceIds) > 0) {
            // Get related services from the same categories
            $relatedCategories = Service::whereIn('id', $previousServiceIds)
                ->pluck('category_id')
                ->unique()
                ->toArray();

            $this->recommendedServices = Service::whereIn('category_id', $relatedCategories)
                ->whereNotIn('id', $previousServiceIds)
                ->where('is_active', true)
                ->limit(4)
                ->get();
        } else {
            // If no history, show popular/featured services
            $this->recommendedServices = Service::where('is_active', true)
                ->limit(4)
                ->get();
        }

        // Update stats
        $this->loyaltyPoints = $this->client->loyalty_points ?? 0;
        $this->totalSpent = $this->client->total_spent ?? 0;
        $this->visitCount = $this->client->visit_count ?? 0;
    }

    public function render()
    {
        return view('livewire.client.dashboard')->layout('layouts.app');
    }
}
