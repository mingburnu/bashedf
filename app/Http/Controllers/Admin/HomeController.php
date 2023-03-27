<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\DepositRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\WalletRepository;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Prettus\Repository\Exceptions\RepositoryException;

class HomeController extends Controller
{
    /**
     * @return Application|Factory|View
     * @throws RepositoryException
     */
    public function index(): View|Factory|Application
    {
        $whereSuccess = [['created_at', '>=', today()], 'status' => 1];
        $whereFail = [['created_at', '>=', today()], 'status' => -1];

        $todayPaymentAmountsSum = number_format(BigDecimal::of(app(PaymentRepository::class)->hasNo('paybackStamp')->sumWhere('amount', $whereSuccess))
            ->plus(app(PaymentRepository::class)->has('rewindStamp')->sumWhere('amount', $whereFail))
            ->jsonSerialize(), 2);
        $todayPaymentProcessingFeesSum = number_format(BigDecimal::of(app(PaymentRepository::class)->hasNo('paybackStamp')->sumWhere('processing_fee', $whereSuccess))
            ->plus(app(PaymentRepository::class)->has('rewindStamp')->sumWhere('processing_fee', $whereFail))
            ->jsonSerialize());
        $todayDepositProcessingFeesSum = number_format(app(DepositRepository::class)->sumWhere('processing_fee', $whereSuccess));
        $todayWalletBalancesSum = number_format(app(WalletRepository::class)->has('user')->sum('balance'), 2);

        return view('admin.index', compact('todayPaymentAmountsSum', 'todayPaymentProcessingFeesSum', 'todayDepositProcessingFeesSum', 'todayWalletBalancesSum'));
    }
}
