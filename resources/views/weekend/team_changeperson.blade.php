@extends('layouts.app')

@section('title')
Change Person: {{ $weekend->weekend_full_name }}
@endsection

@section('body-class', 'team-editor')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="card">
          <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\TeamAssignmentController@update', ['weekend'=>$weekend->id, 'oldposition'=>$assignment->roleID, 'member'=>$assignment->user->id]) }}">
            @csrf @method('patch')

          <div class="card-header card-title">{{ $weekend->weekend_full_name }}</div>
          <div class="card-body">

            <div class="form-group row">
              <label class="col-md-2 offset-md-1 control-label"><strong>Person:</strong></label>
              <div class="col-md-8">
                <strong>{{ $assignment->user->name }}</strong> ({{ $assignment->user->weekend }})
              </div>
            </div>

            <div class="form-group row{{ $errors->has('roleID') ? ' is-invalid' : '' }}">
              <label class="col-md-2 offset-md-1 col-form-label" for="memberID">Position:</label>

              <div class="col-md-8">
                @include('weekend._position_selector', ['fieldname' => 'roleID', 'current' => old('roleID') ?: $assignment->roleID, 'autofocus'=> true])
                @if ($errors->has('roleID'))
                  <span class="form-text"> <strong>{{ $errors->first('roleID') }}</strong> </span>
                @endif
              </div>
            </div>

            @include('weekend._status_selector')

            <div class="form-group row{{ $errors->has('comments') ? ' is-invalid' : '' }}">
              <label class="col-md-3 text-md-right col-form-label" for="comments">Notes to self:</label>

              <div class="col-md-9">
                <input type="text" class="form-control" name="comments" id="comments" value="{{ old('comments') ?: $assignment->comments }}" placeholder="optional: things discussed or other thoughts">
                @if ($errors->has('comments'))
                  <span class="form-text"> <strong>{{ $errors->first('comments') }}</strong> </span>
                @endif
              </div>
            </div>


          </div>
          <div class="card-footer small">
            <p>Please carefully check the information before saving</p>
          </div>

          <div class="form-group row m-2">
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary" name="buttonAction" value="save"><i class="fa fa-btn fa-save"></i> Save </button>
              <a href="javascript:history.back();"><button type="button" class="btn btn-secondary"><i class="fa fa-btn fa-times-circle"></i> Cancel</button></a>
            </div>
          </div>

          </form>
        </div>
      </div>

      @if($weekend->rectorID == Auth::id() || auth()->user()->can('use leaders worksheet'))
      <div class="col-12 m-2">
        <div class="card bg-light">
          <div class="card-body d-flex justify-content-around">
            <a href="/reports/leaders-worksheet?mw={{ $weekend->weekend_MF }}"><button class="btn btn-info m-1"><i class="fa fa-list"></i> Leaders Worksheet</button></a>
            <a href="{{ url('/reports/interested_in_serving') }}"><button class="btn btn-info m-1"><i class="fa fa-tint"></i> Members Interested in Serving</button></a>
            <a href="{{ url('/reports/service_by_position') }}"><button class="btn btn-info m-1"><i class="fa fa-tint"></i> Service By Position</button></a>
          </div>
        </div>
      </div>
    </div>
  @endif
  </div>
@endsection
