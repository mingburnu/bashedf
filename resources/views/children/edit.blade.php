@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div class="container-fluid">
            <h2 class="font-weight-bold">{{__('ui.edit') . __('ui.clerk')}}</h2>

            @include('alerts.success')

            <div class="card mt-2 w-100">
                <div class="card-body">
                    <p>
                        <a href="{{route('children.index')}}">
                            <i class="fas fa-angle-left">{{__('ui.back') . __('ui.list')}}</i>
                        </a>
                    </p>
                    <form method="POST" action="{{route('children.update', $child->id)}}">
                        @csrf
                        @method('PATCH')
                        {!! Form::rowInput('name', 'text', __('validation.attributes.name'), $errors, $child->name, ['placeholder' => __('message.please_enter', ['value' => __('ui.clerk_name')])]) !!}
                        {!! Form::rowInput('email', 'email', __('validation.attributes.email'), $errors, $child->email, ['disabled' => true]) !!}
                        {!! Form::rowInput('password', 'password', __('validation.attributes.password'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.password')])]) !!}
                        {!! Form::rowInput('password_confirmation', 'password', __('validation.attributes.password_confirmation'), $errors, null, ['placeholder' => __('message.please_confirm', ['value' => __('validation.attributes.password')])]) !!}

                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <button class="btn btn-success">{{__('ui.modify')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection