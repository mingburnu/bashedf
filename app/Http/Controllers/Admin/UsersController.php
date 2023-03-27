<?php

namespace App\Http\Controllers\Admin;

use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\BankCardRepository;
use App\Repositories\ContractRepository;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use DataTables;
use DB;
use Hash;
use Hashids;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Str;
use Throwable;

class UsersController extends Controller
{
    protected UserRepository $repository;

    protected UserValidator $validator;

    /**
     * UsersController constructor.
     *
     * @param UserRepository $repository
     * @param UserValidator $validator
     */
    public function __construct(UserRepository $repository, UserValidator $validator)
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
            $search_value = $this->escapeSpecificChars(request('search.value'));

            $recordsTotal = $this->repository->whereNotNull('api_key')->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', "name:$search_value;email:$search_value;merchant_id:$search_value");
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))
                    ->whereNotNull('api_key')
                    ->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->with(['wallet', 'bankCards'])->whereNotNull('api_key')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('admin.users.index');
    }

    /**
     * Show the form for creating the specified resource.
     *
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        $allBankCards = app(BankCardRepository::class)->all();
        return view('admin.users.create', compact('allBankCards'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserCreateRequest $request
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(UserCreateRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $password = Hash::make($request->input('password'));

            /* @var User $user */
            $user = $this->repository->create($request->only('name', 'email', 'company', 'phone') + compact('password'));
            $user_id = $user->getKey();
            $merchant_id = now()->unix() + $user_id * $user_id;
            $api_key = Hashids::encode($user_id, now()->unix(), rand(0, 9), rand(0, 9), rand(0, 9));
            $this->repository->update(compact('merchant_id', 'api_key'), $user_id);
            app(ContractRepository::class)->create(compact('user_id') + $request->only('min_deposit_amount', 'max_deposit_amount', 'min_payment_amount', 'max_payment_amount', 'deposit_processing_fee_percent', 'payment_processing_fee'));

            $this->repository->sync($user_id, 'bankCards', $request->input('bank_cards') ?? []);

            DB::commit();
            return redirect()->back()->with('result', true);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('result', false);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     *
     * @return Application|Factory|View
     */
    public function edit(User $user): Factory|View|Application
    {
        $allBankCards = app(BankCardRepository::class)->all();
        return view('admin.users.edit', compact('user', 'allBankCards'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $name = $request->input('name');
            $password = $request->input('password');
            $company = $request->input('company');
            $phone = $request->input('phone');

            if (empty($password)) {
                $this->repository->lockAndUpdate(compact('name', 'company', 'phone'), $user->id);
            } else {
                $password = Hash::make($password);
                $this->repository->lockAndUpdate(compact('name', 'company', 'phone', 'password'), $user->id);
            }

            app(ContractRepository::class)->update($request->only('min_deposit_amount', 'max_deposit_amount', 'min_payment_amount', 'max_payment_amount', 'deposit_processing_fee_percent', 'payment_processing_fee'), $user->id);

            $this->repository->sync($user->id, 'bankCards', $request->input('bank_cards') ?? []);
            activity('management')->performedOn($user)->causedBy($this->admin)->log($request->ip());
            $result = true;
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $result = false;
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
        return redirect()->back()->with('result', $result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockAndDelete($user->id);
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
     * @param User $user
     * @return JsonResponse
     * @throws Throwable
     */
    public function clear(User $user): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockAndUpdate(['google2fa_secret' => null], $user->id);
            DB::commit();
            return response()->json([]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }

    public function display(User $user): JsonResponse
    {
        try {
            $this->validator->setRules(['password' => ['required', 'password:admin']])->with(request()->all())->passesOrFail();
            $messages = [$user->api_key];
            return response()->json(compact('messages'));
        } catch (ValidatorException $e) {
            $errors = $e->getMessageBag();
            return response()->json(compact('errors'), 422);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}