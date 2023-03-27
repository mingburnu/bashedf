<?php

namespace App\Http\Controllers\Admin;

use App\Entities\BankCard;
use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Log;
use App\Http\Requests\BankCardCreateRequest;
use App\Http\Requests\BankCardUpdateRequest;
use App\Repositories\BankCardRepository;
use App\Validators\BankCardValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Str;
use Throwable;

/**
 * Class BankCardsController.
 *
 * @package namespace App\Http\Controllers\Admin;
 */
class BankCardsController extends Controller
{
    protected BankCardRepository $repository;

    protected BankCardValidator $validator;

    /**
     * BankCardsController constructor.
     *
     * @param BankCardRepository $repository
     * @param BankCardValidator $validator
     */
    public function __construct(BankCardRepository $repository, BankCardValidator $validator)
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
            $search_value = $this->escapeSpecificChars(request('search.value'));

            $recordsTotal = $this->repository->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', "account_name:$search_value;account_number:$search_value;bank_name:$search_value");
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('admin.bank-cards.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('admin.bank-cards.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BankCardCreateRequest $request
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(BankCardCreateRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->create($request->all());
            DB::commit();
            return redirect()->back()->with('result', true);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('result', false)->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BankCard $bank_card
     *
     * @return Application|Factory|View
     */
    public function edit(BankCard $bank_card): View|Factory|Application
    {
        return view('admin.bank-cards.edit', compact('bank_card'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BankCardUpdateRequest $request
     * @param BankCard $bank_card
     *
     * @return RedirectResponse
     *
     * @throws Throwable
     */
    public function update(BankCardUpdateRequest $request, BankCard $bank_card): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockAndUpdate($request->all(), $bank_card->id);
            DB::commit();
            return redirect()->back()->with('result', true);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('result', false)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BankCard $bank_card
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(BankCard $bank_card): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockInRepository()->findOrFail($bank_card->getKey())->users()->detach();
            $this->repository->deleteWhere($bank_card->only([$bank_card->getKeyName()]));
            DB::commit();
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}