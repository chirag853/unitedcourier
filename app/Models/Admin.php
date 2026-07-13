<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin_user';

    protected $fillable = [
        'type',
        'name',
        'email',
        'mobile',
        'designation',
        'state',
        'city',
        'password',
        'status',
        'module_access',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'module_access' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * The master list of modules available in the admin panel.
     * Each module maps to a set of route URI patterns that grant access.
     *
     * key   => unique module identifier (stored in module_access)
     * label => human readable name shown in the UI
     * routes => array of route URI patterns (relative to /admin) the module unlocks
     */
    public static function getModules(): array
    {
        return [
            'dashboard' => [
                'label' => 'Dashboard',
                'icon' => 'ti-dashboard',
                'routes' => ['dashboard', 'dashboard-chart-data'],
            ],
            'website' => [
                'label' => 'Website Management',
                'icon' => 'ti-world',
                'routes' => ['change-*'],
            ],
            'customer' => [
                'label' => 'Customer & Shipments',
                'icon' => 'ti-users',
                'routes' => ['companies', 'kyc-pending*', 'kyc-approved*', 'customer-profile*', 'kyc-export*', 'customer/*/toggle-status', 'assign-delivery', 'receive-shipment', 'generate-label', 'ready-to-dispatch'],
            ],
            'manage_rate' => [
                'label' => 'Manage Rate',
                'icon' => 'ti-currency-rupee',
                'routes' => ['manage-rate*'],
            ],
            'admin_management' => [
                'label' => 'Admin Management',
                'icon' => 'ti-user-cog',
                'routes' => ['delivery-persons*', 'create-user*'],
            ],
            'my_profile' => [
                'label' => 'My Profile',
                'icon' => 'ti-medal',
                'routes' => ['my-profile*'],
            ],
        ];
    }

    /**
     * Get a flat list of all module keys.
     */
    public static function getModuleKeys(): array
    {
        return array_keys(self::getModules());
    }

    /**
     * Whether this admin is a Super Admin (full access, bypasses module checks).
     */
    public function isSuperAdmin(): bool
    {
        return $this->type === 'Super Admin';
    }

    /**
     * The list of module keys this admin has access to.
     * Super Admin returns all module keys.
     */
    public function getAccessModules(): array
    {
        if ($this->isSuperAdmin()) {
            return self::getModuleKeys();
        }

        return is_array($this->module_access) ? $this->module_access : [];
    }

    /**
     * Check if the admin has access to a given module key.
     */
    public function hasModuleAccess(string $moduleKey): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($moduleKey, $this->getAccessModules(), true);
    }

    /**
     * Check if the admin can access a route URI (relative to /admin).
     * Matches against the route patterns defined for each granted module.
     */
    public function canAccessRoute(string $routeUri): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $modules = self::getModules();
        $granted = $this->getAccessModules();

        foreach ($granted as $moduleKey) {
            if (!isset($modules[$moduleKey]['routes'])) {
                continue;
            }
            foreach ($modules[$moduleKey]['routes'] as $pattern) {
                if ($this->matchRoutePattern($pattern, $routeUri)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Simple wildcard route pattern matcher.
     * Supports '*' as a trailing wildcard (e.g. 'manage-rate*' matches 'manage-rate', 'manage-rate/edit').
     */
    protected function matchRoutePattern(string $pattern, string $routeUri): bool
    {
        if (str_ends_with($pattern, '*')) {
            $prefix = rtrim($pattern, '*');
            return $routeUri === $prefix || str_starts_with($routeUri, $prefix);
        }

        return $pattern === $routeUri;
    }
}
