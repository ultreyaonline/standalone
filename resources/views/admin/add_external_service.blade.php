@extends('layouts.app')
@section('title', 'Add EXTERNAL Service History')
@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card">
          <div class="card-header">Add EXTERNAL Service History</div>
          <div class="card-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\WeekendExternalController@' . ($mode === 'edit' ? 'update' : 'store'), $service->id) }}">
              @csrf
              @if($mode === 'edit')
                @method('patch')
              @endif

              <div class="form-group{{ $errors->has('memberID') ? ' is-invalid' : '' }}">
                <label class="col-md-4 control-label" for="memberID">Member</label>

                  <div class="row ml-1">
                <div class="col-md-6">
                  @include('members._member_selector', ['fieldname' => 'memberID', 'current' => old('memberID') ?: $service->memberID])
                  @if ($errors->has('memberID'))
                    <span class="form-text"> <strong>{{ $errors->first('memberID') }}</strong> </span>
                  @endif
                </div>
                @if($mode === 'create' && !request()->has('withExternal'))
                <div class="col-md-6">
                    <a href="{!! request()->fullUrl() !!}?&withExternal=1"><i class="fa fa-plus-circle"></i> Include Non-Local Members</a>
                </div>
                @endif
                  </div>
              </div>


              <div class="form-group{{ $errors->has('WeekendName') ? ' is-invalid' : '' }}">
                <label class="col-md-4 control-label" for="WeekendName">Weekend Name</label>

                <div class="col-md-6">
                  <input type="text" class="form-control" name="WeekendName" id="WeekendName" value="{{ old('WeekendName') ?: $service->WeekendName }}">
                  @if ($errors->has('WeekendName'))
                    <span class="form-text"> <strong>{{ $errors->first('WeekendName') }}</strong> </span>
                  @endif
                </div>
              </div>


              <div class="form-group{{ $errors->has('RoleName') ? ' is-invalid' : '' }}">
                <label class="col-md-4 control-label" for="RoleName">Position Served</label>

                <div class="col-md-6">
                  <input type="text" class="form-control" name="RoleName" id="RoleName" value="{{ old('RoleName') ?: $service->RoleName }}">
                  @if ($errors->has('RoleName'))
                    <span class="form-text"> <strong>{{ $errors->first('RoleName') }}</strong> </span>
                  @endif
                </div>
              </div>


              <div class="form-group row justify-content-center">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> {{ $mode === 'edit' ? 'Save' : 'Add' }}</button>
              </div>

            </form>

@if($mode === 'edit')
            <form class="form-group row justify-content-center" role="form" method="POST" action="{{ action('App\Http\Controllers\WeekendExternalController@destroy', ['id' => $service->id]) }}" onsubmit="return ConfirmDelete();">
              @csrf @method('delete')
                  <button type="submit" class="btn btn-outline-danger float-right"><i class="fal fa-btn fa-trash-o"></i> Delete</button>
            </form>
@endif
            <p class="mt-4 small">This form is used only for recording service on weekends not hosted by {{ config('site.community_acronym') }}</p>

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

