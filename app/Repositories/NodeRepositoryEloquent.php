<?php

namespace App\Repositories;

use Kalnoy\Nestedset\QueryBuilder;
use App\Entities\Node;
use Prettus\Repository\Events\RepositoryEntityCreated;
use Prettus\Repository\Events\RepositoryEntityUpdated;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class NodeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NodeRepositoryEloquent extends Repository implements NodeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Node::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @param int $user_id
     * @return Node
     * @throws RepositoryException
     */
    public function createRoot(int $user_id): Node
    {
        /* @var Node $model */
        $model = $this->model->newInstance(compact('user_id'));
        $model->saveAsRoot();
        $this->resetModel();

        event(new RepositoryEntityCreated($this, $model));

        return $model;
    }

    /**
     * @param int|string $parent_user_id
     * @param int|string $user_id
     * @return Node
     * @throws RepositoryException
     */
    public function createChild(int|string $parent_user_id, int|string $user_id): Node
    {
        /* @var Node $model */
        $model = $this->model;
        $parent = $model->whereUserId($parent_user_id)->firstOrFail();
        $this->resetModel();

        /* @var Node $model */
        $model = $this->model->newInstance(compact('user_id'));
        $parent->prependNode($model);
        $this->resetModel();

        event(new RepositoryEntityCreated($this, $model));

        return $model;
    }

    /**
     * @param int|string $user_id
     * @return Node
     * @throws RepositoryException
     */
    public function convertIntoRoot(int|string $user_id): Node
    {
        /* @var Node $model */
        $model = $this->model;
        $model = $model->whereUserId($user_id)->firstOrFail();
        $model->makeRoot()->save();
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $model;
    }

    /**
     * @param int $parent_user_id
     * @param int $user_id
     * @return Node
     * @throws RepositoryException
     */
    public function moveChild(int $parent_user_id, int $user_id): Node
    {
        /* @var Node $model */
        $model = $this->model;
        $parent = $model->whereUserId($parent_user_id)->firstOrFail();
        $this->resetModel();

        /* @var Node $model */
        $model = $this->model;
        $model = $model->whereUserId($user_id)->firstOrFail();
        $parent->prependNode($model);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $model;
    }

    /**
     * @return int
     * @throws RepositoryException
     */
    public function fixTree(): int
    {
        /* @var QueryBuilder $model */
        $model = $this->model;
        $total = $model->fixTree();
        $this->resetModel();

        return $total;
    }
}