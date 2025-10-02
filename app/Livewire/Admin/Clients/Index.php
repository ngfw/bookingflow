<?php

namespace App\Livewire\Admin\Clients;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\User;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $showFilters = false;
    public $filters = [
        'status' => '',
        'loyalty_points_min' => '',
        'visit_count_min' => '',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->filters = [
            'status' => '',
            'loyalty_points_min' => '',
            'visit_count_min' => '',
        ];
        $this->resetPage();
    }

    public function deleteClient($clientId)
    {
        $client = Client::findOrFail($clientId);
        
        // Check if client has appointments
        if ($client->appointments()->count() > 0) {
            session()->flash('error', 'Cannot delete client with existing appointments.');
            return;
        }

        // Delete associated user
        $client->user()->delete();
        
        session()->flash('success', 'Client deleted successfully.');
    }

    public function render()
    {
        $query = Client::with(['user', 'appointments'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filters['loyalty_points_min'], function ($query) {
                $query->where('loyalty_points', '>=', $this->filters['loyalty_points_min']);
            })
            ->when($this->filters['visit_count_min'], function ($query) {
                $query->where('visit_count', '>=', $this->filters['visit_count_min']);
            });

        // Handle sorting with proper joins
        if ($this->sortField === 'users.name') {
            $query->join('users', 'clients.user_id', '=', 'users.id')
                  ->select('clients.*')
                  ->orderBy('users.name', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $clients = $query->paginate($this->perPage);

        return view('livewire.admin.clients.index', [
            'clients' => $clients,
        ])->layout('layouts.admin');
    }
}
