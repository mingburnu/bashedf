<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Node.
 *
 * @package namespace App\Entities;
 */
class Node extends Model implements Transformable
{
    use TransformableTrait;
    use NodeTrait;

    protected $fillable = [
        'user_id'
    ];

    public function getLftName(): string
    {
        return '_lft';
    }

    public function getRgtName(): string
    {
        return '_rgt';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'parent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
