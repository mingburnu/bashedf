<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
          name='viewport'/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-debug" content="{{config('app.debug')}}">
    @foreach (collect(config('auth.guards'))->keys() as $guard)
        @if (auth($guard)->check())
            <meta name="auth-user" content="{{auth()->user()}}" id="{{$guard}}">
        @endif
    @endforeach
    @routes
    <link rel="stylesheet" href="{{mix('css/app.css')}}">
    <script type="text/javascript" src="{{ mix('js/app.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/messages.js')}}"></script>

    <!-- using local links -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}"/>
    @stack('css')
    <title>{{config('app.project_name')}}</title>
    <script>
        Lang.setLocale($('html').attr('lang'));
    </script>
</head>

<body>
<div class="page-wrapper bg1 default-theme toggled boder-radius-on bg-trueGray-100">
    @include('layouts.page_template.auth')
</div>

@stack('js')

</body>
</html>