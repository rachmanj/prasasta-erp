<?php

namespace App\Events;

use App\Models\CourseBatch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatchStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $batch;

    /**
     * Create a new event instance.
     */
    public function __construct(CourseBatch $batch)
    {
        $this->batch = $batch;
    }
}
