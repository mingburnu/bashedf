@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row mb-4 pr-3">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.clerk') . __('ui.list')}}</h2>
                </div>
            </div>
            <table class="table table-bordered w-100" id="datatable">
                <thead>
                <tr>
                    <th>{{__('ui.id')}}</th>
                    <th>{{__('validation.attributes.name')}}</th>
                    <th>{{__('validation.attributes.email')}}</th>
                    <th>{{__('ui.created_at')}}</th>
                    <th>
                        <a class="btn btn-primary" href="{{route('children.create')}}">
                            <i class="fas fa-plus"></i>
                        </a>
                    </th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script>
        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            ajax: route('children.index'),
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row, meta) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
                {
                    name: 'action', render: function (data, type, row, meta) {
                        return '<a href="' + route('children.edit', {child: row.id}) + '" target="_blank" class="mr-2 btn btn-primary text-white" title="' + Lang.get('ui.edit') + '"><i class="fas fa-edit"></i></a>' +
                            '<button class="btn btn-danger text-white" onclick="destroy(this)"><i class="fas fa-trash" title="' + Lang.get('ui.delete') + '" ></i></button>';
                    }
                }
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
                    axios.delete(route('children.destroy', {child: row.data().id}))
                        .then(function (response) {
                            getDatatable().ajax.reload();
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            });
        }
    </script>
@endpush