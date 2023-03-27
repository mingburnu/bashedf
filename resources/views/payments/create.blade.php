@extends('layouts.app')

@if (!empty(old('payments')) && is_array(old('payments')))
    @php
        $payments = old('payments');
    @endphp
@else
    @php
        $payments = [];
    @endphp
@endif
@push('css')
    <style>
        tr > td:nth-child(3) > input {
            width: auto;
            display: inline-block;
        }

        tr > td:nth-child(3) > button {
            display: inline-block;
        }

        tr > td:nth-child(6) > input {
            width: auto;
            display: inline-block;
        }

        tr > td:nth-child(6) > button {
            display: inline-block;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row mb-4 pr-3">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.create') . __('ui.payment')}}</h2>
                </div>
            </div>
            <div>
                @include('alerts.success')
                <div>
                    <form method="POST" action="{{route('payments.store')}}" id="form-data">
                        @csrf
                        <table id="datatable" class="table"></table>
                        <input type="hidden" name="google_key">
                        <input type="hidden" id="lock" name="_lock">
                        <button id="form-submit" class="btn btn-success">{{__('ui.submit')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="payments" value="{{json_encode($payments)}}">
    <input type="hidden" id="min_payment_amount" value="{{$contract->min_payment_amount}}">
    <input type="hidden" id="max_payment_amount" value="{{$contract->max_payment_amount}}">
    <div id="errors" class="hidden">
        @foreach($errors->all() as $error)
            <p>{{$error}}</p>
        @endforeach
    </div>
@endsection
@push('js')
    <script>
        let errors = $('div#errors').children(),
            massages = '',
            min = parseFloat($('#min_payment_amount').val()),
            max = parseFloat($('#max_payment_amount').val()),
            ths = [
                {title: Lang.get('validation.attributes.account_name')},
                {title: Lang.get('validation.attributes.account_number')},
                {title: Lang.get('validation.attributes.bank_name')},
                {title: Lang.get('validation.attributes.branch')},
                {title: Lang.get('validation.attributes.callback_url')},
                {title: Lang.get('validation.attributes.customized_id')},
                {title: Lang.get('validation.attributes.amount'), width: "8%"},
                {
                    title: '<button id="addRow" class="btn btn-primary"><i class="fas fa-plus"></i></button>',
                    width: "5%"
                }
            ];

        if (errors.length > 0) {
            errors.each(function () {
                massages = massages + $(this)[0].outerHTML;
            });

            Swal.fire(Lang.get('message.fail'), massages, 'error');
        }

        setDatatable($('#datatable').DataTable({
            paging: false, searching: false, ordering: false, info: false, autoWidth: false, columnDefs: targetThs(ths),
        }));

        let counter = 0;

        function addRow(times = 1) {
            let action = '<button class="removeRow btn btn-danger"><i class="fas fa-minus"></i></button>',
                bankBtn = '<button class="btn btn-secondary bankBtn"><i class="fas fa-search"></i></button>',
                uuidBtn = '<button class="btn btn-secondary inline-block uuidBtn">' + Lang.get('ui.uuid') + '</button>';

            for (let i = 0; i < times; i++) {
                getDatatable().row.add([
                    '<input type="text" class="form-control" name="payments[' + counter + '][account_name]" required pattern=".{1,255}">',
                    '<input type="text" class="form-control" name="payments[' + counter + '][account_number]" required pattern=".{6,30}">',
                    '<input type="text" class="form-control" name="payments[' + counter + '][bank_name]" required pattern=".{1,140}">' + bankBtn,
                    '<input type="text" class="form-control" name="payments[' + counter + '][branch]" required pattern=".{1,255}">',
                    '<input type="text" class="form-control" name="payments[' + counter + '][callback_url]" pattern=".{1,255}">',
                    '<input type="text" class="form-control" name="payments[' + counter + '][customized_id]" required pattern=".{1,36}">' + uuidBtn,
                    '<input type="number" class="form-control" name="payments[' + counter + '][amount]" required step="0.01" min="' + min + '" max="' + max + '">',
                    action
                ]).draw(false);

                counter++;
            }
        }

        $('#datatable thead').on('click', '#addRow', function (e) {
            e.preventDefault();
            addRow();
        });

        $('#datatable tbody').on('click', '.removeRow', function (e) {
            e.preventDefault();
            getDatatable().row($(this).parents('tr')).remove().draw();
        }).on('click', '.bankBtn', function (e) {
            e.preventDefault();
            getBankName($(this).parent().prev().children().val(), $(this).prev());
        }).on('click', '.uuidBtn', function (e) {
            e.preventDefault();
            $(this).prev().val(uuid.v4());
        });

        $('#form-submit').on('click', function (e) {
            if ($("#form-data")[0].checkValidity()) {
                e.preventDefault();
                if ($('table#datatable > tbody > tr').length === 0 || $('td.dataTables_empty').length === 1) {
                    Swal.fire(Lang.get('ui.order'), Lang.get('validation.min.array', {attribute: '', min: 1}), 'info');

                    if (getDatatable().row().length === 0) {
                        $('table#datatable > tbody').children().remove();
                        addRow();
                    } else {
                        getDatatable().row().draw();
                    }
                } else {
                    Swal.fire(composePopupBoxSchema({
                        title: '',
                        html:
                            '<div class="text-left"><label for="googleKey">' + Lang.get('validation.attributes.google_key') + '</label>' +
                            '<input id="googleKey" class="swal2-input" type="password"></div>',
                        preConfirm: function () {
                            $('#lock').val(uuid.v4());
                            $('input[name="google_key"]').val($('#googleKey').val());
                            $('#form-data').submit();
                            $('input').attr('disabled', true);
                            $('button').attr('disabled', true);
                        }
                    }));
                }
            }
        });

        function validateAmount(element) {
            let input = element,
                val = input.val(),
                msg = Lang.get('validation.decimal_between', {
                    attribute: Lang.get('validation.attributes.amount'),
                    min: min,
                    max: max
                });

            if (val < min || val > max) {
                element[0].setCustomValidity(msg);
            } else {
                element[0].setCustomValidity('');
            }
        }

        $(document).on('click keyup paste', "input[type='number']", function () {
            validateAmount($(this));
        });

        let payments = JSON.parse($('#payments').val());
        if (payments.length > 0) {
            payments.forEach(function (payment) {
                addRow();
                $('input[name$="[account_name]"]').last().val(payment.account_name);
                $('input[name$="[bank_name]"]').last().val(payment.bank_name);
                $('input[name$="[account_number]"]').last().val(payment.account_number);
                $('input[name$="[branch]"]').last().val(payment.branch);
                $('input[name$="[callback_url]"]').last().val(payment.callback_url);
                $('input[name$="[customized_id]"]').last().val(payment.customized_id);
                $('input[name$="[amount]"]').last().val(payment.amount);
                validateAmount($('input[type="number"]').last());
            });
        } else {
            addRow();
        }
    </script>
@endpush