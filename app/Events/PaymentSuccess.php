<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccess
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('transaction.'.$this->transaction_id);
    }
}
