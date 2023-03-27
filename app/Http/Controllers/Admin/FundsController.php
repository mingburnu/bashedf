<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\DateTimeIntervalCriteria;
use App\Entities\Fund;
use App\Entities\User;
use App\Events\ThawFundEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\FundCreateRequest;
use App\Http\Requests\FundUpdateRequest;
use App\Repositories\FundRepository;
use App\Services\WalletService;
use App\Validators\FundValidator;
use Brick\Math\BigDecimal;
use DataTables;
use DB;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Log;
use Prettus\Repository\Exceptions\RepositoryException;
use Throwable;

/**
 * Class FundsController.
 *
 * @package namespace App\Http\Controllers\Admin;
 */
class FundsController extends Controller
{
    protected FundRepository $repository;

    protected FundValidator $validator;

    /**
     * FundsController constructor.
     *
     * @param FundRepository $repository
     * @param FundValidator $validator
     */
    public function __construct(FundRepository $repository, FundValidator $validator)
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

            $recordsFiltered = $this->repository->pushCriteria(app(DateTimeIntervalCriteria::class))->whereUserId($user->id)->count();
            if ($recordsFiltered === 0) {
                return datatables([])->with('recordsTotal', $recordsTotal)->with('recordsFiltered', $recordsFiltered)->toJson();
            }

            $this->repository->resetModel();
            $source = $this->repository->whereUserId($user->id)->with(['admin'])->orderBy('id', 'desc')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('admin.users.funds.index', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FundCreateRequest $request
     * @param User $user
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(FundCreateRequest $request, User $user): JsonResponse
    {
        try {
            DB::beginTransaction();
            $amount = $request->input('amount');
            $cause = $request->input('cause');
            $admin_id = $this->admin->id;
            $user_id = $user->id;

            /* @var Fund $fund */
            $fund = $this->repository->create(compact('amount', 'cause', 'admin_id', 'user_id'));
            $transaction_amount = BigDecimal::of($amount)->negated()->jsonSerialize();
            app(WalletService::class)->updateBalance($user_id, $transaction_amount, 'seizing', $fund->getMorphClass(), $fund->getKey());
            DB::commit();

            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FundUpdateRequest $request
     * @param Fund $fund
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(FundUpdateRequest $request, Fund $fund): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockAndUpdate(['unfrozen' => 1], $fund->id);
            activity('thaw')->performedOn($fund)->causedBy($this->admin)->log($request->ip());

            $orderable_type = $fund->getMorphClass();
            app(WalletService::class)->updateBalance($fund->user_id, $fund->amount, 'thaw', $orderable_type, $fund->id);
            DB::commit();

            broadcast(new ThawFundEvent($fund));
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}