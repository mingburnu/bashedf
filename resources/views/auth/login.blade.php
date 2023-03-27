@extends('layouts.guest', [
    'class' => 'login-page sidebar-mini ',
    'activePage' => 'login',
])

@section('content')
    <div class="content">
        <div class="container">
            <div class="container">
                <div class="header-body text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-12 col-md-9 mb-5">
                            <h5 class="text-lead text-light info-title">{{config('app.project_name')}}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 ml-auto mr-auto">
                <form method="POST" aria-label="{{ __('ui.login') }}" action="{{$action}}">
                    @csrf
                    <div class="card card-login card-plain">
                        <div class="card-header ">
                            <div class="logo-container"><img src="{{ asset('images/apple.png') }}" alt=""></div>
                        </div>
                        <div class="card-body ">
                            <div class="input-group no-border form-control-lg {{ $errors->has('email') ? 'has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="now-ui-icons users_circle-08"></i></div>
                                </div>
                                <input type="email" class="form-control" name="email" required
                                       placeholder="{{ __('validation.attributes.email') }}" value="{{ old('email') }}"
                                       autocomplete="email" autofocus>
                            </div>
                            <div class="mt-[20px] input-group no-border form-control-lg {{ $errors->has('password') ? 'has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="now-ui-icons objects_key-25"></i></div>
                                </div>
                                <input type="password" class="form-control" name="password" required
                                       placeholder="{{ __('validation.attributes.password') }}"
                                       autocomplete="current-password">
                            </div>
                        </div>
                        <div class="container">
                            @foreach($errors->all() as $msg)
                                <span class="text-center invalid-feedback text-danger d-block" role="alert">
                                    <strong>{{$msg}}</strong>
                                </span>
                            @endforeach
                        </div>
                        <div class="card-footer ">
                            <button type="submit"
                                    class="btn btn-primary btn-round btn-lg btn-block mb-3">{{ __('ui.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
