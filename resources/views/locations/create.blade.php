@extends ('layouts.app')

@section('title', 'Create a Location')


@section('content')
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">Add A Community Meeting Location</div>
          <div class="card-body">
            <form class="form-horizontal row" role="form" method="POST" action="{{ action('LocationController@store') }}">
              @csrf

              @include('locations._input_fields')

              <div class="form-group row col-12">
                  <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-btn fa-save"></i> Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

