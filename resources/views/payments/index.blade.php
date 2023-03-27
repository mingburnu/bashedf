@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row mb-4 pr-3">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.payment') . __('ui.list')}}</h2>
                </div>
            </div>
            <table class="table table-bordered w-100" id="datatable">
                <thead>
                <tr>
                    <th></th>
                    <th>{{__('ui.order_id')}}</th>
                    <th>{{__('validation.attributes.customized_id')}}</th>
                    <th>{{__('validation.attributes.account_name')}}</th>
                    <th>{{__('validation.attributes.amount')}}</th>
                    <th>{{__('validation.attributes.balance')}}</th>
                    <th>{{__('validation.attributes.status')}}</th>
                    <th>{{__('ui.applied_at')}}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <input type="hidden" id="balance" value="{{$balance}}">
    @if (session('result')===true)
        <input type="hidden" id="result" value="{{session('result')}}">
    @endif
@endsection

@push('js')
    <script>
        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            dom: 'lfBrtip',
            buttons: [
                'csv',
                'print',
            ],
            ajax: {
                url: route('payments.index'),
                data: function (d) {
                    return $.extend(d, {});
                },
            },
            initComplete:
                function (settings, json) {
                    $('#datatable_length').append('<span class="mx-2 text-red-500 text-{16px}">' + Lang.get('validation.attributes.balance') + $('#balance').val() + '</span>');
                    $('.dt-button').addClass('btn').addClass('btn-info');
                },
            columns: [
                {
                    className: 'text-center',
                    defaultContent: '<i class="fa-plus-circle fas" onclick="switchDetail(this, loadIconByDTable)"></i>'
                },
                {data: 'order_id', name: 'order_id'},
                {data: 'customized_id', name: 'customized_id'},
                {data: 'account_name', name: 'account_name'},
                {data: 'amount', name: 'amount'},
                {
                    name: 'balance', render: function (data, type, row, meta) {
                        return row.transactions[row.transactions.length - 1].new_balance;
                    }
                },
                {
                    data: 'status', name: 'result', render: function (data, type, row, meta) {
                        switch (data) {
                            case 1:
                                if (row.transactions.length > 1) {
                                    return Lang.get('ui.orders.result.-1');
                                } else {
                                    return row.result;
                                }
                            case -1:
                                if (row.transactions.length > 2) {
                                    return Lang.get('ui.orders.result.1');
                                } else {
                                    return row.result;
                                }
                            case 0:
                            default:
                                return row.result;
                        }
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        let map = new Map();
                        map.set(Lang.get('ui.order_id'), row.order_id);
                        map.set(Lang.get('validation.attributes.status'), row.result);
                        map.set(Lang.get('ui.payee'), row.account_name);
                        map.set(Lang.get('validation.attributes.customized_id'), row.customized_id);
                        map.set(Lang.get('validation.attributes.bank_name'), row.bank_name);
                        map.set(Lang.get('ui.bank_icon'), `<span id="${row.id}_icon"></span>`);
                        map.set(Lang.get('validation.attributes.account_number'), row.account_number);
                        map.set(Lang.get('validation.attributes.branch'), row.branch);
                        map.set(Lang.get('validation.attributes.amount'), row.amount);
                        map.set(Lang.get('validation.attributes.processing_fee'), row.processing_fee);
                        map.set(Lang.get('validation.attributes.total_amount'), row.total_amount);
                        map.set(Lang.get('validation.attributes.balance'), collect(row.transactions).pluck('new_balance').all().toString().replace(",", "<br>"));
                        map.set(Lang.get('ui.clerk_name'), (row.send_log != null ? row.send_log.causer.name : ''));
                        map.set(Lang.get('ui.checked_at'), row.checked_at ? moment(row.checked_at).format('YYYY-MM-DD HH:mm:ss') : '');
                        row.detail = map;

                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                }
            ]
        })));

        if (document.getElementById('result')) {
            fireSuccessBox({data: {messages: []}});
        }
    </script>

@endpush
