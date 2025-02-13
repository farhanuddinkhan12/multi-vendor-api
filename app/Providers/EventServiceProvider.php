<?php

namespace App\Providers;

use App\Events\NewUserRegistered;
use App\Events\OrderPlaced;
use App\Listeners\SendOrderConfirmation;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewUserRegistered::class => [
            SendWelcomeEmail::class,
        ],
        OrderPlaced::class => [
            SendOrderConfirmation::class,
        ],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
