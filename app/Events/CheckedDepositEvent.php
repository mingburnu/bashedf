<?php

namespace App\Events;

use App\Entities\Deposit;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckedDepositEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $broadcastQueue = 'event';
    private Channel $channel;
    private Deposit $deposit;
    private string $admin_name;

    /**
     * Create a new event instance.
     *
     * @param Deposit $deposit
     * @param string $admin_name
     */
    public function __construct(Deposit $deposit, string $admin_name)
    {
        $this->channel = new PrivateChannel('deposit');
        $this->deposit = $deposit;
        $this->admin_name = $admin_name;
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
        $deposit = $this->deposit;
        $deposit->setAttribute('admin', ['name' => $this->admin_name]);
        $deposit->setAttribute('balance', $deposit->transactions[0]->new_balance ?? '');
        return $deposit->toArray();
    }
}