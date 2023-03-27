@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div class="container-fluid">
            <h2 class="font-weight-bold">{{__('ui.payment') . __('ui.list')}}</h2>
        </div>
        <table class="table table-bordered w-100" id="datatable"></table>
    </div>
@endsection

@push('js')
    <script>
        Echo.private('payment').listen('LockedPaymentEvent', (e) => {
            let row = getEventRow(e);
            if (row.data() !== undefined) {
                row.data().admin_id = e.admin_id;
                row.data().admin = e.admin;
                row.data(row.data());
            }
        }).listen('UpdatedPaymentEvent', (e) => {
            let row = getEventRow(e);
            if (row.data() !== undefined) {
                row.data().process = e.process;
                row.data().status = e.status;
                row.data().checked_at = moment(e.checked_at).format('YYYY-MM-DD HH:mm:ss');
                row.data(row.data());
                refreshDetail(row);
            }
        }).listen('CallbackEvent', (e) => {
            let row = getEventRow(e);
            if (row.data() !== undefined) {
                row.data().callback_log = e.callback_log;
                row.data(row.data());
                refreshDetail(row);
            }
        }).listen('CanceledPaymentEvent', (e) => {
            let row = getEventRow(e);
            if (row.data() !== undefined) {
                row.data().payback_stamp = {payment_id: row.data().id};
                row.data(row.data());
            }
        }).listen('PickedBackPaymentEvent', (e) => {
            let row = getEventRow(e);
            if (row.data() !== undefined) {
                row.data().rewind_stamp = {payment_id: row.data().id};
                row.data(row.data());
            }
        });

        let ths = [
            {},
            {title: Lang.get('validation.attributes.order_id')},
            {title: Lang.get('ui.merchant')},
            {title: Lang.get('validation.attributes.balance')},
            {title: Lang.get('ui.payee')},
            {title: Lang.get('validation.attributes.customized_id')},
            {title: Lang.get('validation.attributes.bank_name')},
            {title: Lang.get('validation.attributes.account_number')},
            {title: Lang.get('validation.attributes.amount')},
            {title: Lang.get('validation.attributes.processing_fee')},
            {title: Lang.get('validation.attributes.total_amount')},
            {title: Lang.get('ui.applied_at')},
            {},
            {title: "&#x1F512;"},
            {},
            {title: '<input type="checkbox" onclick="markOrders(this)">'},
        ];

        setDatatable($('#datatable').DataTable(composeDataTableSchema({
            ajax: {
                url: route('admin.payments.index'),
                data: function (d) {
                    return $.extend(d, {});
                },
                dataSrc: function (json) {
                    $('th > input')[0].checked = false;
                    Swal.close();
                    return json.data;
                }
            },
            initComplete:
                function (settings, json) {
                    $('#datatable_length')
                        .append('<div id="datatable_refresh" class="ml-2 inline-block"><button class="btn btn-info" onclick="refresh()"><i class="fas fa-sync"></i></button></div>')
                        .append('<div id="datatable_copy" class="ml-2 inline-block"><button class="btn btn-info" onclick="copy()"><i class="fas fa-copy"></i></button></div>')
                        .append('<div id="datatable_lock" class="ml-2 inline-block"><button class="btn btn-warning" onclick="prepareLockOrders()"><i class="fas fa-lock"></i></button></div>')
                        .append('<div id="datatable_approve" class="ml-2 inline-block"><button class="btn btn-danger" onclick="prepareApproveOrders()"><i class="fas fa-check"></i></button></div>');
                },
            columnDefs: targetThs(ths),
            columns: [
                {
                    className: 'text-center',
                    defaultContent: '<i class="fa-plus-circle fas" onclick="switchDetail(this, loadIconByDTable)"></i>'
                },
                {data: 'order_id', name: 'order_id'},
                {data: 'user.name', name: 'user_name'},
                {data: 'transactions.0.new_balance', name: 'new_balance'},
                {data: 'account_name', name: 'account_name'},
                {data: 'customized_id', name: 'customized_id'},
                {data: 'bank_name', name: 'bank_name'},
                {data: 'account_number', name: 'account_number'},
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
                    data: 'process',
                    name: 'process',
                    render: function (data, type, row, meta) {
                        if (row.status === 0 && auth().id() === row.admin_id) {
                            return '<button value="1" class="btn btn-secondary btn-outline-success" onclick="judge(this)">&#x2714;&#xFE0F;</button>' +
                                '<button value="-1"  class="btn btn-secondary btn-outline-danger" onclick="judge(this)">&#x274C;</button>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'admin.name',
                    name: 'admin_name',
                    render: function (data, type, row, meta) {
                        if (row.status === 0 && data === undefined) {
                            return '<button class="btn btn-secondary btn-outline-dark" onclick="lock(this)">&#x1F512;</button>';
                        } else if (row.status !== 0 && data === undefined) {
                            return '';
                        }
                        return data;
                    }
                },
                {
                    data: 'status',
                    name: 'revert',
                    render: function (data, type, row, meta) {
                        if ([1, -1].indexOf(data) === -1) {
                            row.checked_at = '';
                        } else {
                            row.checked_at = moment(row.updated_at).format('YYYY-MM-DD HH:mm:ss');
                        }

                        let map = new Map();
                        map.set(Lang.get('validation.attributes.merchant_id'), row.user.merchant_id);
                        map.set(Lang.get('ui.payee'), row.account_name);
                        map.set(Lang.get('validation.attributes.bank_name'), row.bank_name);
                        map.set(Lang.get('ui.bank_icon'), `<span id="${row.id}_icon"></span>`);
                        map.set(Lang.get('validation.attributes.account_number'), row.account_number);
                        map.set(Lang.get('validation.attributes.branch'), row.branch);
                        map.set(Lang.get('ui.checked_at'), row.checked_at);
                        map.set(Lang.get('validation.attributes.callback_url'), row.callback_url ?? '');
                        map.set(Lang.get('ui.callback_data'), (row.callback_log != null ? JSON.stringify(row.callback_log.properties.data) : ''));
                        row.detail = map;

                        if (data === 1) {
                            if (row.payback_stamp) {
                                return Lang.get('message.check_canceled');
                            } else {
                                return '<button class="btn btn-danger" onclick="cancel(this)">&#36864;</button>';
                            }
                        } else if (data === -1) {
                            if (row.rewind_stamp) {
                                return Lang.get('message.check_pick_back');
                            } else {
                                return '<button class="btn btn-danger" onclick="pickBack(this)">&#25187;</button>';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    render: function (data, type, row, meta) {
                        if (row.admin_id === auth().id() && [1, -1].indexOf(row.status) === -1) {
                            $(getDatatable().row(meta.row).nodes()).css('background-color', '#c0c0c0');
                        } else if (row.status === 1) {
                            $(getDatatable().row(meta.row).nodes()).css('background-color', '#90ee90');
                        } else if (row.status === -1) {
                            $(getDatatable().row(meta.row).nodes()).css('background-color', '#ffc0cb');
                        }

                        if (row.checked) {
                            return '<input type="checkbox" onclick="mark(this)" checked>';
                        } else {
                            return '<input type="checkbox" onclick="mark(this)">';
                        }
                    }
                },
            ]
        })));

        function cancel(element) {
            let form,
                row = getDatatable().row($(element).closest('tr')),
                button = $(element);
            Swal.fire(composePopupBoxSchema({
                title: '<span class="mr-2">' + Lang.get('validation.attributes.order_id') + '</span>' + row.data().order_id,
                type: 'warning',
                width: '45rem',
                html:
                    '<div class="text-left"><label for="merchant_id">' + Lang.get('validation.attributes.merchant_id') + '</label>' +
                    '<input class="swal2-input" type="text" v-model="merchant_id"></div>' +
                    '<div class="text-left"><label for="account_name">' + Lang.get('validation.attributes.account_name') + '</label>' +
                    '<input class="swal2-input" type="text" v-model="account_name"></div>' +
                    '<div class="text-left"><label for="account_number">' + Lang.get('validation.attributes.account_number') + '</label>' +
                    '<input class="swal2-input" type="text" v-model="account_number"></div>' +
                    '<p>' + Lang.get('message.cancel_it', {value: row.data().total_amount}) + '</p>',
                onOpen: function () {
                    form = Vue.createApp({
                        data() {
                            return {
                                merchant_id: '',
                                account_name: '',
                                account_number: ''
                            }
                        }
                    }).mount('.swal2-content');
                },
                preConfirm: function (content) {
                    button.hide();
                    axios.post(route('admin.payments.payback-stamps.store', {payment: row.data().id}), form.$data)
                        .then(function (response) {
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            button.show();
                            fireErrorBox(error);
                        });
                }
            }));
        }

        function pickBack(element) {
            let form,
                row = getDatatable().row($(element).closest('tr')),
                button = $(element);
            Swal.fire(composePopupBoxSchema({
                title: '<span class="mr-2">' + Lang.get('validation.attributes.order_id') + '</span>' + row.data().order_id,
                type: 'warning',
                width: '45rem',
                html:
                    '<div class="text-left"><label for="merchant_id">' + Lang.get('validation.attributes.merchant_id') + '</label>' +
                    '<input class="swal2-input" type="text" v-model="merchant_id"></div>' +
                    '<div class="text-left"><label for="account_name">' + Lang.get('validation.attributes.account_name') + '</label>' +
                    '<input class="swal2-input" type="text" v-model="account_name"></div>' +
                    '<div class="text-left"><label for="account_number">' + Lang.get('validation.attributes.account_number') + '</label>' +
                    '<input class="swal2-input" type="text" v-model="account_number"></div>' +
                    '<p>' + Lang.get('message.rededuct_it', {value: row.data().total_amount}) + '</p>',
                onOpen: function () {
                    form = Vue.createApp({
                        data() {
                            return {
                                merchant_id: '',
                                account_name: '',
                                account_number: ''
                            }
                        }
                    }).mount('.swal2-content');
                },
                preConfirm: function (content) {
                    button.hide();
                    axios.post(route('admin.payments.rewind-stamps.store', {payment: row.data().id}), form.$data)
                        .then(function (response) {
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            button.show();
                            fireErrorBox(error);
                        });
                }
            }));
        }

        function mark(element) {
            let row = getDatatable().row($(element).closest('tr'));
            row.data().checked = element.checked;
        }

        function markOrders(element) {
            for (let i = 0; i < getDatatable().rows().eq(0).length; i++) {
                let row = getDatatable().row(i);
                row.data().checked = element.checked;
                row.data(row.data());
            }
        }

        function copy() {
            let text = "",
                textarea = $('<textarea>');
            for (let i = 0; i < getDatatable().rows().eq(0).length; i++) {
                if (getDatatable().row(i).data().checked) {
                    text = text + getDatatable().row(i).data().account_name + '\n'
                        + getDatatable().row(i).data().bank_name + '\n'
                        + getDatatable().row(i).data().account_number + '\n'
                        + getDatatable().row(i).data().amount + '\n\r';
                }
            }

            $("body").append(textarea);
            textarea.val(text).select();
            document.execCommand("copy");
            textarea.remove();

            Swal.fire({
                title: '<i class="fas fa-clipboard-check"></i>',
                type: 'info',
                timer: 250,
                showConfirmButton: false,
            });
        }

        function lock(element) {
            let row = getDatatable().row($(element).closest('tr')),
                button = $(element);
            Swal.fire(composePopupBoxSchema({
                title: '<span class="mr-2">' + Lang.get('validation.attributes.order_id') + '</span>' + row.data().order_id,
                text: Lang.get('message.lock_it'),
                type: 'question',
                width: '45rem',
            })).then(function (result) {
                if (result.value) {
                    button.hide();
                    axios.patch(route('admin.payments.task.lock', {payment: row.data().id}))
                        .then(function (response) {
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            button.show();
                            fireErrorBox(error);
                        });
                }
            });
        }

        function prepareLockOrders() {
            Swal.fire(composePopupBoxSchema({
                text: Lang.get('message.lock_them'),
                type: 'question',
                width: '45rem'
            })).then(function (result) {
                if (result.value) {
                    lockOrders();
                }
            });
        }

        async function lockOrders() {
            let counter = {count: 0, errors: []},
                promises = [new Promise((resolve, reject) => {
                    resolve(counter);
                })];

            getDatatable().rows().reverse().every(function (rowIdx, tableLoop, rowLoop) {
                let row = this,
                    button = $(row.node()).find('button');

                if (row.data().checked && row.data().status === 0 && row.data().admin === null) {
                    button.hide();
                    promises.push(axios.patch(route('admin.payments.task.lock', {payment: row.data().id}))
                        .then(function (response) {
                            counter.count = counter.count + 1;
                            return counter;
                        })
                        .catch(function (error) {
                            button.show();
                            counter.errors.push(row.data().order_id);
                            return counter;
                        }));
                }
            });

            await Promise.all(promises);
            promises[promises.length - 1].then(function (result) {
                if (result.errors.length === 0) {
                    Swal.fire(Lang.get('message.success'), Lang.get('message.lock_count', {value: result.count}), 'success');
                } else {
                    let msg = listErrors({
                        0: Lang.get('message.lock_count', {value: result.count}),
                        1: Lang.get('message.fail') + Lang.get('ui.order'),
                        2: result.errors
                    });

                    Swal.fire(Lang.get('message.fail'), msg, 'error');
                }
            });
        }

        function judge(element) {
            let status = element.value,
                row = getDatatable().row($(element).closest('tr')),
                button = $(element);
            if (status === '' || !status) return;
            Swal.fire(composePopupBoxSchema({
                title: '<span class="mr-2">' + Lang.get('validation.attributes.order_id') + '</span>' + row.data().order_id,
                text: Lang.get('message.change_it_status', {target: Lang.get('ui.order'), value: element.textContent}),
                type: 'warning',
                width: '45rem'
            })).then(function (result) {
                if (result.value) {
                    button.parent().children().hide();
                    axios.patch(route('admin.payments.update', {payment: row.data().id}), {status: status})
                        .then(function (response) {
                            fireSuccessBox(response);
                        })
                        .catch(function (error) {
                            button.parent().children().show();
                            fireErrorBox(error);
                        });
                }
            })
        }

        function prepareApproveOrders() {
            Swal.fire(composePopupBoxSchema({
                text: Lang.get('message.approve_them'),
                type: 'warning',
                width: '45rem'
            })).then(function (result) {
                if (result.value) {
                    approveOrders();
                }
            });
        }

        async function approveOrders() {
            let counter = {count: 0, errors: []},
                promises = [new Promise((resolve, reject) => {
                    resolve(counter);
                })];

            getDatatable().rows().reverse().every(function (rowIdx, tableLoop, rowLoop) {
                let row = this,
                    buttons = $(row.node()).find('button');

                if (row.data().checked && row.data().status === 0 && row.data().admin_id === auth().id()) {
                    buttons.hide();
                    promises.push(axios.patch(route('admin.payments.update', {payment: row.data().id}), {status: 1})
                        .then(function (response) {
                            counter.count = counter.count + 1;
                            return counter;
                        })
                        .catch(function (error) {
                            buttons.show();
                            counter.errors.push(row.data().order_id);
                            return counter;
                        })
                    );
                }
            });

            await Promise.all(promises);
            promises[promises.length - 1].then(function (result) {
                if (result.errors.length === 0) {
                    Swal.fire(Lang.get('message.success'), Lang.get('message.approve_count', {value: result.count}), 'success');
                } else {
                    let msg = listErrors({
                        0: Lang.get('message.approve_count', {value: result.count}),
                        1: Lang.get('message.fail') + Lang.get('ui.order'),
                        2: result.errors
                    });

                    Swal.fire(Lang.get('message.fail'), msg, 'error');
                }
            });
        }
    </script>
@endpush