<?php

namespace App\Listeners;

use App\Events\SpyCreated;
use Illuminate\Support\Facades\Log;

class LogSpyCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SpyCreated $event): void
    {
        // Log the spy creation
        Log::info('A new spy has been created:', [
            'name' => $event->spy->name,
            'surname' => $event->spy->surname,
            'agency' => $event->spy->agency,
            'country_of_operation' => $event->spy->country_of_operation,
            'date_of_birth' => $event->spy->date_of_birth,
            'date_of_death' => $event->spy->date_of_death,
        ]);
    }
}
