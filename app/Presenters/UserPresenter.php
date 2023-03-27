<?php

namespace App\Presenters;

use App\Transformers\UserTransformer;
use JetBrains\PhpStorm\Pure;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserPresenter.
 *
 * @package namespace App\Presenters;
 */
class UserPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return UserTransformer
     */
    #[Pure] public function getTransformer(): UserTransformer
    {
        return new UserTransformer();
    }
}
