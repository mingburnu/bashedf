<?php

namespace App\Observers;

use App\Entities\Payment;
use App\Jobs\CallbackJob;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use Brick\Math\BigDecimal;
use Prettus\Validator\Exceptions\ValidatorException;
use Redis;

class PaymentObserver
{
    /**
     * Handle the payment "created" event.
     *
     * @param Payment $payment
     * @return void
     * @throws ValidatorException
     */
    public function created(Payment $payment)
    {
        $user_id = $payment->user_id;
        $userWallet = app(WalletRepository::class)->whereUserId($user_id)->lock()->firstOrFail();
        $old_balance = $userWallet->balance;
        $transaction_amount = BigDecimal::of($payment->total_amount)->negated()->jsonSerialize();
        $new_balance = BigDecimal::of($old_balance)->plus($transaction_amount)->jsonSerialize();
        $type = 'payment';
        $orderable_type = $payment->getMorphClass();
        $orderable_id = $payment->id;
        app(TransactionRepository::class)->create(compact('user_id', 'old_balance', 'transaction_amount', 'new_balance', 'type', 'orderable_type', 'orderable_id'));

        $balance = $new_balance;
        app(WalletRepository::class)->update(compact('balance'), $user_id);
        Redis::incrBy('unchecked_order_count', 1);
    }

    /**
     * Handle the payment "updated" event.
     *
     * @param Payment $payment
     * @return void
     * @throws ValidatorException
     */
    public function updated(Payment $payment)
    {
        $transactions = $payment->transactions;
        if ($payment->status == -1 && $transactions->count() === 1) {
            $user_id = $payment->user_id;
            $wallet = app(WalletRepository::class)->whereUserId($user_id)->lock()->firstOrFail();
            $old_balance = $wallet->balance;
            $transaction_amount = $payment->total_amount;
            $new_balance = BigDecimal::of($old_balance)->plus($transaction_amount)->jsonSerialize();
            $type = 'refund';
            $orderable_type = $payment->getMorphClass();
            $orderable_id = $payment->id;
            app(TransactionRepository::class)->create(compact('user_id', 'old_balance', 'transaction_amount', 'new_balance', 'type', 'orderable_type', 'orderable_id'));

            $balance = $new_balance;
            app(WalletRepository::class)->update(compact('balance'), $user_id);
        }

        if (in_array($payment->status, [1, -1])) {
            if (!is_null($payment->callback_url)) {
                CallbackJob::dispatch($payment)->onQueue('callback');
            }

            Redis::decrBy('unchecked_order_count', 1);
        }
    }

    /**
     * Handle the payment "deleted" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function deleted(Payment $payment)
    {
        if ($payment->status == 0) {
            Redis::decrBy('unchecked_order_count', 1);
        }
    }

    /**
     * Handle the payment "restored" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function restored(Payment $payment)
    {
        //
    }

    /**
     * Handle the payment "force deleted" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function forceDeleted(Payment $payment)
    {
        //
    }
}
