<?php

namespace App\Entities;

use Google2FA;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\Activitylog\Models\Activity;

/**
 * Class User.
 *
 * @package namespace App\Entities;
 */
class User extends Authenticatable implements Transformable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TransformableTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'company', 'phone', 'api_key', 'google2fa_secret', 'merchant_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'google2fa_secret', 'api_key'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    public function merchantSetting(): HasOne
    {
        return $this->hasOne(MerchantSetting::class, 'user_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id', 'id');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'user_id', 'id');
    }

    public function bankCards(): BelongsToMany
    {
        return $this->belongsToMany(BankCard::class);
    }

    public function loginRecords(): MorphMany
    {
        return $this->morphMany(Activity::class, 'causer')->where('log_name', 'login');
    }

    public function node(): HasOne
    {
        return $this->hasOne(Node::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function authorizer(): HasOne
    {
        return $this->hasOne(Authorizer::class);
    }

    public function whiteIps(): HasMany
    {
        return $this->hasMany(WhiteIp::class);
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class);
    }

    public function getQrCodeAttribute(): ?string
    {
        if (!is_null($this->google2fa_secret)) {
            return Google2FA::getQRCodeInline(config('app.name'), $this->email, $this->google2fa_secret);
        } else {
            return null;
        }
    }
}
