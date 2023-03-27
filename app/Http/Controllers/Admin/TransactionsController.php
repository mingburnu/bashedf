<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\DateTimeIntervalCriteria;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use App\Validators\TransactionValidator;
use DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class TransactionsController.
 *
 * @package namespace App\Http\Controllers\Admin;
 */
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
     * Display a listing of the resource.
     *
     * @param User $user
     * @return Application|Factory|View|JsonResponse
     * @throws RepositoryException
     */
    public function index(User $user): View|Factory|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $recordsTotal = $this->repository->whereUserId($user->id)->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            $recordsFiltered = $this->repository->pushCriteria(app(DateTimeIntervalCriteria::class))
                ->whereUserId($user->id)->count();
            if ($recordsFiltered === 0) {
                return datatables([])->with('recordsTotal', $recordsTotal)->with('recordsFiltered', $recordsFiltered)->toJson();
            }

            $this->repository->resetModel();
            $source = $this->repository->whereUserId($user->id)->with(['orderable'])->orderBy('id', 'desc')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }
        return view('admin.users.transactions.index', compact('user'));
    }
}