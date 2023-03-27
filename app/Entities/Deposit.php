<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Deposit.
 *
 * @package namespace App\Entities;
 */
class Deposit extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'user_id', 'amount', 'processing_fee', 'total_amount', 'account_name', 'bank_name', 'account_number', 'bank_district', 'bank_address', 'status', 'admin_id'
    ];

    protected $appends = ['process', 'result', 'order_name', 'checked_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    //錢包紀錄
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'orderable');
    }

    function getProcessAttribute(): ?string
    {
        return trans('ui.orders.process')[$this->status] ?? null;
    }

    function getResultAttribute(): ?string
    {
        return trans('ui.orders.result')[$this->status] ?? null;
    }

    function getOrderNameAttribute(): ?string
    {
        return trans('ui.deposit');
    }

    function getCheckedAtAttribute(): ?string
    {
        return in_array($this->status, [1, -1]) ? $this->updated_at->toJSON() ?? null : null;
    }
}
