<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
          name='viewport'/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{mix('css/app.css')}}">
    <!-- using local links -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/now-ui-dashboard.css?v=1.3.0') }}"/>

    <title>{{config('app.project_name')}}</title>
</head>

<body class="{{ $class ?? '' }}">
<div class="wrapper">
    @guest
        @include('layouts.page_template.guest')
    @endguest
</div>
<script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
@stack('js')
</body>
</html>