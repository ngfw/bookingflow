<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class EnhancedPortal extends Component
{
    // Modal states
    public $showBookingModal = false;
    public $showManageBooking = false;
    public $showProfile = false;
    public $showLoyalty = false;

    // Booking form
    public $selectedService = '';
    public $preferredDate = '';
    public $preferredTime = '';
    public $specialRequests = '';

    // Data properties
    public $upcomingAppointments;
    public $recentServices;
    public $loyaltyPoints = 0;
    public $totalAppointments = 0;
    public $totalSpent = 0;
    public $favoriteService = '';
    public $memberSince = '';
    public $services;

    public function mount()
    {
        $this->loadClientData();
        $this->loadServices();
    }

    public function loadClientData()
    {
        $user = auth()->user();
        $client = $user->client;

        if ($client) {
            // Load upcoming appointments
            $this->upcomingAppointments = Appointment::where('client_id', $client->id)
                ->where('appointment_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->with(['service', 'staff.user'])
                ->orderBy('appointment_date')
                ->limit(3)
                ->get();

            // Load recent services
            $this->recentServices = Appointment::where('client_id', $client->id)
                ->where('status', 'completed')
                ->with(['service'])
                ->orderBy('appointment_date', 'desc')
                ->limit(5)
                ->get();

            // Load client stats
            $this->loyaltyPoints = $client->loyalty_points ?? 0;
            $this->totalAppointments = Appointment::where('client_id', $client->id)
                ->where('status', 'completed')
                ->count();
            $this->totalSpent = Appointment::where('client_id', $client->id)
                ->where('status', 'completed')
                ->sum('total_price');

            // Get favorite service
            $favoriteService = Appointment::where('client_id', $client->id)
                ->where('status', 'completed')
                ->selectRaw('service_id, COUNT(*) as count')
                ->groupBy('service_id')
                ->orderBy('count', 'desc')
                ->first();

            if ($favoriteService) {
                $service = Service::find($favoriteService->service_id);
                $this->favoriteService = $service ? $service->name : 'N/A';
            } else {
                $this->favoriteService = 'N/A';
            }

            // Member since
            $this->memberSince = $user->created_at->format('M Y');
        }
    }

    public function loadServices()
    {
        $this->services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function showBookingModal()
    {
        $this->showBookingModal = true;
        $this->resetBookingForm();
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->resetBookingForm();
    }

    public function resetBookingForm()
    {
        $this->selectedService = '';
        $this->preferredDate = '';
        $this->preferredTime = '';
        $this->specialRequests = '';
    }

    public function submitBooking()
    {
        $this->validate([
            'selectedService' => 'required|exists:services,id',
            'preferredDate' => 'required|date|after_or_equal:today',
            'preferredTime' => 'required',
        ]);

        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            session()->flash('error', 'Client profile not found.');
            return;
        }

        // Create appointment
        $appointmentDateTime = Carbon::parse($this->preferredDate . ' ' . $this->preferredTime);
        
        $appointment = Appointment::create([
            'client_id' => $client->id,
            'service_id' => $this->selectedService,
            'staff_id' => 1, // Default staff - in real app, you'd select or assign
            'appointment_date' => $appointmentDateTime,
            'notes' => $this->specialRequests,
            'status' => 'pending',
            'total_price' => Service::find($this->selectedService)->price,
            'appointment_number' => 'APT' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        ]);

        // Add loyalty points
        $client->increment('loyalty_points', 10);

        session()->flash('success', 'Appointment booked successfully!');
        $this->closeBookingModal();
        $this->loadClientData();
    }

    public function showManageBooking()
    {
        $this->showManageBooking = true;
    }

    public function showProfile()
    {
        $this->showProfile = true;
    }

    public function showLoyalty()
    {
        $this->showLoyalty = true;
    }

    public function showAllAppointments()
    {
        return redirect()->route('client.appointments');
    }

    public function showServiceHistory()
    {
        return redirect()->route('client.history');
    }

    public function rescheduleAppointment($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        if ($appointment) {
            // Implement rescheduling logic
            session()->flash('info', 'Rescheduling feature coming soon!');
        }
    }

    public function viewAppointment($appointmentId)
    {
        return redirect()->route('client.appointment.show', $appointmentId);
    }

    public function redeemReward($rewardType)
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            session()->flash('error', 'Client profile not found.');
            return;
        }

        $rewardCosts = [
            'free_service' => 500,
            'discount' => 200,
            'upgrade' => 1000,
        ];

        $cost = $rewardCosts[$rewardType] ?? 0;

        if ($client->loyalty_points >= $cost) {
            $client->decrement('loyalty_points', $cost);
            
            // Create reward record (you'd have a rewards table)
            session()->flash('success', 'Reward redeemed successfully!');
            $this->loadClientData();
        } else {
            session()->flash('error', 'Insufficient loyalty points for this reward.');
        }
    }

    public function render()
    {
        return view('livewire.client.enhanced-portal');
    }
}
