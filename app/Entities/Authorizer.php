<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Authorizer.
 *
 * @package namespace App\Entities;
 */
class Authorizer extends Model implements Transformable
{
    use TransformableTrait;

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'supervising', 'api', 'boolean_index', 'additional_parameters'];

    protected $casts = [
        'additional_parameters' => 'collection'
    ];
}
