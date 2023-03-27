<?php

namespace App\Http\Controllers;

use App\Criteria\DateTimeIntervalCriteria;
use App\Criteria\AuthUserCriteria;
use App\Repositories\TransactionRepository;
use App\Validators\TransactionValidator;
use DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Prettus\Repository\Exceptions\RepositoryException;

class TransactionsController extends Controller
{
    protected TransactionRepository $repository;

    protected TransactionValidator $validator;

    /**
     * TransactionsController constructor.
     *
     * @param TransactionRepository $repository
     * @param TransactionValidator $validator
     */
    public function __construct(TransactionRepository $repository, TransactionValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @return View|Factory|JsonResponse|Application
     * @throws RepositoryException
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $recordsTotal = $this->repository->pushCriteria(app(AuthUserCriteria::class))->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            $recordsFiltered = $this->repository->pushCriteria(app(DateTimeIntervalCriteria::class))->count();
            if ($recordsFiltered === 0) {
                return datatables([])->with('recordsTotal', $recordsTotal)->with('recordsFiltered', $recordsFiltered)->toJson();
            }

            $source = $this->repository->with(['orderable'])->orderBy('id', 'desc')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('transactions.index');
    }
}