<?php

namespace App\Providers;

use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
    ];

    /**
     * Bootstrap any authentication/authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('updateOrderStatus', [OrderPolicy::class, 'updateOrderStatus']);
    }
}
