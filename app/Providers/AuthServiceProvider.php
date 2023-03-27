<?php

namespace App\Providers;

use App\Entities\Admin;
use App\Entities\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($admin) {
            return $admin instanceof Admin && $admin->hasRole(1) ? true : null;
        });

        Gate::define('rule', function ($admin, $user) {
            return $admin instanceof Admin && $admin->merchants->contains($user);
        });

        Gate::define('use', function ($user) {
            return $user instanceof User && !is_null($user->api_key) ;
        });
    }
}