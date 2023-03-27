@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <h2 class="font-weight-bold">{{__('ui.bank_card') . __('ui.list')}}</h2>
        <table class="table table-bordered w-100" id="dataTable">
            <thead>
            <tr>
                <th>{{__('validation.attributes.account_name')}}</th>
                <th>{{__('validation.attributes.bank_name')}}</th>
                <th>{{__('validation.attributes.account_number')}}</th>
                <th>{{__('validation.attributes.bank_district')}}</th>
                <th>{{__('validation.attributes.bank_address')}}</th>
                <th>
                    <a class="btn btn-primary" href="{{route('admin.bank-cards.create')}}">
                        <i class="fas fa-plus"></i>
                    </a>
                </th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script>
        setDatatable($('#dataTable').DataTable(composeDataTableSchema({
            ajax: route('admin.bank-cards.index'),
            columns: [
                {data: 'account_name', name: 'account_name'},
                {data: 'bank_name', name: 'bank_name'},
                {data: 'account_number', name: 'account_number'},
                {data: 'bank_district', name: 'bank_district'},
                {data: 'bank_address', name: 'bank_address'},
                {
                    name: 'action', render: function (data, type, row, meta) {
                        return '<a href="' + route('admin.bank-cards.edit', {bank_card: row.id}) + '" target="_blank" class="mr-2 btn btn-primary text-white" title="' + Lang.get('ui.edit') + '"><i class="fas fa-edit"></i></a>' +
                            '<button class="btn btn-danger text-white" onclick="destroy(this)"><i class="fas fa-trash" title="' + Lang.get('ui.delete') + '" ></i></button>';
                    }
                }
            ],
        })));

        function destroy(element) {
            let row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: Lang.get('message.delete_target', {
                    target: Lang.get('validation.attributes.account_number'),
                    value: row.data().account_number
                }),
                text: Lang.get('message.delete_it'),
                type: 'warning',
                width: 'auto',
            })).then(function (result) {
                if (result.value) {
                    axios.delete(route('admin.bank-cards.destroy', {bank_card: row.data().id}))
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