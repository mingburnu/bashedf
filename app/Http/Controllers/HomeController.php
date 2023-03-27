<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityRepository;
use App\Repositories\DepositRepository;
use App\Repositories\PaymentRepository;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Prettus\Repository\Exceptions\RepositoryException;

class HomeController extends Controller
{
    /**
     * @return Factory|View|Application
     * @throws RepositoryException
     */
    public function index(): Factory|View|Application
    {
        $balance = $this->merchant->wallet->balance ?? 0;
        $previousLoginRecord = app(ActivityRepository::class)->whereCauserId($this->clerk->id)
                ->whereCauserType($this->clerk->getMorphClass())->whereLogName('login')
                ->orderBy('id', 'desc')->limit(2)->get()[1] ?? null;
        $whereSuccess = [['created_at', '>=', today()], 'user_id' => $this->merchant->id, 'status' => 1];
        $whereFail = [['created_at', '>=', today()], 'user_id' => $this->merchant->id, 'status' => -1];
        $todayDepositTotalAmountSum = app(DepositRepository::class)->sumWhere('total_amount', $whereSuccess);
        $todayPaymentTotalAmountSum = BigDecimal::of(app(PaymentRepository::class)->hasNo('paybackStamp')->sumWhere('total_amount', $whereSuccess))
            ->plus(app(PaymentRepository::class)->has('rewindStamp')->sumWhere('amount', $whereFail))->jsonSerialize();
        return view('index', compact('todayDepositTotalAmountSum', 'todayPaymentTotalAmountSum', 'balance', 'previousLoginRecord'));
    }
}
