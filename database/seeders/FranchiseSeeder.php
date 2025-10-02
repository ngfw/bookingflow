<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Franchise;
use App\Models\Location;
use App\Models\FranchisePayment;

class FranchiseSeeder extends Seeder
{
    public function run(): void
    {
        // Corporate franchise
        $corporate = Franchise::create([
            'name' => 'Beauty Salon Corporate',
            'franchise_code' => 'FR001',
            'franchise_type' => 'owned',
            'status' => 'active',
            'owner_name' => 'Corporate Management',
            'owner_email' => 'corporate@beautysalon.com',
            'owner_phone' => '(555) 100-0001',
            'owner_address' => '123 Corporate Plaza',
            'owner_city' => 'San Francisco',
            'owner_state' => 'CA',
            'owner_postal_code' => '94102',
            'agreement_start_date' => '2020-01-01',
            'agreement_end_date' => '2030-12-31',
            'royalty_rate' => 0.00,
            'monthly_sales_target' => 150000,
            'current_month_sales' => 165000,
        ]);

        // Marina franchise
        $marina = Franchise::create([
            'name' => 'Marina District Franchise',
            'franchise_code' => 'FR002',
            'franchise_type' => 'franchisee',
            'status' => 'active',
            'owner_name' => 'Sarah Johnson',
            'owner_email' => 'sarah@marina-salon.com',
            'owner_phone' => '(555) 200-0002',
            'owner_address' => '456 Marina Boulevard',
            'owner_city' => 'San Francisco',
            'owner_state' => 'CA',
            'owner_postal_code' => '94123',
            'agreement_start_date' => '2021-03-01',
            'agreement_end_date' => '2031-02-28',
            'initial_franchise_fee' => 75000,
            'royalty_rate' => 0.06,
            'marketing_fee_rate' => 0.03,
            'technology_fee_rate' => 0.02,
            'monthly_sales_target' => 85000,
            'current_month_sales' => 92000,
        ]);

        // Beverly Hills franchise
        $beverly = Franchise::create([
            'name' => 'Beverly Hills Luxury Franchise',
            'franchise_code' => 'FR003',
            'franchise_type' => 'franchisee',
            'status' => 'active',
            'owner_name' => 'Michael Rodriguez',
            'owner_email' => 'michael@beverlyhills-salon.com',
            'owner_phone' => '(555) 300-0003',
            'owner_address' => '789 Rodeo Drive',
            'owner_city' => 'Beverly Hills',
            'owner_state' => 'CA',
            'owner_postal_code' => '90210',
            'agreement_start_date' => '2022-06-01',
            'agreement_end_date' => '2032-05-31',
            'initial_franchise_fee' => 125000,
            'royalty_rate' => 0.05,
            'marketing_fee_rate' => 0.02,
            'technology_fee_rate' => 0.015,
            'monthly_sales_target' => 120000,
            'current_month_sales' => 135000,
        ]);

        // Associate locations with franchises
        Location::where('code', 'SF001')->update(['franchise_id' => $corporate->id]);
        Location::where('code', 'SF002')->update(['franchise_id' => $corporate->id]);
        Location::where('code', 'SF003')->update(['franchise_id' => $corporate->id]);
        Location::where('code', 'LA001')->update(['franchise_id' => $beverly->id]);
        Location::where('code', 'SJ001')->update(['franchise_id' => $marina->id]);
    }
}