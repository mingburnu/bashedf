<?php

namespace App\Observers;

use App\Entities\Deposit;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use Brick\Math\BigDecimal;
use Prettus\Validator\Exceptions\ValidatorException;
use Redis;

class DepositObserver
{
    /**
     * Handle the deposit "created" event.
     *
     * @param Deposit $deposit
     * @return void
     */
    public function created(Deposit $deposit)
    {
        Redis::incrBy('unchecked_order_count', 1);
    }

    /**
     * Handle the deposit "updated" event.
     *
     * @param Deposit $deposit
     * @return void
     * @throws ValidatorException
     */
    public function updated(Deposit $deposit)
    {
        $transactions = $deposit->transactions;
        if ($deposit->status == 1 && count($transactions) == 0) {
            $user_id = $deposit->user_id;

            $wallet = app(WalletRepository::class)->whereKey($user_id)->lock()->first();
            $old_balance = $wallet->balance;
            $transaction_amount = $deposit->total_amount;
            $new_balance = BigDecimal::of($old_balance)->plus($transaction_amount)->jsonSerialize();
            $type = 'deposit';
            $orderable_type = $deposit->getMorphClass();
            $orderable_id = $deposit->id;
            app(TransactionRepository::class)->create(compact('user_id', 'old_balance', 'transaction_amount', 'new_balance', 'type', 'orderable_type', 'orderable_id'));

            $balance = $new_balance;
            app(WalletRepository::class)->update(compact('balance'), $user_id);
        }

        Redis::decrBy('unchecked_order_count', 1);
    }

    /**
     * Handle the deposit "deleted" event.
     *
     * @param Deposit $deposit
     * @return void
     */
    public function deleted(Deposit $deposit)
    {
        if ($deposit->status == 0) {
            Redis::decrBy('unchecked_order_count', 1);
        }
    }

    /**
     * Handle the deposit "restored" event.
     *
     * @param Deposit $deposit
     * @return void
     */
    public function restored(Deposit $deposit)
    {
        //
    }

    /**
     * Handle the deposit "force deleted" event.
     *
     * @param Deposit $deposit
     * @return void
     */
    public function forceDeleted(Deposit $deposit)
    {
        //
    }
}
