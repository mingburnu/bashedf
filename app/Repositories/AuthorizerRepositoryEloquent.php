<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Authorizer;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class AuthorizerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AuthorizerRepositoryEloquent extends Repository implements AuthorizerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Authorizer::class;
    }

    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
