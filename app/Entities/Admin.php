<?php

namespace App\Entities;

use Google2FA;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Admin.
 *
 * @package namespace App\Entities;
 */
class Admin extends Authenticatable implements Transformable
{
    use TransformableTrait;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    use HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'google2fa_secret'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['role'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function loginRecords(): MorphMany
    {
        return $this->morphMany(Activity::class, 'causer')->where('log_name', 'login');
    }

    /**
     * @return User[]|Collection|\Illuminate\Support\Collection
     */
    public function getMerchantsAttribute(): Collection|array|\Illuminate\Support\Collection
    {
        if ($this->hasRole(1)) {
            return User::whereNotNull(config('auth.guards.api.storage_key'))->get();
        } else {
            return $this->relations['users'] ?? $this->users()->get();
        }
    }

    public function getQrCodeAttribute(): ?string
    {
        if (!is_null($this->google2fa_secret)) {
            return Google2FA::getQRCodeInline(config('app.name'), $this->email, $this->google2fa_secret);
        } else {
            return null;
        }
    }

    public function getRoleAttribute(): ?string
    {
        return $this->hasRole(1) ? trans('ui.admins.role.0') : trans('ui.admins.role.1');
    }
}