<?php

namespace App\Presenters;

use App\Transformers\PaymentTransformer;
use JetBrains\PhpStorm\Pure;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class PaymentPresenter.
 *
 * @package namespace App\Presenters;
 */
class PaymentPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return PaymentTransformer
     */
    #[Pure] public function getTransformer(): PaymentTransformer
    {
        return new PaymentTransformer();
    }
}
