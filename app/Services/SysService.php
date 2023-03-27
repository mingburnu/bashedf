<?php

namespace App\Services;

use App\Repositories\DepositRepository;
use App\Repositories\UserRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use App\Repositories\WhiteIpRepository;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use DB;
use Log;
use Prettus\Validator\Exceptions\ValidatorException;
use Redis;
use Throwable;

class SysService
{
    /**
     * @param int $user_id
     * @param string $ip
     * @return bool
     * @throws ValidatorException
     */
    public function insertWhiteIP(int $user_id, string $ip): bool
    {
        $user = app(UserRepository::class)->whereId($user_id)->whereNotNull('api_key')->lock()->firstOrFail();
        if (!$user->whiteIps()->pluck('ip')->contains($ip)) {
            app(WhiteIpRepository::class)->create(compact('user_id', 'ip'));
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $user_id
     * @param string $ip
     * @return bool
     * @throws Throwable
     */
    public function dropWhiteIP(int $user_id, string $ip): bool
    {
        return app(WhiteIpRepository::class)->deleteWhere(compact('user_id', 'ip'));
    }

    public function delUncheckedOrderCount(): int
    {
        return Redis::del('unchecked_order_count');
    }

    public function checkTransactionOrder($user_id): bool
    {
        $logs = app(TransactionRepository::class)->whereUserId($user_id)->orderBy('id')->get();
        foreach ($logs as $k => $log) {
            if ($k + 1 === $logs->count()) {
                break;
            }

            $next = $logs[$k + 1];
            if ($log->new_balance !== $next->old_balance) {
                var_dump($log->id);
                return false;
            }
        }
        return true;
    }

    public function resetOrderCount(): int
    {
        return Redis::del('unchecked_order_count');
    }

    /**
     * @param int $id
     * @param string $amount
     * @return bool
     * @throws Throwable
     */
    public function resetDepositAmount(int $id, string $amount): bool
    {
        try {
            DB::beginTransaction();
            $deposit = app(DepositRepository::class)->whereId($id)->lock()->firstOrFail();
            $depositFeePercent = BigDecimal::of($deposit->user->contract->deposit_processing_fee_percent)->exactlyDividedBy('100');
            $processing_fee = $depositFeePercent->multipliedBy($amount)->toScale(0, RoundingMode::HALF_UP)->jsonSerialize();
            $total_amount = BigDecimal::of($amount)->minus($processing_fee)->jsonSerialize();
            $diff = BigDecimal::of($total_amount)->minus($deposit->total_amount)->jsonSerialize();
            app(DepositRepository::class)->whereId($id)->getQuery()->update(compact('amount', 'total_amount', 'processing_fee'));

            $walletLog = $deposit->transactions[0];
            app(TransactionRepository::class)->whereId($walletLog->id)->lock()->getQuery()->increment('transaction_amount', $diff);
            app(TransactionRepository::class)->whereUserId($deposit->user_id)->where('id', '>', $walletLog->id)->lock()->getQuery()->increment('old_balance', $diff);
            app(TransactionRepository::class)->whereUserId($deposit->user_id)->where('id', '>=', $walletLog->id)->lock()->getQuery()->increment('new_balance', $diff);
            app(WalletRepository::class)->whereUserId($deposit->user_id)->lock()->getQuery()->increment('balance', $diff);

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }
}