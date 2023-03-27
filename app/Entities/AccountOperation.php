<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AccountOperation.
 *
 * @package namespace App\Entities;
 */
class AccountOperation extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'amount', 'admin_id', 'cause'];

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'orderable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
