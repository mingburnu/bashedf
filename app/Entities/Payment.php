<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\Activitylog\Models\Activity;

/**
 * Class Payment.
 *
 * @package namespace App\Entities;
 */
class Payment extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'user_id', 'account_name', 'customized_id', 'bank_name', 'account_number', 'branch', 'amount', 'processing_fee', 'status', 'total_amount', 'callback_url', 'admin_id'
    ];

    protected $appends = ['process', 'result', 'order_name', 'checked_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //錢包紀錄
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'orderable');
    }

    /**
     * @return MorphOne
     */
    public function sendRecord(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')->where('log_name', 'clerk');
    }

    public function paybackStamp(): HasOne
    {
        return $this->hasOne(PaybackStamp::class);
    }

    public function rewindStamp(): HasOne
    {
        return $this->hasOne(RewindStamp::class);
    }

    public function callbackRecord(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')->where('log_name', 'callback');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
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
        return trans('ui.payment');
    }

    function getCheckedAtAttribute(): ?string
    {
        return in_array($this->status, [1, -1]) ? $this->updated_at->toJSON() ?? null : null;
    }
}
