@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <h2 class="font-weight-bold">{{__('ui.merchant').__('ui.list')}}</h2>

        <table class="table table-bordered w-100" id="datatable">
            <thead>
            <tr>
                <th></th>
                <th>{{__('ui.id')}}</th>
                <th>{{__('validation.attributes.merchant_id')}}</th>
                <th>{{__('ui.merchant_name')}}</th>
                <th>{{__('validation.attributes.email')}}</th>
                <th>{{__('validation.attributes.balance')}}</th>
                <th>{{__('validation.attributes.company')}}</th>
                <th>{{__('validation.attributes.phone')}}</th>
                <th>{{__('validation.attributes.api_key')}}</th>
                <th>{{__('ui.created_at')}}</th>
                <th><a class="btn btn-primary" href="{{route('admin.users.create')}}"><i class="fas fa-plus"></i></a></th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script>
        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            ajax: route('admin.users.index'),
            columns: [
                {
                    className: 'text-center',
                    defaultContent: '<i class="fa-plus-circle fas" onclick="switchDetail(this)"></i>'
                },
                {data: 'id', name: 'id'},
                {data: 'merchant_id', name: 'merchant_id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'wallet.balance', name: 'wallet.balance'},
                {data: 'company', name: 'company'},
                {data: 'phone', name: 'phone'},
                {defaultContent: '<button class="btn btn-success" onclick="displayApiKey(this)"><i class="fas fa-eye"></i></button>'},
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
                {
                    name: 'action', height: "100%", render: function (data, type, row, meta) {
                        let map = new Map();

                        map.set(Lang.get('ui.bank_card'), `<span class="inline-block w-[250px]">${Lang.get('validation.attributes.account_name')}</span><span class="inline-block w-[250px]">${Lang.get('validation.attributes.bank_name')}</span><span class="inline-block w-[250px]">${Lang.get('validation.attributes.account_number')}</span><hr>`);
                        row.bank_cards.forEach(function (element, idx) {
                            map.set(`<span id="card_${idx}"></span>`, `<span class="inline-block w-[250px]">${element.account_name}</span><span class="inline-block w-[250px]">${element.bank_name}</span><span class="inline-block w-[250px]">${element.account_number}</span><hr>`);
                        });

                        row.detail = map;

                        return '<a href="' + route('admin.users.edit', {user: row.id}) + '" target="_blank" class="mr-2 btn btn-primary text-white" title="' + Lang.get('ui.edit') + '"><i class="fas fa-edit"></i></a>' +
                            '<button class="mr-2 btn btn-secondary text-white" onclick="clearGoogle2faSecret(this)" title="' + Lang.get('ui.delete') + Lang.get('ui.google_authenticator') + '"><i class="fas fa-edit"></i></button>' +
                            '<button class="btn btn-danger text-white" onclick="destroy(this)" title="' + Lang.get('ui.delete') + '"><i class="fas fa-trash"></i></button>';
                    }
                }
            ]
        })));

        function destroy(element) {
            let row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: '<span class="mr-2">' + Lang.get('ui.merchant') + '</span>' + row.data().name,
                text: Lang.get('message.delete_it'),
                type: 'warning',
            })).then(function (result) {
                if (result.value) {
                    axios.delete(route('admin.users.destroy', {user: row.data().id}))
                        .then(function (response) {
                            getDatatable().ajax.reload(null, false);
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            });
        }

        function clearGoogle2faSecret(element) {
            let row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: '<span class="mr-2">' + Lang.get('ui.merchant') + '</span>' + row.data().name + '<span class="ml-2">' + Lang.get('ui.google_authenticator') + '</span>',
                text: Lang.get('message.reset_it'),
                type: 'warning',
            })).then(function (result) {
                if (result.value) {
                    axios.delete(route('admin.users.google2fa-secret.clear', {user: row.data().id}))
                        .then(function (response) {
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            });
        }

        function displayApiKey(element) {
            let row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: Lang.get('message.please_enter', {value: Lang.get('validation.attributes.password')}),
                input: 'password',
                preConfirm: function (password) {
                    axios.post(route('admin.users.api-key.display', {user: row.data().id}), {password: password})
                        .then(function (response) {
                            fireSuccessBox(response);
                            setTimeout(function () {
                                Swal.close();
                            }, 10000);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            }));
        }
    </script>
@endpush
