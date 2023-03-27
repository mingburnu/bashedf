@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST"
                              action="{{auth('admin')->check() ? route('admin.google2fa') : route('google2fa') }}">
                            @csrf
                            <div class="form-group">
                                <label for="one_time_password"
                                       class="col-md-6 control-label">{{__('message.please_enter', ['value' => __('validation.attributes.google_key')])}}</label>

                                <div class="col-md-6">
                                    <input id="one_time_password" type="number" class="form-control"
                                           name="one_time_password" required autofocus>
                                    @if($errors->has('message'))
                                        @foreach($errors->get('message') as $msg)
                                            <div class="invalid-feedback d-block"><strong>{{$msg}}</strong></div>
                                        @endforeach
                                    @endif
                                </div>



                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{__('ui.submit')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection