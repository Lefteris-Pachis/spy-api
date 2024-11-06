<?php

namespace App\Events;

use App\Models\Spy;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpyCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Spy $spy;

    /**
     * Create a new event instance.
     */
    public function __construct(Spy $spy)
    {
        $this->spy = $spy;
    }
}
