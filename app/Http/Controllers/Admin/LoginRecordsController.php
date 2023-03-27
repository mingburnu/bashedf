<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ActivityRepository;
use DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Str;

class LoginRecordsController extends Controller
{
    protected ActivityRepository $repository;

    /**
     * LoginRecordsController constructor.
     *
     * @param ActivityRepository $repository
     */
    public function __construct(ActivityRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * @return Application|Factory|View|JsonResponse
     * @throws RepositoryException
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $search_value = $this->escapeSpecificChars(request('search.value'));

            $recordsTotal = $this->repository->whereCauserId($this->admin->id)->whereCauserType($this->admin->getMorphClass())
                ->whereLogName('login')->count();

            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', "description:$search_value");
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))
                    ->whereCauserId($this->admin->id)->whereCauserType($this->admin->getMorphClass())
                    ->whereLogName('login')->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $this->repository->resetModel();
            $source = $this->repository->whereCauserId($this->admin->id)->whereCauserType($this->admin->getMorphClass())
                ->whereLogName('login')->orderBy('id', 'desc')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('admin.login-records.index');
    }
}