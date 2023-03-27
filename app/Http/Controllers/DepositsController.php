<?php

namespace App\Http\Controllers;

use App\Criteria\AuthUserCriteria;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use DataTables;
use DB;
use Hashids;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use App\Http\Requests\DepositCreateRequest;
use App\Repositories\DepositRepository;
use App\Validators\DepositValidator;
use Str;
use Throwable;

/**
 * Class DepositsController.
 *
 * @package namespace App\Http\Controllers;
 */
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
     * @return View|Factory|JsonResponse|Application
     * @throws RepositoryException
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $qualifiedCreatedAtColumn = $this->repository->getQualifiedCreatedAtColumn();
            $search_value = $this->escapeSpecificChars(request('search.value'));

            $recordsTotal = $this->repository->pushCriteria(app(AuthUserCriteria::class))->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', $search_value);
                request()->request->set('searchFields', 'order_id:like binary');
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->orderBy($qualifiedCreatedAtColumn, 'desc')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('deposits.index');
    }

    /**
     * Show the form for creating the specified resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('deposits.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DepositCreateRequest $request
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(DepositCreateRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $amount = $request->input('amount');
            $bank_card_id = $request->input('bank_card_id');

            $depositFeePercent = BigDecimal::of($this->merchant->contract->deposit_processing_fee_percent)->exactlyDividedBy(100);
            $processing_fee = $depositFeePercent->multipliedBy($amount)->toScale($this->repository->getFieldSchema('processing_fee')->getScale(), RoundingMode::HALF_UP)->jsonSerialize();
            $total_amount = BigDecimal::of($amount)->minus($processing_fee);

            $user_id = $this->merchant->id;
            $uuid = str_replace('-', '', Str::uuid()->jsonSerialize());
            $order_id = Hashids::encode(base_convert($uuid, 16, 10));

            $bank = $this->merchant->bankCards->find($bank_card_id);
            $status = 0;

            $this->repository->create(compact('order_id', 'user_id', 'amount', 'processing_fee', 'total_amount', 'status') + $bank->toArray());

            DB::commit();
            return redirect()->back()->with('result', true);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput()->with('result', false);
        }
    }
}