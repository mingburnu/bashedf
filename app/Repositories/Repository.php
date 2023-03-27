<?php

namespace App\Repositories;

use DB;
use Doctrine\DBAL\Schema\Column;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Events\RepositoryEntityUpdated;
use Prettus\Repository\Events\RepositoryEntityUpdating;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

abstract class Repository extends BaseRepository
{
    /**
     * Sum results of repository
     *
     * @param string $column
     * @param array $where
     *
     * @return int|string
     * @throws RepositoryException
     */
    public function sumWhere(string $column, array $where = []): int|string
    {
        $this->applyCriteria();
        $this->applyScope();

        if ($where) {
            $this->applyConditions($where);
        }

        $result = $this->model->sum($column);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * Check if entity has no relation
     *
     * @param string $relation
     *
     * @return Repository
     */
    public function hasNo(string $relation): Repository
    {
        $this->model = $this->model->has($relation, '<');

        return $this;
    }

    /**
     * Lock the selected rows in the table.
     *
     * @param bool $value
     * @return Repository
     */
    public function lockInRepository(bool $value = true): Repository
    {
        $this->model = $this->model->lock($value);

        return $this;
    }

    /**
     * @param string $column
     * @return Column
     */
    public function getFieldSchema(string $column): Column
    {
        return DB::connection()->getDoctrineColumn($this->model->getTable(), $column);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws RepositoryException
     * @throws ValidatorException
     */
    public function lockAndUpdate(array $attributes, $id): mixed
    {
        $this->applyScope();

        if (!is_null($this->validator)) {
            $model = $this->model->newInstance();
            $model->setRawAttributes([]);
            $model->setAppends([]);
            if ($this->versionCompare($this->app->version(), "5.2.*", ">")) {
                $attributes = $model->forceFill($attributes)->makeVisible($this->model->getHidden())->toArray();
            } else {
                $model->forceFill($attributes);
                $model->makeVisible($this->model->getHidden());
                $attributes = $model->toArray();
            }

            $this->validator->with($attributes)->setId($id)->passesOrFail(ValidatorInterface::RULE_UPDATE);
        }

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->model->lock()->findOrFail($id);

        event(new RepositoryEntityUpdating($this, $model));

        $model->fill($attributes);
        $model->save();

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * @param $id
     * @return bool|null
     */
    public function lockAndDelete($id): ?bool
    {
        $this->lockInRepository();
        return parent::delete($id);
    }
}