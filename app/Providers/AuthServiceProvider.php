<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{User, Permission};

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        // Define gates for each permission
        try {
            Permission::all()->each(function ($permission) {
                Gate::define($permission->name, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->name);
                });
            });
        } catch (\Exception $e) {
            // Permissions table might not exist during initial migration
        }

        // Super admin bypass
        Gate::before(function (User $user, $ability) {
            if ($user->hasRole('administrator')) {
                return true;
            }
        });
    }
}
