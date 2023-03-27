@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div>
            <h2 class="font-weight-bold">{{__('ui.report')}}</h2>
        </div>
        <div class="filter card">
            <div class="card-body">
                <div class="row ">
                    @include('layouts.components.datetimepicker')
                </div>
                <br>
                <div class="row">
                    <div class="col-md-auto">
                        <label for="email">{{__('ui.merchant_account')}}</label>
                    </div>
                    <div class="col-md-auto">
                        <input type="text" class="form-control" name="email" id="email">
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
                        <span>@{{ all_deposit_processing_fee_sum }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="mr-1">{{__('validation.attributes.payment_processing_fee')}}</span>
                        <i class="fas fa-dollar-sign"></i>
                        <span>@{{ all_payment_processing_fee_sum }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="mr-1">{{__('ui.total') . __('validation.attributes.processing_fee')}}</span>
                        <i class="fas fa-dollar-sign"></i>
                        <span>@{{ all_processing_fee_sum }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="mr-1">{{__('ui.deposit') . __('validation.attributes.total_amount')}}</span>
                        <i class="fas fa-dollar-sign"></i>
                        <span>@{{ all_deposit_amount_sum }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="mr-1">{{__('ui.payment') . __('validation.attributes.total_amount')}}</span>
                        <i class="fas fa-dollar-sign"></i>
                        <span>@{{ all_payment_amount_sum }}</span>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <table class="table table-bordered table-hover w-100" id="datatable">
            <thead>
            <tr>
                <th>{{__('ui.merchant_name')}}</th>
                <th>{{__('ui.successful_count')}}</th>
                <th>{{__('ui.deposit') . __('validation.attributes.total_amount')}}</th>
                <th>{{__('ui.payment') . __('validation.attributes.total_amount')}}</th>
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
                    all_payment_amount_sum: 0,
                    all_deposit_amount_sum: 0,
                    all_payment_processing_fee_sum: 0,
                    all_deposit_processing_fee_sum: 0,
                    all_processing_fee_sum: 0
                }
            },
            methods: {
                fetchTotal: function () {
                    let data = $.extend({email: document.getElementById('email').value}, getDtRange());
                    axios.get(route('admin.statistics.index'), {params: data})
                        .then(function (response) {
                            statistic.all_payment_amount_sum = response.data.all_payment_amount_sum;
                            statistic.all_deposit_amount_sum = response.data.all_deposit_amount_sum;
                            statistic.all_payment_processing_fee_sum = response.data.all_payment_processing_fee_sum;
                            statistic.all_deposit_processing_fee_sum = response.data.all_deposit_processing_fee_sum;
                            statistic.all_processing_fee_sum = response.data.all_processing_fee_sum;
                        });

                }
            }
        }).mount('div.card-footer');

        function download() {
            axios.get(route('admin.reports.index'), {
                params: $.extend({email: document.getElementById('email').value}, getDtRange()),
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
                url: route('admin.reports.index'),
                data: function (d) {
                    d.email = $('#email').val();
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
                    $('body').on('change input', 'input#email', function () {
                        getDatatable().ajax.reload();
                    });
                },
            columns: [
                {data: 'name', name: 'name', className: 'cursor-pointer'},
                {data: 'quantity', name: 'quantity', className: 'cursor-pointer'},
                {data: 'depositAmountSum', name: 'depositAmountSum', className: 'cursor-pointer'},
                {data: 'paymentAmountSum', name: 'paymentAmountSum', className: 'cursor-pointer'},
            ]
        })));

        $('#datatable tbody').unbind('click').on('click', 'tr', function () {
            window.location.href = route('admin.reports.show', {user: getDatatable().row(this).data().id});
        });
    </script>

@endpush