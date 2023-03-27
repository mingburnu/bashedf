@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row mb-4 pr-3">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.login_logs')}}</h2>
                </div>
            </div>
            <table class="table table-bordered w-100" id="datatable">
                <thead>
                <tr>
                    <th>{{__('ui.ip')}}</th>
                    <th>{{__('ui.login_at')}}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@push('js')
    <script>
        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            ajax: route('admin.login-records.index'),
            columns: [
                {data: 'description', name: 'description'},
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