<?php

namespace App\Events;

use App\Entities\Admin;
use App\Entities\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LockedPaymentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $broadcastQueue = 'event';
    private Channel $channel;
    private Payment $payment;
    private Admin $admin;

    /**
     * Create a new event instance.
     *
     * @param Payment $payment
     * @param Admin $admin
     */
    public function __construct(Payment $payment, Admin $admin)
    {
        $this->channel = new PrivateChannel('payment');
        $this->payment = $payment;
        $this->admin = $admin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|PrivateChannel
     */
    public function broadcastOn(): Channel|PrivateChannel
    {
        return $this->channel;
    }

    public function broadcastWith(): array
    {
        $payment = $this->payment;
        $payment->admin_id = $this->admin->id;
        $payment->setAttribute('admin', $this->admin);
        return $payment->toArray();
    }
}
