<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PermissionRepositoryEloquent extends BaseRepository implements PermissionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Permission::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable  = ['name'];
    }
}
