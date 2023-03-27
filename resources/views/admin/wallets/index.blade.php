@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <h2 class="font-weight-bold">{{__('ui.wallet').__('ui.list')}}</h2>

        <table class="table table-bordered w-100" id="datatable">
            <thead>
            <tr>
                <th>{{__('ui.id')}}</th>
                <th>{{__('validation.attributes.merchant_id')}}</th>
                <th>{{__('ui.merchant_name')}}</th>
                <th>{{__('validation.attributes.email')}}</th>
                <th>{{__('validation.attributes.balance')}}</th>
                <th></th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script>
        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            ajax: route('admin.wallets.index'),
            columns: [
                {data: 'id', name: 'id'},
                {data: 'merchant_id', name: 'merchant_id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'wallet.balance', name: 'balance'},
                {
                    name: 'action', height: "100%", render: function (data, type, row, meta) {
                        let walletOperationBtn = '<button class="mr-2 btn btn-warning" onclick="operate(this)"><i class="fas fa-money-check-alt"></i></button>',
                            transactionsLink = `<a href="${route('admin.users.transactions.index', {user: row.id})}" class="mr-2 btn btn-info text-white" title="${Lang.get('ui.transaction')}${Lang.get('ui.record')}"><i class="fas fa-list"></i></a>`;

                        return walletOperationBtn + transactionsLink;
                    }
                }
            ]
        })));

        function operate(element) {
            let form,
                row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: Lang.get('message.plus_minus_merchant_point', {value: row.data().name}),
                html:
                    '<div class="text-left">' + Lang.get('validation.attributes.merchant_id') + '</div>' +
                    '<input v-model="merchant_id" class="swal2-input" type="text">' +
                    '<div class="text-left">' + Lang.get('message.please_enter', {value: Lang.get('ui.positive_negative') + Lang.get('validation.attributes.amount')}) + '</div>' +
                    '<div class="text-left"><input v-model="amount" class="swal2-input" type="number" step="0.01"></div>' +
                    '<div class="text-left">' + Lang.get('validation.attributes.amount_confirmation') + '</div>' +
                    '<div class="text-left"><input v-model="amount_confirmation" class="swal2-input" type="number" step="0.01"></div>' +
                    '<div class="text-left">' + Lang.get('validation.attributes.cause') + '</div>' +
                    '<div class="text-left"><input v-model="cause" class="swal2-input" type="text"></div>',
                type: 'warning',
                width: '35rem',
                onOpen: function () {
                    form = Vue.createApp({
                        data() {
                            return {
                                merchant_id: '',
                                amount: '',
                                amount_confirmation: '',
                                cause: ''
                            }
                        }
                    }).mount('.swal2-content');
                },
                preConfirm: function () {
                    axios.post(route('admin.users.account-operations.store', {user: row.data().id}), form.$data)
                        .then(function (response) {
                            fireSuccessBox(response);
                            getDatatable().ajax.reload(null, false);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            }));
        }
    </script>
@endpush
