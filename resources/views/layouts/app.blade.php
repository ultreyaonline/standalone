<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('site.community_long_name') )</title>

    <!-- Fonts -->

    <!-- Styles -->
@section('bootstrap-css')
@show

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    @yield('extra_css')

    @if(isset($robots_rules) && $robots_rules == 'noindex')
    <meta name="robots" content="noindex, nofollow">
    @endif

    {{--Google Analytics--}}
    @if (config('google_analytics.google'))
        {{--<!-- Adds an event listener to capture uncaught errors. -->--}}
        <script>addEventListener('error', window.__e=function f(e){f.q=f.q||[];f.q.push(e)});</script>
    @endif

    @include('partials.favicon')
</head>
<body id="app-layout" class="members-page @yield('body-class','')">

@include('layouts.navbar')

{{--Info/Error Messaging--}}
<div id="flash-msg mt-1">
    @include('flash::message')
</div>
@include('common.errors')

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

{{--Display primary page content--}}
<div class="content-container" id="app">
    @yield('content')
</div>

{{--Render any footer content--}}
@include('partials.footer')


    <!-- JavaScripts -->
    <script src="{{ mix('/js/app.js') }}"></script>
@yield('page-js')

{{--Google Analytics--}}
@includeWhen(config('google_analytics.google'), 'partials.analytics')
@includeIf('system.tenant_analytics')

<script>
    function ConfirmDelete(){
        return confirm('Are you sure you want to delete?');
    }
    function ConfirmSend(){
        return confirm('Are you sure you want to send?');
    }
    function ConfirmAction(){
        return confirm('Are you sure you want to do this?');
    }
</script>

</body>
</html>
