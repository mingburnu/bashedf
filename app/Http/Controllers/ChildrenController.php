<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Requests\ChildCreateRequest;
use App\Http\Requests\ChildUpdateRequest;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use DataTables;
use DB;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Kalnoy\Nestedset\QueryBuilder;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Str;
use Throwable;

class ChildrenController extends Controller
{
    protected UserRepository $repository;

    protected UserValidator $validator;

    /**
     * ChildrenController constructor.
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

            $recordsTotal = $this->merchant->node->descendants()->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', "name:$search_value;email:$search_value");
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))
                    ->whereHas('node.parent', function (QueryBuilder $q) {
                        $q->where('user_id', $this->merchant->id);
                    })->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->whereHas('node.parent', function (QueryBuilder $q) {
                $q->where('user_id', $this->merchant->id);
            })->newQuery();

            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('children.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        return view('children.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ChildCreateRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(ChildCreateRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $password = $request->input('password');

            $this->repository->create([
                    'password' => Hash::make($password),
                    'company' => $this->merchant->company,
                    'permission' => 1
                ] + $request->only(['name', 'email', 'phone']));

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
     * Show the form for editing the specified resource.
     *
     * @param User $child
     * @return Application|Factory|View
     */
    public function edit(User $child): View|Factory|Application
    {
        abort_unless($child->node->parent->user->id === $this->merchant->id, 403);
        return view('children.edit', compact('child'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ChildUpdateRequest $request
     * @param User $user
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(ChildUpdateRequest $request, User $user): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $name = $request->input('name');
            $password = $request->input('password');

            if (strlen($password) === 0) {
                $this->repository->lockAndUpdate(compact('name'), $user->id);
            } else {
                $password = Hash::make($password);
                $this->repository->lockAndUpdate(compact('name', 'password'), $user->id);
            }

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
     * @param User $child
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(User $child): JsonResponse
    {
        if ($child->node->parent->user->id !== $this->merchant->id) {
            return response()->json([], 403);
        }

        try {
            DB::beginTransaction();
            $this->repository->lockAndDelete($child->id);
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