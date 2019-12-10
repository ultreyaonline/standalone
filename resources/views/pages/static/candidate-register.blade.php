@extends('layouts.app')

@section('title')
  {{ config('site.community_acronym') }} Pre-Weekend Activities
@endsection

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 offset-md-1">


        @can('create members')
        <li><a href="{{ url('/register') }}">Register</a></li>
        @endcan



      </div>
    </div>
  </div>
@endsection
