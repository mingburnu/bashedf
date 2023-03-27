<?php

namespace App\Http\Controllers\Admin;

use App\Entities\News;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewsCreateRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Repositories\NewsRepository;
use App\Validators\NewsValidator;
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

class NewsController extends Controller
{
    protected NewsRepository $repository;

    protected NewsValidator $validator;

    /**
     * NewsController constructor.
     *
     * @param NewsRepository $repository
     * @param NewsValidator $validator
     */
    public function __construct(NewsRepository $repository, NewsValidator $validator)
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
                request()->request->set('search', "title:$search_value");
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('admin.news.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewsCreateRequest $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(NewsCreateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $status = 0;
            $this->repository->create($request->except(['status']) + compact('status'));
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
     * @param NewsUpdateRequest $request
     * @param News $news
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(NewsUpdateRequest $request, News $news): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockAndUpdate($request->except(['status']), $news->id);
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
     * Remove the specified resource from storage.
     *
     * @param News $news
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(News $news): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockAndDelete($news->id);
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
     * @param NewsUpdateRequest $request
     * @param News $news
     * @return JsonResponse
     * @throws Throwable
     */
    public function change(NewsUpdateRequest $request, News $news): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->lockAndUpdate($request->only(['status']), $news->id);
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