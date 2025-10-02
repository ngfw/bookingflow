<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AccessLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class AccessControlService
{
    /**
     * Check user permissions
     */
    public function hasPermission($user, $permission, $resource = null)
    {
        try {
            if (!$user) {
                return false;
            }

            // Check if user is active
            if (!$user->is_active) {
                return false;
            }

            // Admin users have all permissions
            if ($user->isAdmin()) {
                return true;
            }

            // Check user-specific permissions
            if ($this->checkUserPermission($user, $permission, $resource)) {
                return true;
            }

            // Check role-based permissions
            if ($this->checkRolePermission($user, $permission, $resource)) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Permission check failed", [
                'user_id' => $user ? $user->id : null,
                'permission' => $permission,
                'resource' => $resource,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check user-specific permission
     */
    protected function checkUserPermission($user, $permission, $resource = null)
    {
        $userPermissions = $user->permissions ?? [];
        
        if (in_array($permission, $userPermissions)) {
            return true;
        }

        // Check resource-specific permissions
        if ($resource) {
            $resourcePermission = $permission . '_' . $resource;
            return in_array($resourcePermission, $userPermissions);
        }

        return false;
    }

    /**
     * Check role-based permission
     */
    protected function checkRolePermission($user, $permission, $resource = null)
    {
        $role = $user->role;
        
        if (!$role) {
            return false;
        }

        $rolePermissions = $this->getRolePermissions($role);
        
        if (in_array($permission, $rolePermissions)) {
            return true;
        }

        // Check resource-specific permissions
        if ($resource) {
            $resourcePermission = $permission . '_' . $resource;
            return in_array($resourcePermission, $rolePermissions);
        }

        return false;
    }

    /**
     * Get role permissions
     */
    protected function getRolePermissions($role)
    {
        $cacheKey = "role_permissions_{$role}";
        
        return Cache::remember($cacheKey, 3600, function () use ($role) {
            $permissions = [
                'admin' => [
                    'users.create', 'users.read', 'users.update', 'users.delete',
                    'clients.create', 'clients.read', 'clients.update', 'clients.delete',
                    'appointments.create', 'appointments.read', 'appointments.update', 'appointments.delete',
                    'services.create', 'services.read', 'services.update', 'services.delete',
                    'staff.create', 'staff.read', 'staff.update', 'staff.delete',
                    'reports.read', 'reports.export',
                    'settings.read', 'settings.update',
                    'backup.create', 'backup.restore',
                    'audit.read', 'audit.export',
                ],
                'staff' => [
                    'clients.read', 'clients.update',
                    'appointments.create', 'appointments.read', 'appointments.update',
                    'services.read',
                    'reports.read',
                ],
                'client' => [
                    'appointments.create', 'appointments.read', 'appointments.update',
                    'profile.read', 'profile.update',
                ],
            ];

            return $permissions[$role] ?? [];
        });
    }

    /**
     * Grant permission to user
     */
    public function grantPermission($user, $permission, $resource = null)
    {
        try {
            $userPermissions = $user->permissions ?? [];
            
            $permissionKey = $resource ? $permission . '_' . $resource : $permission;
            
            if (!in_array($permissionKey, $userPermissions)) {
                $userPermissions[] = $permissionKey;
                
                $user->update(['permissions' => $userPermissions]);
                
                // Clear cache
                Cache::forget("user_permissions_{$user->id}");
                
                // Log permission grant
                $this->logAccessEvent('permission_granted', $user, $permission, $resource);
                
                Log::info("Permission granted to user", [
                    'user_id' => $user->id,
                    'permission' => $permissionKey,
                ]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to grant permission", [
                'user_id' => $user->id,
                'permission' => $permission,
                'resource' => $resource,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Revoke permission from user
     */
    public function revokePermission($user, $permission, $resource = null)
    {
        try {
            $userPermissions = $user->permissions ?? [];
            
            $permissionKey = $resource ? $permission . '_' . $resource : $permission;
            
            $userPermissions = array_filter($userPermissions, function ($p) use ($permissionKey) {
                return $p !== $permissionKey;
            });
            
            $user->update(['permissions' => array_values($userPermissions)]);
            
            // Clear cache
            Cache::forget("user_permissions_{$user->id}");
            
            // Log permission revoke
            $this->logAccessEvent('permission_revoked', $user, $permission, $resource);
            
            Log::info("Permission revoked from user", [
                'user_id' => $user->id,
                'permission' => $permissionKey,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to revoke permission", [
                'user_id' => $user->id,
                'permission' => $permission,
                'resource' => $resource,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check resource access
     */
    public function canAccessResource($user, $resource, $action = 'read')
    {
        $permission = $action . '_' . $resource;
        return $this->hasPermission($user, $permission, $resource);
    }

    /**
     * Check data access
     */
    public function canAccessData($user, $dataType, $action = 'read')
    {
        $permission = $action . '_' . $dataType;
        return $this->hasPermission($user, $permission, $dataType);
    }

    /**
     * Check location access
     */
    public function canAccessLocation($user, $locationId)
    {
        // Admin users can access all locations
        if ($user->isAdmin()) {
            return true;
        }

        // Staff users can access their assigned locations
        if ($user->isStaff()) {
            $staff = $user->staff;
            if ($staff && $staff->location_id == $locationId) {
                return true;
            }
        }

        // Check user-specific location permissions
        $userLocations = $user->allowed_locations ?? [];
        return in_array($locationId, $userLocations);
    }

    /**
     * Check franchise access
     */
    public function canAccessFranchise($user, $franchiseId)
    {
        // Admin users can access all franchises
        if ($user->isAdmin()) {
            return true;
        }

        // Check user's franchise
        if ($user->franchise_id == $franchiseId) {
            return true;
        }

        // Check user-specific franchise permissions
        $userFranchises = $user->allowed_franchises ?? [];
        return in_array($franchiseId, $userFranchises);
    }

    /**
     * Log access event
     */
    public function logAccessEvent($eventType, $user, $permission = null, $resource = null, $result = 'granted')
    {
        try {
            AccessLog::create([
                'user_id' => $user->id,
                'event_type' => $eventType,
                'permission' => $permission,
                'resource' => $resource,
                'result' => $result,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'created_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to log access event", [
                'event_type' => $eventType,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get user permissions
     */
    public function getUserPermissions($user)
    {
        $cacheKey = "user_permissions_{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            $permissions = [];
            
            // Get role permissions
            $rolePermissions = $this->getRolePermissions($user->role);
            $permissions = array_merge($permissions, $rolePermissions);
            
            // Get user-specific permissions
            $userPermissions = $user->permissions ?? [];
            $permissions = array_merge($permissions, $userPermissions);
            
            return array_unique($permissions);
        });
    }

    /**
     * Get access control statistics
     */
    public function getAccessStatistics($startDate = null, $endDate = null)
    {
        $query = AccessLog::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_events' => $query->count(),
            'granted' => $query->where('result', 'granted')->count(),
            'denied' => $query->where('result', 'denied')->count(),
            'by_event_type' => $query->selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->pluck('count', 'event_type')
                ->toArray(),
            'by_permission' => $query->selectRaw('permission, COUNT(*) as count')
                ->whereNotNull('permission')
                ->groupBy('permission')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'permission')
                ->toArray(),
            'by_user' => $query->selectRaw('user_id, COUNT(*) as count')
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'user_id')
                ->toArray(),
        ];
    }

    /**
     * Get user access history
     */
    public function getUserAccessHistory($userId, $limit = 50)
    {
        return AccessLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'event_type' => $log->event_type,
                    'permission' => $log->permission,
                    'resource' => $log->resource,
                    'result' => $log->result,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at,
                ];
            });
    }

    /**
     * Check for permission escalation
     */
    public function checkPermissionEscalation($user, $requestedPermissions)
    {
        $currentPermissions = $this->getUserPermissions($user);
        
        foreach ($requestedPermissions as $permission) {
            if (!in_array($permission, $currentPermissions)) {
                // Log potential escalation attempt
                $this->logAccessEvent('permission_escalation', $user, $permission, null, 'attempted');
                
                return [
                    'escalation_detected' => true,
                    'unauthorized_permission' => $permission,
                ];
            }
        }

        return [
            'escalation_detected' => false,
        ];
    }

    /**
     * Get access control dashboard data
     */
    public function getAccessDashboard()
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subWeek();

        return [
            'today_events' => AccessLog::where('created_at', '>=', $today)->count(),
            'week_events' => AccessLog::where('created_at', '>=', $weekAgo)->count(),
            'denied_access' => AccessLog::where('result', 'denied')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'escalation_attempts' => AccessLog::where('event_type', 'permission_escalation')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'top_permissions' => AccessLog::selectRaw('permission, COUNT(*) as count')
                ->whereNotNull('permission')
                ->where('created_at', '>=', $weekAgo)
                ->groupBy('permission')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->pluck('count', 'permission')
                ->toArray(),
            'recent_denials' => AccessLog::where('result', 'denied')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($log) {
                    return [
                        'user_id' => $log->user_id,
                        'permission' => $log->permission,
                        'resource' => $log->resource,
                        'ip_address' => $log->ip_address,
                        'created_at' => $log->created_at,
                    ];
                }),
        ];
    }
}
