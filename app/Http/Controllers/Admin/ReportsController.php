<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\DateTimeIntervalCriteria;
use App\Criteria\DepositReportCriteria;
use App\Criteria\PaymentReportCriteria;
use App\Entities\User;
use App\Exports\MerchantsExport;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Repositories\DepositRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use App\Services\ClosureService;
use App\Transformers\ReportTransformer;
use App\Validators\UserValidator;
use DataTables;
use Excel;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Exception;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Storage;
use Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    protected UserRepository $repository;

    protected UserValidator $validator;

    /**
     * ReportsController constructor.
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
     * @return Application|Factory|View|JsonResponse|StreamedResponse
     * @throws RepositoryException
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function index(): View|Factory|StreamedResponse|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $email = $this->escapeSpecificChars(request('email'));

            $merchants = $this->admin->merchants;
            $recordsTotal = $merchants->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($email) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', $email);
                request()->request->set('searchFields', 'email:=');
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))
                    ->whereIn('id', $merchants->modelKeys())
                    ->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $this->repository->resetModel();
            $source = $this->repository->whereIn('id', $merchants->modelKeys())->newQuery();

            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)
                ->skipTotalRecords()->setTransformer(app(ReportTransformer::class))->toJson();
        }

        if (request()->acceptsHtml()) {
            return view('admin.reports.index');
        } else {
            $email = $this->escapeSpecificChars(request('email'));
            $this->initRepositoryParams();
            request()->request->set('search', $email);
            request()->request->set('searchFields', 'email:=');
            $merchants = $this->repository->pushCriteria(app(RequestCriteria::class))->findWhereIn('id', $this->admin->merchants->modelKeys());

            $data = $merchants->map(function (User $user) {
                $paymentRepository = app(PaymentRepository::class)
                    ->pushCriteria(new PaymentReportCriteria($user->id, 1))
                    ->pushCriteria(app(DateTimeIntervalCriteria::class));
                $depositRepository = app(DepositRepository::class)
                    ->pushCriteria(new DepositReportCriteria($user->id, 1))
                    ->pushCriteria(app(DateTimeIntervalCriteria::class));

                $paymentAmountSum = $paymentRepository->sum('amount');
                $depositAmountSum = $depositRepository->sum('amount');
                $paymentRepository->resetModel();
                $depositRepository->resetModel();
                $quantity = $paymentRepository->count() + $depositRepository->count();
                return [
                    $user->name,
                    $quantity,
                    $depositAmountSum,
                    $paymentAmountSum,
                ];
            });

            $admin_id = $this->admin->id;
            $filename = time() . '.xlsx';
            $path = "reports/$admin_id/$filename";
            Excel::store(new MerchantsExport($data), $path);
            return Storage::download($path, 'report.xlsx');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return View|Factory|StreamedResponse|JsonResponse|Application
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function show(User $user): View|Factory|StreamedResponse|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $status = is_string(request('status')) ? request('status') : '';

            $paymentRepository = app(PaymentRepository::class);
            $depositRepository = app(DepositRepository::class);

            $recordsTotal = $paymentRepository->whereUserId($user->id)->count() +
                $depositRepository->whereUserId($user->id)->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            $source = collect([$user])->map(app(ClosureService::class)->getByStatusClosure($status))->first();
            $recordsFiltered = $source->count();
            return datatables($source)->with('recordsTotal', $recordsTotal)->with('recordsFiltered', $recordsFiltered)->toJson();
        }

        if (request()->acceptsHtml()) {
            return view('admin.reports.show', compact('user'));
        } else {
            $status = is_string(request('status')) ? request('status') : '';
            $tz = request('tz');
            $adminId = $this->admin->id;
            $filename = time() . '.xlsx';
            $path = "reports/$adminId/$filename";

            /* @var Collection $data */
            $data = collect([$user])->map(app(ClosureService::class)->getByStatusClosure($status))->first();
            $data = $data->map(app(ClosureService::class)->getExportClosure($tz));

            Excel::store(new OrdersExport($data), $path);
            return Storage::download($path, 'report.xlsx');
        }
    }
}