@extends('layouts.app')


@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div>
            <h2 class="font-weight-bold">{{__('ui.create') . __('ui.clerk')}}</h2>
        </div>

        @include('alerts.success')

        <div class="card mt-2 w-100">
            <div class="card-body">
                <p>
                    <a href="{{route('children.index')}}">
                        <i class="fas fa-angle-left">{{__('ui.back') . __('ui.list')}}</i>
                    </a>
                </p>
                <form method="POST" action="{{ route('children.store') }}">
                    @csrf
                    {!! Form::rowInput('name', 'text', __('validation.attributes.name'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('ui.clerk_name')])]) !!}
                    {!! Form::rowInput('email', 'email', __('validation.attributes.email'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.email')])]) !!}
                    {!! Form::rowInput('phone', 'text', __('validation.attributes.phone'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.phone')])]) !!}
                    {!! Form::rowInput('password', 'password', __('validation.attributes.password'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.password')])]) !!}
                    {!! Form::rowInput('password_confirmation', 'password', __('validation.attributes.password_confirmation'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.password_confirmation')])]) !!}

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <button class="btn btn-success">{{__('ui.create')}}</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection
