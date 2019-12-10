@extends('layouts.app')
@section('title', 'Add a Weekend')
@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card">
          <div class="card-header">Add New Weekend</div>
          <div class="card-body">
            <form class="form-horizontal" role="form" method="post" action="{{ url('/weekend') }}">
              @csrf

              @include('weekend._create-edit', ['submitButtonText' => 'Add Weekend'])

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
