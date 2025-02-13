<?php

namespace App\Listeners;

use App\Events\NewUserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(NewUserRegistered $event)
    {
        Log::info('SendWelcomeEmail listener triggered for ' . $event->user->email);
        
       Mail::to($event->user->email)->send(new WelcomeEmail($event->user));
    }
}
