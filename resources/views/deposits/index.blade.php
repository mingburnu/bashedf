@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row mb-4 pr-3">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.deposit') . __('ui.list')}}</h2>
                </div>
            </div>
            <table class="table table-bordered w-100" id="datatable">
                <thead>
                <tr>
                    <th></th>
                    <th>{{__('ui.order_id')}}</th>
                    <th>{{__('validation.attributes.amount')}}</th>
                    <th>{{__('validation.attributes.processing_fee')}}</th>
                    <th>{{__('validation.attributes.total_amount')}}</th>
                    <th>{{__('validation.attributes.status')}}</th>
                    <th>{{__('ui.created_at')}}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script>
        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            ajax: {
                url: route('deposits.index'),
                data: function (d) {
                    return $.extend(d, {});
                },
            },
            initComplete:
                function (settings, json) {
                },
            columns: [
                {
                    className: 'text-center',
                    defaultContent: '<i class="fa-plus-circle fas" onclick="switchDetail(this)"></i>'
                },
                {data: 'order_id', name: 'order_id'},
                {data: 'amount', name: 'amount'},
                {data: 'processing_fee', name: 'processing_fee'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'result', name: 'result'},
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        let map = new Map();
                        map.set(Lang.get('validation.attributes.account_name'), row.account_name);
                        map.set(Lang.get('validation.attributes.bank_name'), row.bank_name);
                        map.set(Lang.get('validation.attributes.account_number'), row.account_number);
                        map.set(Lang.get('validation.attributes.bank_district'), row.bank_district);
                        map.set(Lang.get('validation.attributes.bank_address'), row.bank_address);
                        map.set(Lang.get('ui.checked_at'), row.checked_at ? moment(row.checked_at).format('YYYY-MM-DD HH:mm:ss') : '');
                        row.detail = map;

                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
            ]
        })));
    </script>

@endpush
