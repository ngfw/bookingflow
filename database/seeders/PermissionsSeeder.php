<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define all permissions with categories
        $permissions = [
            // Dashboard & General
            ['name' => 'view_dashboard', 'description' => 'View dashboard', 'category' => 'dashboard'],
            
            // Appointments
            ['name' => 'view_appointments', 'description' => 'View appointments', 'category' => 'appointments'],
            ['name' => 'create_appointments', 'description' => 'Create appointments', 'category' => 'appointments'],
            ['name' => 'edit_appointments', 'description' => 'Edit appointments', 'category' => 'appointments'],
            ['name' => 'delete_appointments', 'description' => 'Delete appointments', 'category' => 'appointments'],
            ['name' => 'manage_appointment_calendar', 'description' => 'Manage appointment calendar', 'category' => 'appointments'],
            
            // Clients
            ['name' => 'view_clients', 'description' => 'View clients', 'category' => 'clients'],
            ['name' => 'create_clients', 'description' => 'Create clients', 'category' => 'clients'],
            ['name' => 'edit_clients', 'description' => 'Edit clients', 'category' => 'clients'],
            ['name' => 'delete_clients', 'description' => 'Delete clients', 'category' => 'clients'],
            
            // Services
            ['name' => 'view_services', 'description' => 'View services', 'category' => 'services'],
            ['name' => 'create_services', 'description' => 'Create services', 'category' => 'services'],
            ['name' => 'edit_services', 'description' => 'Edit services', 'category' => 'services'],
            ['name' => 'delete_services', 'description' => 'Delete services', 'category' => 'services'],
            
            // Products & Inventory
            ['name' => 'view_products', 'description' => 'View products', 'category' => 'inventory'],
            ['name' => 'create_products', 'description' => 'Create products', 'category' => 'inventory'],
            ['name' => 'edit_products', 'description' => 'Edit products', 'category' => 'inventory'],
            ['name' => 'delete_products', 'description' => 'Delete products', 'category' => 'inventory'],
            ['name' => 'manage_inventory', 'description' => 'Manage inventory', 'category' => 'inventory'],
            ['name' => 'view_suppliers', 'description' => 'View suppliers', 'category' => 'inventory'],
            ['name' => 'manage_suppliers', 'description' => 'Manage suppliers', 'category' => 'inventory'],
            
            // Staff Management
            ['name' => 'view_staff', 'description' => 'View staff', 'category' => 'staff'],
            ['name' => 'create_staff', 'description' => 'Create staff', 'category' => 'staff'],
            ['name' => 'edit_staff', 'description' => 'Edit staff', 'category' => 'staff'],
            ['name' => 'delete_staff', 'description' => 'Delete staff', 'category' => 'staff'],
            ['name' => 'manage_staff_schedules', 'description' => 'Manage staff schedules', 'category' => 'staff'],
            ['name' => 'view_staff_performance', 'description' => 'View staff performance', 'category' => 'staff'],
            ['name' => 'manage_staff_payroll', 'description' => 'Manage staff payroll', 'category' => 'staff'],
            
            // POS & Sales
            ['name' => 'use_pos', 'description' => 'Use point of sale system', 'category' => 'pos'],
            ['name' => 'process_payments', 'description' => 'Process payments', 'category' => 'pos'],
            ['name' => 'manage_cash_drawer', 'description' => 'Manage cash drawer', 'category' => 'pos'],
            ['name' => 'view_sales_reports', 'description' => 'View sales reports', 'category' => 'pos'],
            
            // Invoices & Payments
            ['name' => 'view_invoices', 'description' => 'View invoices', 'category' => 'financial'],
            ['name' => 'create_invoices', 'description' => 'Create invoices', 'category' => 'financial'],
            ['name' => 'edit_invoices', 'description' => 'Edit invoices', 'category' => 'financial'],
            ['name' => 'delete_invoices', 'description' => 'Delete invoices', 'category' => 'financial'],
            ['name' => 'view_payments', 'description' => 'View payments', 'category' => 'financial'],
            ['name' => 'process_refunds', 'description' => 'Process refunds', 'category' => 'financial'],
            
            // Reports & Analytics
            ['name' => 'view_reports', 'description' => 'View reports', 'category' => 'reports'],
            ['name' => 'view_analytics', 'description' => 'View analytics', 'category' => 'reports'],
            ['name' => 'export_reports', 'description' => 'Export reports', 'category' => 'reports'],
            ['name' => 'view_financial_reports', 'description' => 'View financial reports', 'category' => 'reports'],
            
            // Settings & Configuration
            ['name' => 'view_settings', 'description' => 'View settings', 'category' => 'settings'],
            ['name' => 'edit_settings', 'description' => 'Edit settings', 'category' => 'settings'],
            ['name' => 'manage_salon_settings', 'description' => 'Manage salon settings', 'category' => 'settings'],
            ['name' => 'manage_system_settings', 'description' => 'Manage system settings', 'category' => 'settings'],
            
            // User Management
            ['name' => 'view_users', 'description' => 'View users', 'category' => 'users'],
            ['name' => 'create_users', 'description' => 'Create users', 'category' => 'users'],
            ['name' => 'edit_users', 'description' => 'Edit users', 'category' => 'users'],
            ['name' => 'delete_users', 'description' => 'Delete users', 'category' => 'users'],
            ['name' => 'manage_roles', 'description' => 'Manage roles and permissions', 'category' => 'users'],
            
            // Marketing & Communications
            ['name' => 'view_marketing', 'description' => 'View marketing campaigns', 'category' => 'marketing'],
            ['name' => 'create_marketing', 'description' => 'Create marketing campaigns', 'category' => 'marketing'],
            ['name' => 'manage_promotions', 'description' => 'Manage promotions', 'category' => 'marketing'],
            ['name' => 'send_notifications', 'description' => 'Send notifications', 'category' => 'marketing'],
            
            // Page Management
            ['name' => 'view_pages', 'description' => 'View pages', 'category' => 'content'],
            ['name' => 'create_pages', 'description' => 'Create pages', 'category' => 'content'],
            ['name' => 'edit_pages', 'description' => 'Edit pages', 'category' => 'content'],
            ['name' => 'delete_pages', 'description' => 'Delete pages', 'category' => 'content'],
            ['name' => 'manage_blog', 'description' => 'Manage blog', 'category' => 'content'],
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Define roles with their permissions
        $roles = [
            'customer' => [
                'display_name' => 'Customer',
                'description' => 'Regular customers who can book appointments',
                'is_default' => true,
                'permissions' => [
                    // Customers can only book appointments through frontend
                ]
            ],
            'staff' => [
                'display_name' => 'Staff',
                'description' => 'Staff members who can manage appointments and use POS',
                'is_default' => false,
                'permissions' => [
                    'view_dashboard',
                    'view_appointments', 'create_appointments', 'edit_appointments',
                    'view_clients', 'create_clients', 'edit_clients',
                    'view_services',
                    'use_pos', 'process_payments',
                    'view_invoices', 'create_invoices',
                    'view_payments',
                ]
            ],
            'admin' => [
                'display_name' => 'Admin',
                'description' => 'Administrative staff who can manage products and appointments but not system settings',
                'is_default' => false,
                'permissions' => [
                    'view_dashboard',
                    'view_appointments', 'create_appointments', 'edit_appointments', 'delete_appointments', 'manage_appointment_calendar',
                    'view_clients', 'create_clients', 'edit_clients', 'delete_clients',
                    'view_services', 'create_services', 'edit_services', 'delete_services',
                    'view_products', 'create_products', 'edit_products', 'delete_products', 'manage_inventory',
                    'view_suppliers', 'manage_suppliers',
                    'view_staff', 'create_staff', 'edit_staff', 'manage_staff_schedules',
                    'use_pos', 'process_payments', 'manage_cash_drawer',
                    'view_invoices', 'create_invoices', 'edit_invoices', 'delete_invoices',
                    'view_payments', 'process_refunds',
                    'view_marketing', 'create_marketing', 'manage_promotions',
                    'view_pages', 'create_pages', 'edit_pages',
                ]
            ],
            'super_admin' => [
                'display_name' => 'Super Admin',
                'description' => 'Full system access including settings, reports, and user management',
                'is_default' => false,
                'permissions' => array_column($permissions, 'name') // All permissions
            ]
        ];

        // Insert roles and assign permissions
        foreach ($roles as $roleName => $roleData) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $roleName,
                'display_name' => $roleData['display_name'],
                'description' => $roleData['description'],
                'is_default' => $roleData['is_default'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign permissions to role
            foreach ($roleData['permissions'] as $permissionName) {
                $permissionId = DB::table('permissions')->where('name', $permissionName)->value('id');
                if ($permissionId) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}