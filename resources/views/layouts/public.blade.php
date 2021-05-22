<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="no-js">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@yield('title', config('site.community_long_name'))</title>
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
<body class="public-page @yield('body-class','')">
<div id="main-container">

@include('layouts.navbar')

@include('common.errors')

<div class="content-container" id="app">
	@yield('content')
</div>
@include('partials.footer')
</div>


<script src="{{ mix('/js/app.js') }}"></script>
@yield('page-js')

@includeWhen(config('google_analytics.google'), 'partials.analytics')
@includeIf('system.tenant_analytics')

</body>
</html>
