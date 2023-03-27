<?php

namespace App\Transformers;

use App\Entities\Payment;
use League\Fractal\TransformerAbstract;

/**
 * Class PaymentTransformer.
 *
 * @package namespace App\Transformers;
 */
class PaymentTransformer extends TransformerAbstract
{
    /**
     * Transform the Payment entity.
     *
     * @param Payment|null $model
     * @return array
     */
    public function transform(Payment $model = null): array
    {
        if (is_null($model)) {
            return Payment::newModelInstance()->only([]);
        } else {
            $model->status = match ($model->status) {
                1 => $model->paybackStamp ? -1 : 1,
                -1 => $model->rewindStamp ? 1 : -1,
                default => $model->status
            };
        }

        return $model->only(['amount', 'checked_at', 'customized_id', 'order_id', 'result', 'status']);
    }
}