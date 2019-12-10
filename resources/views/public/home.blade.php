@extends('layouts.calculate')

@section('title')
    {{ config('site.community_long_name') }}
@endsection

@section('body-class', 'home')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1 col-xl-8 offset-xl-2">
                <div class="text-center">
                    <img src="logo/logo.jpg" title="{{ config('site.community_long_name') }}" class="text-center">
                </div>
                <div class="m-3">
                    <h1 class="text-center">Welcome to<br>{{ config('site.community_long_name') }}</h1>
                </div>
                <div class="m-3">
                    <p style="font-size: 24px">Tres Dias is a three-day Christian renewal experience which aims to concentrate on the person and teachings of Jesus Christ.
                        While Tres Dias explores the basic Christian beliefs, it is best described as a spiritual encounter with Christ.
                        Many who have attended a weekend have experienced a deeper and more meaningful relationship with Christ as they sense His love in a dynamic way.
                        Tres Dias is interdenominational in its teaching and outreach. It is a laity-led experience and has no affiliation with any particular Christian denomination.</p>
                </div>
                <div class="text-center">
                    <a class="btn btn-lg btn-primary" href="{{ url('/about') }}">Learn More</a>
                </div>
            </div>
        </div>
    </div>

@endsection
