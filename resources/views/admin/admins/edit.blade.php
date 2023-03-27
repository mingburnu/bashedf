@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <div class="container-fluid">
            <h2 class="font-weight-bold">{{__('ui.edit') . __('ui.admin')}}</h2>

            @include('alerts.success')

            <div class="card mt-2 w-100">
                <div class="card-body">
                    <p>
                        <a href="{{route('admin.admins.index')}}">
                            <i class="fas fa-angle-left">{{__('ui.back') . __('ui.list')}}</i>
                        </a>
                    </p>
                    <form method="POST" action="{{route('admin.admins.update',['admin' => request('admin')])}}">
                        @csrf
                        @method('put')
                        {!! Form::rowInput('name', 'text', __('validation.attributes.name'), $errors, $admin->name, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.name')])]) !!}

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"
                                   for="email">{{__('validation.attributes.email')}}</label>
                            <div class="col-sm-10">
                                <p>{{$admin->email }}</p>
                            </div>
                        </div>

                        {!! Form::rowInput('password', 'password', __('validation.attributes.password'), $errors, null, ['placeholder' => __('message.please_enter', ['value' => __('validation.attributes.password')])]) !!}
                        {!! Form::rowInput('password_confirmation', 'password', __('validation.attributes.password_confirmation'), $errors, null, ['placeholder' => __('message.please_confirm', ['value' => __('validation.attributes.password')])]) !!}

                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="">{{trans('ui.manage_merchant')}}</label>
                            <div class="col-sm-10">
                                @foreach($users as $i => $user)
                                    <label for="{{"user$i"}}">{{$user->name}}</label>
                                    <input type="checkbox" id="{{"user$i"}}" name="users[]"
                                           value="{{$user->id}}"
                                            {{in_array($user->id, is_null(old('_token')) ? $admin->merchants->pluck('id')->toArray() : old('users') ?? []) ? 'checked':''}}>
                                @endforeach
                                @if ($errors->has('users'))
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $errors->first('users') }}</strong>
                                    </span>
                                @endif
                                @foreach ($errors->get('users.*') as $error_data)
                                    @foreach($error_data as $error)
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $error }}</strong>
                                        </span>
                                    @endforeach()
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"
                                   for="">{{__('ui.permission')}}</label>
                            <div class="col-sm-10">
                                @foreach($permissions as $i => $permission)
                                    <label for="{{"permission$i"}}">{{__("ui.$permission->name")}}</label>
                                    <input type="checkbox" id="{{"permission$i"}}" name="permissions[]"
                                           value="{{$permission->id}}"
                                            {{in_array($permission->id, is_null(old('_token')) ? $admin->permissions->pluck('id')->toArray() : old('permissions') ?? []) ? 'checked':''}}>
                                @endforeach
                                @if ($errors->has('users'))
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $errors->first('permissions') }}</strong>
                                    </span>
                                @endif
                                @foreach ($errors->get('permissions.*') as $error_data)
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
                                <button class="btn btn-success">{{trans('ui.modify')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection