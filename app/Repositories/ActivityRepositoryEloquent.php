<?php

namespace App\Repositories;

use Spatie\Activitylog\Models\Activity;

/**
 * Class ActivityRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ActivityRepositoryEloquent extends Repository implements ActivityRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Activity::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = ['description'];
    }
}
