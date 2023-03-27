@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div class="container-fluid">
            <h2 class="font-weight-bold">{{__('ui.deposit') . __('ui.list')}}</h2>
        </div>
        <table class="table table-bordered w-100" id="datatable">
            <thead>
            <tr>
                <th></th>
                <th>{{__('validation.attributes.order_id')}}</th>
                <th>{{__('ui.merchant')}}</th>
                <th>{{__('validation.attributes.balance')}}</th>
                <th>{{__('validation.attributes.amount')}}</th>
                <th>{{__('validation.attributes.processing_fee')}}</th>
                <th>{{__('validation.attributes.total_amount')}}</th>
                <th>{{__('ui.applied_at')}}</th>
                <th></th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script>
        Echo.private('deposit').listen('CheckedDepositEvent', (e) => {
            let row = getEventRow(e);
            if (row.data() !== undefined) {
                row.data().status = e.status;
                row.data().process = e.process;
                row.data().admin = e.admin;
                row.data().transactions = e.transactions;
                row.data().checked_at = moment(e.checked_at).format('YYYY-MM-DD HH:mm:ss');
                row.data(row.data());
                refreshDetail(row);
            }
        });

        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            ajax: {
                url: route('admin.deposits.index'),
                data: function (d) {
                    return $.extend(d, {});
                },
                dataSrc: function (json) {
                    Swal.close();
                    return json.data;
                }
            },
            initComplete:
                function (settings, json) {
                    $('#datatable_length').append('<div id="datatable_refresh" class="ml-2 inline-block"><button class="btn btn-info" onclick="refresh()"><i class="fas fa-sync"></i></button></div>');
                },
            columns: [
                {
                    className: 'text-center',
                    defaultContent: '<i class="fas fa-plus-circle" onclick="switchDetail(this)"></i>'
                },
                {data: 'order_id', name: 'order_id'},
                {data: 'user.name', name: 'name'},
                {
                    data: 'transactions', name: 'balance', render: function (data, type, row, meta) {
                        if (data.length === 0) {
                            return '';
                        } else {
                            return data[0].new_balance;
                        }
                    }
                },
                {data: 'amount', name: 'amount'},
                {data: 'processing_fee', name: 'processing_fee'},
                {data: 'total_amount', name: 'total_amount'},
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
                {
                    data: 'status',
                    name: 'action',
                    render: function (data, type, row, meta) {
                        if ([1, -1].indexOf(data) === -1) {
                            row.checked_at = '';
                        } else {
                            row.checked_at = moment(row.updated_at).format('YYYY-MM-DD HH:mm:ss');
                        }

                        let map = new Map();
                        map.set(Lang.get('ui.checker'), (row.admin === null ? '' : row.admin.name));
                        map.set(Lang.get('validation.attributes.account_name'), row.account_name);
                        map.set(Lang.get('validation.attributes.bank_name'), row.bank_name);
                        map.set(Lang.get('validation.attributes.account_number'), row.account_number);
                        map.set(Lang.get('validation.attributes.bank_district'), row.bank_district);
                        map.set(Lang.get('validation.attributes.bank_address'), row.bank_address);
                        map.set(Lang.get('ui.checked_at'), row.checked_at);
                        row.detail = map;

                        if (data === 0) {
                            return '<select class="form-control" onchange="judge(this)">' +
                                '<option value="" selected>' + Lang.get('ui.select') + '</option>' +
                                '<option value="1">' + Lang.get('ui.orders.process.1') + '</option>' +
                                '<option value="-1">' + Lang.get('ui.orders.process.-1') + '</option>' +
                                '</select>';
                        } else {
                            return row.process;
                        }
                    }
                },
            ]
        })));

        function judge(element) {
            let status = element.value,
                row = getDatatable().row($(element).closest('tr'));
            if (status === '' || !status) return;
            Swal.fire(composePopupBoxSchema({
                title: '<span class="mr-2">' + Lang.get('validation.attributes.order_id') + '</span>' + row.data().order_id,
                text: Lang.get('message.change_it_status', {
                    target: Lang.get('ui.order'),
                    value: element.selectedOptions[0].textContent
                }),
                type: 'warning',
                width: '45rem'
            })).then(function (result) {
                if (result.value) {
                    $(element).hide();
                    axios.patch(route('admin.deposits.update', {deposit: row.data().id}), {status: status})
                        .then(function (response) {
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            $(element).val('');
                            $(element).show();
                            fireErrorBox(error);
                        });
                } else {
                    element.value = '';
                }
            });
        }
    </script>
@endpush