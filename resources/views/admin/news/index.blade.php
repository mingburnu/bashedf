@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <h2 class="font-weight-bold">{{__('ui.new') . __('ui.list')}}</h2>
        <table class="table table-bordered w-100" id="dataTable">
            <thead>
            <tr>
                <th>{{__('ui.id')}}</th>
                <th>{{__('validation.attributes.title')}}</th>
                <th>{{__('validation.attributes.content')}}</th>
                <th></th>
                <th>
                    <button class="btn btn-primary" onclick="create()"><i class="fas fa-plus"></i></button>
                </th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script>
        setDatatable($('#dataTable').DataTable(composeDataTableSchema({
            ajax: route('admin.news.index'),
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'content', name: 'content'},
                {
                    data: 'status', name: 'status', render: function (data, type, row, meta) {
                        return '<select class="newsSetting form-control" onchange="changeStatus(this)">' +
                            '<option value="1" ' + (data > 0 ? 'selected' : '') + '>' + Lang.get('ui.news.status.1') + '</option>' +
                            '<option value="0" ' + (data < 1 ? 'selected' : '') + '>' + Lang.get('ui.news.status.0') + '</option>' +
                            '</select>';
                    }
                },
                {
                    name: 'action', render: function (data, type, row, meta) {
                        return '<button class="mr-2 btn btn-primary text-white" onclick="update(this)"><i class="fas fa-edit" title="' + Lang.get('ui.edit') + '" ></i></button>' +
                            '<button class="btn btn-danger text-white" onclick="destroy(this)"><i class="fas fa-trash" title="' + Lang.get('ui.delete') + '" ></i></button>';
                    }
                }
            ]
        })));

        function create() {
            let form;
            Swal.fire(composePopupBoxSchema({
                title: Lang.get('ui.create') + Lang.get('ui.new'),
                html:
                    '<div class="text-left">' + Lang.get('validation.attributes.title') + '</div>' +
                    '<input type="text" class="swal2-input" v-model="title">' +
                    '<div class="text-left">' + Lang.get('validation.attributes.content') + '</div>' +
                    '<textarea class="swal2-textarea resize-y" v-model="content"></textarea>',
                onOpen: function () {
                    form = Vue.createApp({
                        data() {
                            return {
                                title: '',
                                content: '',
                            }
                        }
                    }).mount('.swal2-content');
                },
                preConfirm: function (content) {
                    axios.post(route('admin.news.store'), form.$data)
                        .then(function (response) {
                            getDatatable().ajax.reload();
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            }));
        }

        function changeStatus(element) {
            let status = element.value,
                row = getDatatable().row($(element).closest('tr'));
            if (status === '' || !status) return;
            Swal.fire(composePopupBoxSchema({
                title: Lang.get('ui.change') + Lang.get('ui.new'),
                text: Lang.get('message.change_it_status', {
                    target: Lang.get('ui.id') + row.data().id + row.data().id,
                    value: element.selectedOptions[0].textContent
                }),
                type: 'warning',
            })).then(function (result) {
                if (result.value) {
                    $(element).hide();
                    axios.patch(route('admin.news.status.change', {news: row.data().id}), {status: status})
                        .then(function (response) {
                            getDatatable().ajax.reload(null, false);
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                            $(element).show();
                            $(element).val(row.data().status);
                        });
                } else {
                    $(element).val(row.data().status);
                }
            });
        }

        function update(element) {
            let form,
                row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: Lang.get('ui.edit') + Lang.get('ui.new'),
                html:
                    '<div class="text-left">' + Lang.get('validation.attributes.title') + '</div>' +
                    '<input type="text" class="swal2-input" v-model="title">' +
                    '<div class="text-left">' + Lang.get('validation.attributes.content') + '</div>' +
                    '<textarea class="swal2-textarea resize-y" v-model="content"></textarea>',
                onOpen: function () {
                    form = Vue.createApp({
                        data() {
                            return {
                                title: $('<textarea/>').html(row.data().title).text(),
                                content: $('<textarea/>').html(row.data().content).text(),
                            }
                        }
                    }).mount('.swal2-content');
                },
                preConfirm: function (content) {
                    axios.patch(route('admin.news.update', {news: row.data().id}), form.$data)
                        .then(function (response) {
                            getDatatable().ajax.reload(null, false);
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            fireErrorBox(error);
                        });
                }
            }));
        }

        function destroy(element) {
            let row = getDatatable().row($(element).closest('tr'));

            Swal.fire(composePopupBoxSchema({
                title: Lang.get('message.delete_target', {
                    target: Lang.get('ui.id'),
                    value: row.data().id
                }),
                text: Lang.get('message.delete_it'),
                type: 'warning',
            })).then(function (result) {
                if (result.value) {
                    axios.delete(route('admin.news.destroy', {news: row.data().id}))
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
    </script>
@endpush
