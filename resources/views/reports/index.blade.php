@extends('layouts.app')
@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div>
            <h2 class="font-weight-bold">{{ __('ui.report')}}</h2>
        </div>
        <div class="filter card">
            <div class="card-body">
                <div class="row ">
                    @include('layouts.components.datetimepicker')
                </div>
                <br>
                <div class="row">
                    <div class="col-md-auto">
                        <label for="status">{{__('ui.order') . __('validation.attributes.status')}}</label>
                    </div>
                    <div class="col-md-auto">
                        <select class="form-control" id="status">
                            <option value="" selected>{{__('ui.all')}}</option>
                            <option value="1">{{__('ui.orders.result.1')}}</option>
                            <option value="0">{{__('ui.orders.result.0')}}</option>
                            <option value="-1">{{__('ui.orders.result.-1')}}</option>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button onclick="download()" class="btn btn-info"><i class="fas fa-download"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="row">
                    <div class="col-md-4">
                        <span class="mr-1">{{__('ui.deposit') . __('validation.attributes.processing_fee')}}</span>
                        <i class="fas fa-dollar-sign"></i>
                        <span>@{{ deposit_processing_fee_sum }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="mr-1">{{__('validation.attributes.payment_processing_fee')}}</span>
                        <i class="fas fa-dollar-sign"></i>
                        <span>@{{ payment_processing_fee_sum }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="mr-1">{{__('ui.deposit') . __('validation.attributes.total_amount')}}</span>
                        <i class="fas fa-dollar-sign"></i>
                        <span>@{{ deposit_amount_sum }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="mr-1">{{__('ui.payment') . __('validation.attributes.total_amount')}}</span>
                        <i class="fas fa-dollar-sign"></i>
                        <span>@{{ payment_amount_sum }}</span>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <table class="table table-bordered table-hover w-100" id="datatable">
            <thead>
            <tr>
                <th>{{__('ui.applied_at')}}</th>
                <th>{{__('validation.attributes.order_id')}}</th>
                <th>{{__('ui.deposit')}}<i class="fas fa-slash fa-rotate-90"></i>{{__('ui.payment')}}</th>
                <th>{{__('validation.attributes.amount')}}</th>
                <th>{{__('validation.attributes.processing_fee')}}</th>
                <th>{{__('ui.order') . __('validation.attributes.status')}}</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection
@push('js')
    <script>
        let statistic = Vue.createApp({
            data() {
                return {
                    payment_amount_sum: 0,
                    payment_processing_fee_sum: 0,
                    deposit_amount_sum: 0,
                    deposit_processing_fee_sum: 0
                }
            },
            methods: {
                fetchTotal: function () {
                    let data = $.extend({status: $('#status').val()}, getDtRange());
                    axios.get(route('statistics.index'), {params: data})
                        .then(function (response) {
                            statistic.payment_amount_sum = response.data.payment_amount_sum;
                            statistic.payment_processing_fee_sum = response.data.payment_processing_fee_sum;
                            statistic.deposit_amount_sum = response.data.deposit_amount_sum;
                            statistic.deposit_processing_fee_sum = response.data.deposit_processing_fee_sum;
                        });

                }
            }
        }).mount('div.card-footer');

        function download() {
            axios.get(route('reports.index'), {
                params: $.extend({
                    status: document.getElementById('status').value,
                    tz: Intl.DateTimeFormat().resolvedOptions().timeZone
                }, getDtRange()),
                headers: {'Accept': 'text/csv'},
                responseType: 'blob'
            }).then(function (response) {
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(new Blob([response.data]));
                link.setAttribute('download', response.headers['content-disposition'].split('filename=')[1]);
                document.body.appendChild(link);
                link.click();
                link.remove();
            });
        }

        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            searching: false,
            ajax: {
                url: route('reports.index'),
                data: function (d) {
                    d.status = $('#status').val();
                    return $.extend(d, getDtRange());
                },
                dataSrc: function (json) {
                    Swal.close();
                    statistic.fetchTotal();
                    return json.data;
                }
            },
            initComplete:
                function (settings, json) {
                    bindDtRange();
                    $('body').on('change input', 'select#status', function () {
                        getDatatable().ajax.reload();
                    });
                },
            columns: [
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
                {data: 'order_id', name: 'order_id'},
                {data: 'order_name', name: 'order_name'},
                {data: 'amount', name: 'amount'},
                {data: 'processing_fee', name: 'processing_fee'},
                {data: 'result', name: 'status'},
            ],
        })));
    </script>
@endpush