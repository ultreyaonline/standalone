@extends('layouts.app')

@section('title')
Add Person: {{ $weekend->weekend_full_name }}
@endsection

@section('body-class', 'team-editor')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="card">
          <form class="form-horizontal" role="form" method="POST" action="{{ action('TeamAssignmentController@store', $weekend->id) }}">
            @csrf

          <div class="card-header">{{ $weekend->weekend_full_name }}</div>
          <div class="card-footer small">
            Rectors use this form to add people, one at a time, to the team roster by selecting one person from the first list,
            and then the position they are serving from the second list then clicking on SAVE button at the bottom of the page.
          </div>
          <div class="card-body">


              <div class="form-group row{{ $errors->has('memberID') ? ' is-invalid' : '' }}">
              <label class="col-md-2 offset-md-1 col-form-label" for="memberID"><strong>Person:</strong></label>

              <div class="col-md-8">
                <div class="row">
                  <div class="col-12">
                    @include('members._member_selector', ['fieldname' => 'memberID', 'current' => old('memberID') ?: '', 'autofocus' => true])
                    @if ($errors->has('memberID'))
                      <span class="form-text"> <strong>{{ $errors->first('memberID') }}</strong> </span>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row{{ $errors->has('roleID') ? ' is-invalid' : '' }}">
              <label class="col-md-2 offset-md-1 col-form-label" for="roleID"><strong>Position:</strong></label>

              <div class="col-md-8">
                @include('weekend._position_selector', ['fieldname' => 'roleID', 'current' => old('roleID') ?: ''])
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
            <p>Note: List contains active community members only, check with Leaders person to reinstate someone to the active list.</p>
          </div>

          <div class="form-group row m-2">
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-success" name="buttonAction" value="save"><i class="fa fa-btn fa-save"></i> Save </button>
              <button type="submit" class="btn btn-success" name="buttonAction" value="saveAndAdd"><i class="fa fa-btn fa-save"></i> Save and Add Another </button>
              <a href="javascript:history.back();"><button type="button" class="btn btn-secondary"><i class="fa fa-btn fa-times-circle"></i> Cancel</button></a>
            </div>
            @can('add community member')
              <div class="col-12 text-right"><p></p>
                <a href="/member/add" class="btn btn-primary" role="button"><i class="fa fa-user-plus" aria-hidden="true"></i> New Member</a>
              </div>
            @endcan

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
