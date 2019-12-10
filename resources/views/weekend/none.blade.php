@extends('layouts.app')

@section('title')
{{ empty($id) ? 'Weekends' : $weekend->weekend_full_name }}
@endsection

@section('body-class', 'weekends')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-sm-8 offset-sm-2">
        <div class="card">
          <div class="card-body">

            <p>No weekends have been defined yet.</p>
            @can('create weekends')
              <p>To create a weekend, use the Admin menu above.</p>
            @endcan

          </div>
        </div>

      </div>

    </div>
  </div>
@endsection
