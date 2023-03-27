<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\MerchantsByAdminCriteria;
use App\Entities\Deposit;
use App\Events\CheckedDepositEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepositUpdateRequest;
use App\Repositories\DepositRepository;
use App\Validators\DepositValidator;
use DataTables;
use DB;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Str;
use Throwable;

class DepositsController extends Controller
{
    protected DepositRepository $repository;

    protected DepositValidator $validator;

    /**
     * DepositsController constructor.
     *
     * @param DepositRepository $repository
     * @param DepositValidator $validator
     */
    public function __construct(DepositRepository $repository, DepositValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws RepositoryException
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $qualifiedCreatedAtColumn = $this->repository->getQualifiedCreatedAtColumn();
            $search_value = $this->escapeSpecificChars(request('search.value'));

            $recordsTotal = $this->repository->pushCriteria(app(MerchantsByAdminCriteria::class))->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', $search_value);
                request()->request->set('searchFields', 'order_id:like binary;user.name:=');
                request()->request->set('searchJoin', 'or');
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->with(['user', 'admin', 'transactions'])->orderBy($qualifiedCreatedAtColumn, 'desc')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('admin.deposits.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DepositUpdateRequest $request
     * @param Deposit $deposit
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(DepositUpdateRequest $request, Deposit $deposit): JsonResponse
    {
        try {
            DB::beginTransaction();
            $admin_id = $this->admin->id;
            $deposit = $this->repository->lockAndUpdate($request->only('status') + compact('admin_id'), $deposit->id);
            activity('check')->performedOn($deposit)->causedBy($this->admin)->log($request->ip());
            DB::commit();

            broadcast((new CheckedDepositEvent($deposit, $this->admin->name)));
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}