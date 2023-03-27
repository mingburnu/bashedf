@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <h2 class="font-weight-bold">{{__('ui.admin') . __('ui.list')}}</h2>
        <table class="table table-bordered w-100" id="dataTable">
            <thead>
            <tr>
                <th></th>
                <th>{{__('ui.id')}}</th>
                <th>{{__('validation.attributes.name')}}</th>
                <th>{{__('validation.attributes.email')}}</th>
                <th>{{__('ui.role')}}</th>
                <th>{{__('ui.created_at')}}</th>
                <th><a class="btn btn-primary" href="{{route('admin.admins.create')}}"><i class="fas fa-plus"></i></a></th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script>
        setDatatable($('#dataTable').DataTable(composeDataTableSchema({
            ajax: route('admin.admins.index'),
            columns: [
                {
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return row.roles.length > 0 ? null : '<i class="fa-plus-circle fas" onclick="switchDetail(this)"></i>';
                    }
                },
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'role', name: 'role'},
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
                {
                    name: 'action',
                    render: function (data, type, row, meta) {
                        if (row.roles.length > 0) {
                            return null;
                        } else {
                            let map = new Map(),
                                permissions = '';

                            map.set(Lang.get('ui.merchant'), `<span class="inline-block w-[250px]">${Lang.get('validation.attributes.name')}</span><span class="inline-block w-[250px]">${Lang.get('validation.attributes.email')}</span><hr>`);
                            row.users.forEach(function (element, idx) {
                                map.set(`<span id="user_${idx}"></span>`, `<span class="inline-block w-[250px]">${element.name}</span><span class="inline-block w-[250px]">${element.email}</span><hr>`);
                            });


                            row.permissions.forEach(function (element, idx) {
                                permissions = `${permissions}<span class="mr-2">${Lang.get(`ui.${element.name}`)}</span>`;
                            });

                            map.set(Lang.get('ui.permission'), permissions);

                            row.detail = map;

                            return '<a class="mr-2 btn btn-primary edit text-white" target="_blank" href="' + route('admin.admins.edit', {admin: row.id}) + '"><i class="fas fa-edit"></i></a>' +
                                '<button class="btn btn-danger delete text-white" onclick="destroy(this)"><i class="fas fa-trash"></i></button>';
                        }
                    }
                },
            ],
        })));

        function destroy(element) {
            let row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: Lang.get('message.delete_target', {target: Lang.get('ui.id'), value: row.data().id}),
                text: Lang.get('message.delete_it'),
                type: 'warning',
            })).then(function (result) {
                if (result.value) {
                    axios.delete(route('admin.admins.destroy', {admin: row.data().id}))
                        .then(function (response) {
                            fireSuccessBox(response);
                            getDatatable().ajax.reload(null, false);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            });
        }
    </script>
@endpush