<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class MerchantSetting.
 *
 * @package namespace App\Entities;
 */
class MerchantSetting extends Model implements Transformable
{
    use TransformableTrait;

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'default_payment_callback_url', 'api_token_switch'
    ];
}
