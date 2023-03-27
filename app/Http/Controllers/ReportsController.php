<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Repositories\DepositRepository;
use App\Repositories\PaymentRepository;
use App\Services\ClosureService;
use Excel;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Exception;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    /**
     * @return Application|Factory|View|JsonResponse|StreamedResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function index(): View|Factory|StreamedResponse|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $status = is_string(request('status')) ? request('status') : '';

            $paymentRepository = app(PaymentRepository::class);
            $depositRepository = app(DepositRepository::class);

            $recordsTotal = $paymentRepository->whereUserId($this->merchant->id)->count() +
                $depositRepository->whereUserId($this->merchant->id)->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            /* @var Collection $source */
            $source = collect([$this->merchant])->map(app(ClosureService::class)->getByStatusClosure($status))->first();
            $recordsFiltered = $source->count();
            return datatables($source)->with('recordsTotal', $recordsTotal)->with('recordsFiltered', $recordsFiltered)->toJson();
        }

        if (request()->acceptsHtml()) {
            return view('reports.index');
        } else {
            $status = is_string(request('status')) ? request('status') : '';
            $tz = request('tz');
            $userId = $this->clerk->id;
            $filename = time() . '.xlsx';
            $path = "reports/$userId/$filename";

            /* @var Collection $data */
            $data = collect([$this->merchant])->map(app(ClosureService::class)->getByStatusClosure($status))->first();
            $data = $data->map(app(ClosureService::class)->getExportClosure($tz));

            Excel::store(new OrdersExport($data), $path);
            return Storage::download($path, 'report.xlsx');
        }
    }
}