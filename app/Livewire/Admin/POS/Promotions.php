<?php

namespace App\Livewire\Admin\POS;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;

class Promotions extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingPromotion = null;
    public $promotion = [
        'name' => '',
        'description' => '',
        'type' => 'percentage',
        'discount_value' => 0,
        'minimum_quantity' => 1,
        'minimum_amount' => null,
        'buy_quantity' => null,
        'get_quantity' => null,
        'get_discount_percentage' => null,
        'applicable_products' => [],
        'applicable_categories' => [],
        'start_date' => '',
        'end_date' => '',
        'start_time' => '',
        'end_time' => '',
        'is_active' => true,
        'usage_limit' => null,
        'promo_code' => '',
        'requires_promo_code' => false,
    ];

    public $search = '';
    public $statusFilter = 'all';
    public $typeFilter = 'all';

    public function mount()
    {
        $this->promotion['start_date'] = Carbon::now()->format('Y-m-d');
        $this->promotion['end_date'] = Carbon::now()->addMonth()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function createPromotion()
    {
        $this->editingPromotion = null;
        $this->resetPromotion();
        $this->showModal = true;
    }

    public function editPromotion($promotionId)
    {
        $promotion = Promotion::find($promotionId);
        $this->editingPromotion = $promotion;
        
        $this->promotion = [
            'name' => $promotion->name,
            'description' => $promotion->description,
            'type' => $promotion->type,
            'discount_value' => $promotion->discount_value,
            'minimum_quantity' => $promotion->minimum_quantity,
            'minimum_amount' => $promotion->minimum_amount,
            'buy_quantity' => $promotion->buy_quantity,
            'get_quantity' => $promotion->get_quantity,
            'get_discount_percentage' => $promotion->get_discount_percentage,
            'applicable_products' => $promotion->applicable_products ?? [],
            'applicable_categories' => $promotion->applicable_categories ?? [],
            'start_date' => $promotion->start_date->format('Y-m-d'),
            'end_date' => $promotion->end_date->format('Y-m-d'),
            'start_time' => $promotion->start_time ? $promotion->start_time->format('H:i') : '',
            'end_time' => $promotion->end_time ? $promotion->end_time->format('H:i') : '',
            'is_active' => $promotion->is_active,
            'usage_limit' => $promotion->usage_limit,
            'promo_code' => $promotion->promo_code,
            'requires_promo_code' => $promotion->requires_promo_code,
        ];
        
        $this->showModal = true;
    }

    public function savePromotion()
    {
        $this->validate([
            'promotion.name' => 'required|string|max:255',
            'promotion.description' => 'nullable|string',
            'promotion.type' => 'required|in:percentage,fixed_amount,buy_x_get_y,bulk_discount',
            'promotion.discount_value' => 'required|numeric|min:0',
            'promotion.minimum_quantity' => 'required|integer|min:1',
            'promotion.minimum_amount' => 'nullable|numeric|min:0',
            'promotion.buy_quantity' => 'nullable|integer|min:1',
            'promotion.get_quantity' => 'nullable|integer|min:1',
            'promotion.get_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'promotion.start_date' => 'required|date',
            'promotion.end_date' => 'required|date|after:promotion.start_date',
            'promotion.start_time' => 'nullable|date_format:H:i',
            'promotion.end_time' => 'nullable|date_format:H:i',
            'promotion.usage_limit' => 'nullable|integer|min:1',
            'promotion.promo_code' => 'nullable|string|max:50',
        ]);

        $data = $this->promotion;
        
        // Convert time strings to proper format
        if ($data['start_time']) {
            $data['start_time'] = Carbon::createFromFormat('H:i', $data['start_time'])->format('H:i:s');
        }
        if ($data['end_time']) {
            $data['end_time'] = Carbon::createFromFormat('H:i', $data['end_time'])->format('H:i:s');
        }

        if ($this->editingPromotion) {
            $this->editingPromotion->update($data);
            session()->flash('success', 'Promotion updated successfully.');
        } else {
            Promotion::create($data);
            session()->flash('success', 'Promotion created successfully.');
        }

        $this->closeModal();
    }

    public function deletePromotion($promotionId)
    {
        $promotion = Promotion::find($promotionId);
        $promotion->delete();
        session()->flash('success', 'Promotion deleted successfully.');
    }

    public function togglePromotionStatus($promotionId)
    {
        $promotion = Promotion::find($promotionId);
        $promotion->update(['is_active' => !$promotion->is_active]);
        session()->flash('success', 'Promotion status updated successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingPromotion = null;
        $this->resetPromotion();
    }

    public function resetPromotion()
    {
        $this->promotion = [
            'name' => '',
            'description' => '',
            'type' => 'percentage',
            'discount_value' => 0,
            'minimum_quantity' => 1,
            'minimum_amount' => null,
            'buy_quantity' => null,
            'get_quantity' => null,
            'get_discount_percentage' => null,
            'applicable_products' => [],
            'applicable_categories' => [],
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonth()->format('Y-m-d'),
            'start_time' => '',
            'end_time' => '',
            'is_active' => true,
            'usage_limit' => null,
            'promo_code' => '',
            'requires_promo_code' => false,
        ];
    }

    public function getPromotions()
    {
        $query = Promotion::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('promo_code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'active':
                    $query->where('is_active', true)
                          ->where('start_date', '<=', Carbon::now())
                          ->where('end_date', '>=', Carbon::now());
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('end_date', '<', Carbon::now());
                    break;
                case 'upcoming':
                    $query->where('start_date', '>', Carbon::now());
                    break;
            }
        }

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getProducts()
    {
        return Product::where('type', 'retail')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getCategories()
    {
        return Category::orderBy('name')->get();
    }

    public function getStats()
    {
        $total = Promotion::count();
        $active = Promotion::where('is_active', true)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->count();
        $expired = Promotion::where('end_date', '<', Carbon::now())->count();
        $upcoming = Promotion::where('start_date', '>', Carbon::now())->count();

        return [
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'upcoming' => $upcoming,
        ];
    }

    public function render()
    {
        $promotions = $this->getPromotions();
        $products = $this->getProducts();
        $categories = $this->getCategories();
        $stats = $this->getStats();

        return view('livewire.admin.pos.promotions', [
            'promotions' => $promotions,
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
        ])->layout('layouts.admin');
    }
}
