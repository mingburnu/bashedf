<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Admin;
use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRepository;
use DataTables;
use DB;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\AdminCreateRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Repositories\AdminRepository;
use App\Validators\AdminValidator;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Str;
use Throwable;

/**
 * Class AdminsController.
 *
 * @package namespace App\Http\Controllers\Admin;
 */
class AdminsController extends Controller
{
    protected AdminRepository $repository;

    protected AdminValidator $validator;

    /**
     * AdminsController constructor.
     *
     * @param AdminRepository $repository
     * @param AdminValidator $validator
     */
    public function __construct(AdminRepository $repository, AdminValidator $validator)
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

            $recordsTotal = $this->repository->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', "name:$search_value;email:$search_value");
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->with(['users', 'permissions'])->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('admin.admins.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function create(): Factory|View|Application
    {
        $users = app(UserRepository::class)->whereNotNull('api_key')->get();
        $permissions = app(PermissionRepository::class)->whereNotIn('id', [1, 2])->get();
        return view('admin.admins.create', compact('users', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdminCreateRequest $request
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(AdminCreateRequest $request): RedirectResponse
    {
        $password = Hash::make($request->input('password'));
        $users = $request->input('users') ?? [];
        $permissions = $request->input('permissions') ?? [];

        try {
            DB::beginTransaction();
            /* @var Admin $admin */
            $admin = $this->repository->create($request->only(['name', 'email']) + compact('password'));
            $this->repository->sync($admin->getKey(), 'users', $users);
            $admin->syncPermissions($permissions);
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
     * @param Admin $admin
     *
     * @return Application|Factory|View
     */
    public function edit(Admin $admin): View|Factory|Application
    {
        $admin->load(['users', 'permissions']);
        $users = app(UserRepository::class)->whereNotNull('api_key')->get();
        $permissions = app(PermissionRepository::class)->whereNotIn('id', [1, 2])->get();
        return view('admin.admins.edit', compact('admin', 'users', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AdminUpdateRequest $request
     * @param Admin $admin
     *
     * @return Application|Factory|RedirectResponse|View
     * @throws Throwable
     */
    public function update(AdminUpdateRequest $request, Admin $admin): View|Factory|RedirectResponse|Application
    {
        try {
            DB::beginTransaction();
            $attributes = $request->only('name');
            $users = $request->input('users') ?? [];
            $permissions = $request->input('permissions') ?? [];

            if (!empty($request->input('password'))) {
                $password = Hash::make($request->input('password'));
                $attributes = $attributes + compact('password');
            }

            $this->repository->lockAndUpdate($attributes, $admin->id);
            $this->repository->sync($admin->id, 'users', $users);
            $admin->syncPermissions($permissions);

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
     * @param Admin $admin
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(Admin $admin): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockAndDelete($admin->id);
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