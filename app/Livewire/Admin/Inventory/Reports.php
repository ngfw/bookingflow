<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductUsage;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Category;
use Carbon\Carbon;

class Reports extends Component
{
    public $dateRange = 'this_month';
    public $categoryFilter = '';
    public $supplierFilter = '';
    public $reportType = 'overview';

    public function render()
    {
        $dateRange = $this->getDateRange();
        
        $overview = $this->getOverviewData($dateRange);
        $lowStockProducts = $this->getLowStockProducts();
        $topUsedProducts = $this->getTopUsedProducts($dateRange);
        $supplierPerformance = $this->getSupplierPerformance($dateRange);
        $categoryAnalysis = $this->getCategoryAnalysis($dateRange);
        $purchaseOrderSummary = $this->getPurchaseOrderSummary($dateRange);
        $costAnalysis = $this->getCostAnalysis($dateRange);

        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.inventory.reports', [
            'overview' => $overview,
            'lowStockProducts' => $lowStockProducts,
            'topUsedProducts' => $topUsedProducts,
            'supplierPerformance' => $supplierPerformance,
            'categoryAnalysis' => $categoryAnalysis,
            'purchaseOrderSummary' => $purchaseOrderSummary,
            'costAnalysis' => $costAnalysis,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ])->layout('layouts.admin');
    }

    private function getDateRange()
    {
        return match($this->dateRange) {
            'today' => [Carbon::today(), Carbon::today()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    private function getOverviewData($dateRange)
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockCount = Product::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outOfStockCount = Product::where('current_stock', 0)->count();
        
        $totalValue = Product::sum(\DB::raw('current_stock * cost_price'));
        $totalUsage = ProductUsage::whereBetween('usage_date', $dateRange)->sum('total_cost');
        $totalPurchases = PurchaseOrder::where('status', 'received')
            ->whereBetween('order_date', $dateRange)
            ->sum('total_amount');

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_value' => $totalValue,
            'total_usage' => $totalUsage,
            'total_purchases' => $totalPurchases,
        ];
    }

    private function getLowStockProducts()
    {
        return Product::with(['category', 'supplier'])
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(10)
            ->get();
    }

    private function getTopUsedProducts($dateRange)
    {
        return ProductUsage::with(['product.category'])
            ->whereBetween('usage_date', $dateRange)
            ->selectRaw('product_id, SUM(quantity_used) as total_quantity, SUM(total_cost) as total_cost')
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();
    }

    private function getSupplierPerformance($dateRange)
    {
        return PurchaseOrder::with('supplier')
            ->where('status', 'received')
            ->whereBetween('order_date', $dateRange)
            ->selectRaw('supplier_id, COUNT(*) as order_count, SUM(total_amount) as total_amount, AVG(total_amount) as avg_order_value')
            ->groupBy('supplier_id')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();
    }

    private function getCategoryAnalysis($dateRange)
    {
        return ProductUsage::with(['product.category'])
            ->whereBetween('usage_date', $dateRange)
            ->selectRaw('categories.name as category_name, SUM(product_usages.quantity_used) as total_quantity, SUM(product_usages.total_cost) as total_cost')
            ->join('products', 'product_usages.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_cost', 'desc')
            ->get();
    }

    private function getPurchaseOrderSummary($dateRange)
    {
        $orders = PurchaseOrder::whereBetween('order_date', $dateRange);
        
        return [
            'total_orders' => $orders->count(),
            'draft_orders' => $orders->where('status', 'draft')->count(),
            'pending_orders' => $orders->where('status', 'pending')->count(),
            'approved_orders' => $orders->where('status', 'approved')->count(),
            'ordered_orders' => $orders->where('status', 'ordered')->count(),
            'received_orders' => $orders->where('status', 'received')->count(),
            'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
            'total_value' => $orders->sum('total_amount'),
            'avg_order_value' => $orders->avg('total_amount'),
        ];
    }

    private function getCostAnalysis($dateRange)
    {
        $usageCost = ProductUsage::whereBetween('usage_date', $dateRange)->sum('total_cost');
        $purchaseCost = PurchaseOrder::where('status', 'received')
            ->whereBetween('order_date', $dateRange)
            ->sum('total_amount');
        
        $costPerService = $usageCost / max(1, \App\Models\Appointment::whereBetween('appointment_date', $dateRange)->count());
        
        return [
            'usage_cost' => $usageCost,
            'purchase_cost' => $purchaseCost,
            'cost_per_service' => $costPerService,
            'cost_trend' => $this->getCostTrend($dateRange),
        ];
    }

    private function getCostTrend($dateRange)
    {
        $previousRange = $this->getPreviousDateRange($dateRange);
        
        $currentCost = ProductUsage::whereBetween('usage_date', $dateRange)->sum('total_cost');
        $previousCost = ProductUsage::whereBetween('usage_date', $previousRange)->sum('total_cost');
        
        if ($previousCost == 0) {
            return 0;
        }
        
        return (($currentCost - $previousCost) / $previousCost) * 100;
    }

    private function getPreviousDateRange($dateRange)
    {
        $start = $dateRange[0];
        $end = $dateRange[1];
        $daysDiff = $start->diffInDays($end);
        
        return [
            $start->copy()->subDays($daysDiff + 1),
            $start->copy()->subDay(),
        ];
    }
}
