@extends('layouts.app')
@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <p>
            <a href="{{route('admin.wallets.index')}}">{{__('ui.back') . __('ui.list')}}</a>
        </p>
        <div>
            <h2 class="font-weight-bold">{{$user->name . __('ui.transaction') . __('ui.record')}}</h2>
        </div>
        <div class="filter card">
            <div class="card-body">
                <div class="row ">
                    @include('layouts.components.datetimepicker')
                </div>
            </div>
        </div>

        <br>
        <table class="table table-bordered table-hover w-100" id="datatable">
            <thead>
            <tr>
                <th>{{__('ui.type')}}</th>
                <th>{{__('ui.old_balance')}}</th>
                <th>{{__('ui.transaction_amount')}}</th>
                <th>{{__('ui.new_balance')}}</th>
                <th>{{__('validation.attributes.order_id')}}</th>
                <th>{{__('validation.attributes.cause')}}</th>
                <th>{{__('ui.transacted_at')}}</th>
            </tr>
            </thead>
        </table>
    </div>
    <input type="hidden" id="user_id" value="{{$user->id}}">
@endsection
@push('js')
    <script>
        let user_id = $('#user_id').val();

        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            searching: false,
            ajax: {
                url: route('admin.users.transactions.index', {user: user_id}),
                data: function (d) {
                    return $.extend(d, getDtRange());
                },
                dataSrc: function (json) {
                    Swal.close();
                    return json.data;
                }
            },
            initComplete:
                function (settings, json) {
                    bindDtRange();
                    $('#datatable_length').append('<div id="datatable_funds" class="ml-2 inline-block"><a class="btn btn-info w-11 h-[37px] overflow-hidden"><i class="fas fa-list"></i><sub class="bottom-4 left-2.5"><i class="fa-xs fas fa-lock"></i></sub></a></div>');
                    $('div#datatable_funds > a').attr('href', route('admin.users.funds.index', {user: user_id}));
                },
            columns: [
                {data: 'tag', name: 'tag'},
                {data: 'old_balance', name: 'old_balance'},
                {data: 'transaction_amount', name: 'transaction_amount'},
                {data: 'new_balance', name: 'new_balance'},
                {
                    data: 'orderable.order_id', name: 'orderable_order_id', render: function (data, type, row, meta) {
                        if (data === undefined) {
                            return null;
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'orderable.cause', name: 'orderable_cause', render: function (data, type, row, meta) {
                        if (data === undefined) {
                            return null;
                        } else if (row.type === 'seizing' || row.type === 'operation') {
                            return data;
                        } else {
                            return null;
                        }
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                }
            ]
        })));
    </script>
@endpush