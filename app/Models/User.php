<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'is_active',
        'primary_location_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function primaryLocation()
    {
        return $this->belongsTo(Location::class, 'primary_location_id');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'staff', 'user_id', 'location_id');
    }

    // New Role-Based Permission System
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function primaryRole()
    {
        return $this->belongsToMany(Role::class, 'user_roles')->wherePivot('is_primary', true)->first();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function hasRole(string $role): bool
    {
        // Check new role system first
        if ($this->roles()->where('name', $role)->exists()) {
            return true;
        }
        
        // Fallback to old role system for backward compatibility
        return $this->role === $role;
    }

    public function hasPermission(string $permission): bool
    {
        // Check direct user permissions
        if ($this->permissions()->where('name', $permission)->exists()) {
            return true;
        }

        // Check role-based permissions
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function assignRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        if (!$this->roles()->where('role_id', $role->id)->exists()) {
            $this->roles()->attach($role->id, ['is_primary' => $this->roles()->count() === 0]);
        }
    }

    public function removeRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    public function givePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        if (!$this->permissions()->where('permission_id', $permission->id)->exists()) {
            $this->permissions()->attach($permission->id);
        }
    }

    public function revokePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission->id);
    }

    // Helper methods for common role checks
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdminLevel(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin');
    }

    public function isStaffLevel(): bool
    {
        return $this->hasRole('staff') || $this->isAdminLevel();
    }

    public function isCustomerOnly(): bool
    {
        return $this->hasRole('customer') && !$this->isStaffLevel();
    }

    // Get user's primary role name for display
    public function getRoleName(): string
    {
        $primaryRole = $this->primaryRole();
        return $primaryRole ? $primaryRole->display_name : ucfirst($this->role ?? 'Customer');
    }

    // Get user's highest privilege level
    public function getHighestRole(): string
    {
        if ($this->isSuperAdmin()) return 'super_admin';
        if ($this->hasRole('admin')) return 'admin';
        if ($this->hasRole('staff')) return 'staff';
        return 'customer';
    }
}
