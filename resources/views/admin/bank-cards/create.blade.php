@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div class="container-fluid">
            <h2 class="font-weight-bold">{{__('ui.create') . __('ui.bank_card')}}</h2>

            @include('alerts.success')

            <div class="card mt-2 w-100">
                <div class="card-body">
                    <p>
                        <a href="{{route('admin.bank-cards.index')}}">
                            <i class="fas fa-angle-left">{{__('ui.back') . __('ui.list')}}</i>
                        </a>
                    </p>
                    <form method="POST" action="{{ route('admin.bank-cards.store') }}">
                        @csrf
                        {!! Form::rowInput('account_name', 'text', __('validation.attributes.account_name'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.account_name')])]) !!}
                        {!! Form::rowInput('bank_name', 'text', __('validation.attributes.bank_name'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.bank_name')])]) !!}
                        {!! Form::rowInput('account_number', 'text', __('validation.attributes.account_number'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.account_number')])]) !!}
                        {!! Form::rowInput('bank_district', 'text', __('validation.attributes.bank_district'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.bank_district')])]) !!}
                        {!! Form::rowInput('bank_address', 'text', __('validation.attributes.bank_address'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.bank_address')])]) !!}

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
