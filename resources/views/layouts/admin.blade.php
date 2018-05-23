<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">




    <!-- Styles -->

    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    <link href="{{asset('css/admin.css')}}" rel="stylesheet">

    @yield('styles')
</head>
<body class="timerly-font-pnr">

<div class="">@include('layouts.admin_nav')</div>

<div class="workspace">
    <div class ="workspace-title">@yield('workspaceheader') </div>
    <div class ="workspace-body">@yield('content')</div>
</div>

<!-- Scripts -->

<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>
