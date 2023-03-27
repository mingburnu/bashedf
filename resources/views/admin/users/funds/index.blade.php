@extends('layouts.app')
@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <p>
            <a href="{{route('admin.users.transactions.index', ['user' => $user->id])}}">{{__('ui.back') . __('ui.list')}}</a>
        </p>
        <div>
            <h2 class="font-weight-bold">{{__('ui.seizing') . __('ui.record')}}</h2>
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
                <th>{{__('ui.id')}}</th>
                <th>{{__('validation.attributes.amount')}}</th>
                <th>{{__('validation.attributes.cause')}}</th>
                <th>{{__('ui.admin')}}</th>
                <th>{{__('ui.seized_at')}}</th>
                <th>{{__('ui.unfrozen_at')}}</th>
                <th>
                    <button onclick="seize()" class="btn btn-warning seize">
                        <i class="fas fa-dollar-sign"></i><sub><i class="fas fa-lock"></i></sub>
                    </button>
                </th>
            </tr>
            </thead>
        </table>
    </div>
    <input type="hidden" id="user_id" value="{{$user->id}}">
    <input type="hidden" id="user_name" value="{{$user->name}}">
@endsection
@push('js')
    <script>
        Echo.private('fund').listen('ThawFundEvent', (e) => {
            let row = getEventRow(e);
            if (row.data() !== undefined) {
                row.data().updated_at = moment().utc(e.updated_at).format('YYYY-MM-DD HH:mm:ss');
                row.data().unfrozen = 1;
                row.data(row.data());
            }
        });

        let user_id = $('#user_id').val(),
            user_name = $('#user_name').val();

        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            searching: false,
            ajax: {
                url: route('admin.users.funds.index', {user: user_id}),
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
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'amount', name: 'amount'},
                {data: 'cause', name: 'cause'},
                {
                    data: 'admin.name', name: 'admin_name', render: function (data, type, row, meta) {
                        return  data === undefined ? null : data;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                    render: function (data, type, row, meta) {
                        if (row.unfrozen === 1) {
                            return moment(data).format('YYYY-MM-DD HH:mm:ss');
                        } else {
                            return null;
                        }
                    }
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.unfrozen === 1) {
                            return null;
                        } else {
                            return '<button class="btn btn-danger" onclick="thaw(this)"><i class="fas fa-dollar-sign"></i><sub><i class="fas fa-unlock-alt"></i></sub></button>';
                        }
                    }
                },
            ]
        })));

        function thaw(element) {
            let form,
                row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: Lang.get('message.thaw_merchant_point', {value: user_name}),
                html:
                    '<div class="text-left">' + Lang.get('validation.attributes.merchant_id') + '</div>' +
                    '<input v-model="merchant_id" class="swal2-input" type="text">' +
                    '<div class="text-left">' + Lang.get('validation.attributes.amount') + '</div>' +
                    '<div class="text-left"><input v-model="amount" class="swal2-input" type="number" step="0.01"></div>' +
                    '<div class="text-left">' + Lang.get('validation.attributes.amount_confirmation') + '</div>' +
                    '<div class="text-left"><input v-model="amount_confirmation" class="swal2-input" type="number" step="0.01"></div>',
                type: 'warning',
                onOpen: function () {
                    form = Vue.createApp({
                        data() {
                            return {
                                merchant_id: '',
                                amount: '',
                                amount_confirmation: ''
                            }
                        }
                    }).mount('.swal2-content');
                },
                preConfirm: function (content) {
                    $(element).hide();
                    axios.patch(route('admin.funds.update', {fund: row.data().id}), form.$data)
                        .then(function (response) {
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            $(element).show();
                            fireErrorBox(error);
                        });
                }
            }));
        }

        function seize() {
            let form;

            Swal.fire(composePopupBoxSchema({
                title: Lang.get('message.seize_merchant_point', {value: user_name}),
                html:
                    '<div class="text-left">' + Lang.get('validation.attributes.merchant_id') + '</div>' +
                    '<input v-model="merchant_id" class="swal2-input" type="text">' +
                    '<div class="text-left">' + Lang.get('validation.attributes.amount') + '</div>' +
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
                                cause: '',
                            }
                        }
                    }).mount('.swal2-content');
                },
                preConfirm: function (content) {
                    axios.post(route('admin.users.funds.store', {user: user_id}), form.$data)
                        .then(function (response) {
                            getDatatable().ajax.reload(function (response) {
                                fireSuccessBox(response);
                            }, false);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            }));
        }
    </script>
@endpush
