<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use Brick\Math\BigDecimal;
use Prettus\Validator\Exceptions\ValidatorException;

class WalletService
{
    /**
     * @param int $user_id
     * @param string $transaction_amount
     * @param string $type
     * @param string|null $orderable_type
     * @param int|null $orderable_id
     * @throws ValidatorException
     */
    function updateBalance(int $user_id, string $transaction_amount, string $type = 'sys', string $orderable_type = null, int $orderable_id = null)
    {
        $wallet = app(WalletRepository::class)->whereUserId($user_id)->lock()->firstOrFail();
        $old_balance = $wallet->balance;
        $new_balance = BigDecimal::of($wallet->balance)->plus($transaction_amount)->jsonSerialize();
        app(TransactionRepository::class)->create(compact('user_id', 'old_balance', 'transaction_amount', 'new_balance', 'type', 'orderable_type', 'orderable_id'));
        $balance = $new_balance;
        app(WalletRepository::class)->update(compact('balance'), $user_id);
    }
}