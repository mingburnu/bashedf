<?php

namespace App\Jobs;

use App\Entities\Payment;
use App\Events\CallbackEvent;
use App\Presenters\PaymentPresenter;
use App\Services\CallbackService;
use App\Services\SignService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Payment $payment;

    /**
     * Create a new job instance.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $url = $this->payment->callback_url;
        $api_key = $this->payment->user->api_key;

        $data = app(PaymentPresenter::class)->present($this->payment);
        $data = app(SignService::class)->sign($data, $api_key);
        $response = app(CallbackService::class)->call($url, $data) !== false;

        $user_id = $this->payment->user_id;
        activity('callback')->performedOn($this->payment)
            ->withProperties(compact('user_id', 'url', 'response', 'data'))->log('');

        broadcast(new CallbackEvent($this->payment));
    }
}
