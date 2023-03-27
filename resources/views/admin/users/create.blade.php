@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div class="container-fluid">
            <h2 class="font-weight-bold">{{__('ui.create') . __('ui.merchant')}}</h2>

            @include('alerts.success')

            <div class="card mt-2 w-100">
                <div class="card-body">
                    <p>
                        <a href="{{route('admin.users.index')}}">
                            <i class="fas fa-angle-left">{{__('ui.back') . __('ui.list')}}</i>
                        </a>
                    </p>
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        {!! Form::rowInput('name', 'text', __('validation.attributes.name'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('ui.merchant_name')])]) !!}
                        {!! Form::rowInput('email', 'email', __('validation.attributes.email'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.email')])]) !!}
                        {!! Form::rowInput('company', 'text', __('validation.attributes.company'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.company')])]) !!}
                        {!! Form::rowInput('phone', 'text', __('validation.attributes.phone'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.phone')])]) !!}
                        {!! Form::rowInput('password', 'password', __('validation.attributes.password'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.password')])]) !!}
                        {!! Form::rowInput('password_confirmation', 'password', __('validation.attributes.password_confirmation'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.password_confirmation')])]) !!}
                        {!! Form::rowInput('deposit_processing_fee_percent', 'number', __('validation.attributes.deposit_processing_fee_percent'), $errors, null, ['min' => '0', 'max' => '100', 'step' => '0.0001', 'placeholder' => __('message.please_enter', ['value' => __('validation.attributes.deposit_processing_fee_percent')])]) !!}
                        {!! Form::rowInput('min_deposit_amount', 'number', __('validation.attributes.min_deposit_amount'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.min_deposit_amount')])]) !!}
                        {!! Form::rowInput('max_deposit_amount', 'number', __('validation.attributes.max_deposit_amount'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.max_deposit_amount')])]) !!}
                        {!! Form::rowInput('payment_processing_fee', 'number', __('validation.attributes.payment_processing_fee'), $errors, null, ['min' => '0', 'step' => '0.01', 'placeholder' => __('message.please_enter', ['value' => __('validation.attributes.payment_processing_fee')])]) !!}
                        {!! Form::rowInput('min_payment_amount', 'number', __('validation.attributes.min_payment_amount'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.min_payment_amount')])]) !!}
                        {!! Form::rowInput('max_payment_amount', 'number', __('validation.attributes.max_payment_amount'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.max_payment_amount')])]) !!}

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"
                                   for="bank_cards">{{__('ui.bind') . __('ui.bank_card')}}</label>
                            <div class="col-sm-10">
                                <select name="bank_cards[]" id="bank_cards" multiple
                                        class="form-control {{ $errors->has('bank_cards') ? ' border-danger' : ''  }}">
                                    @foreach ($allBankCards as $card)
                                        <option value="{{$card->id}}" {{in_array($card->id, old('bank_cards') ?? []) ? 'selected':''}}>
                                            {{$card->account_name . '/' . $card->bank_name . '/' . $card->account_number}}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('bank_cards'))
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $errors->first('bank_cards') }}</strong>
                                    </span>
                                @endif
                                @foreach ($errors->get('bank_cards.*') as $error_data)
                                    @foreach($error_data as $error)
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $error }}</strong>
                                        </span>
                                    @endforeach()
                                @endforeach
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <button class="btn btn-success">{{__('ui.create')}}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
            $('select').selectpicker();
        });
    </script>
@endpush
