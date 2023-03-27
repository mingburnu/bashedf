<?php

namespace App\Events;

use App\Repositories\PaymentRepository;
use App\Repositories\DepositRepository;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\ArrayShape;
use Redis;

class UncheckedOrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $broadcastQueue = 'default';
    private Channel $channel;

    /**
     * Create a new event instance.
     *
     */
    public function __construct()
    {
        $this->channel = new PrivateChannel('checker');
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

    #[ArrayShape(['unchecked_order_count' => "false|mixed|string"])]
    public function broadcastWith(): array
    {
        if (!Redis::exists('unchecked_order_count')) {
            $unchecked_order_count = app(PaymentRepository::class)->whereStatus(0)->has('user')->count() + app(DepositRepository::class)->whereStatus(0)->has('user')->count();
            Redis::set('unchecked_order_count', $unchecked_order_count);
        }

        return ['unchecked_order_count' => Redis::get('unchecked_order_count')];
    }
}
