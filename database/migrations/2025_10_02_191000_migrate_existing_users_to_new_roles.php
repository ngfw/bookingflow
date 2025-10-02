<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing users to the new role system
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            // Determine the new role based on existing role
            $newRoleName = match($user->role) {
                'admin' => 'super_admin', // Existing admins become super admins
                'staff' => 'staff',
                'client' => 'customer',
                default => 'customer'
            };
            
            // Get the role ID
            $role = DB::table('roles')->where('name', $newRoleName)->first();
            
            if ($role) {
                // Assign the role to the user
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'is_primary' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Remove all user role assignments
        DB::table('user_roles')->truncate();
    }
};