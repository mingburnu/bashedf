@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div class="container-fluid">
            <h2 class="font-weight-bold">{{__('ui.two_way_validation_api')}}</h2>

            @include('alerts.success')

            <div class="card mt-2 w-100">
                <div class="card-body">
                    <p>
                        <a href="{{route('profile.index')}}">
                            <i class="fas fa-angle-left">{{__('ui.back') . __('ui.list')}}</i>
                        </a>
                    </p>
                    <form method="POST" action="{{route('authorizer.update')}}">
                        @csrf
                        @method('PUT')
                        {!!Form::rowInput('api', 'url', 'POST API', $errors, $authorizer->api, ['required', 'pattern' => ".{1,255}"])!!}
                        {!!Form::rowInput('boolean_index', 'text', __('validation.attributes.boolean_index'), $errors, $authorizer->boolean_index, ['required', 'pattern' => ".{1,40}"])!!}
                        {!!Form::rowInput('additional_parameters', 'textarea', __('validation.attributes.additional_parameters'), $errors, $authorizer->additional_parameters) !!}
                        {!!Form::rowSubmit(trans('ui.modify'))!!}
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection