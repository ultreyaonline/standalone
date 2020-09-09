@extends('layouts.app')

@section('title', 'Import Team List')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card">
          <div class="card-header">Import Team List</div>
          <div class="card-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\TeamController@import') }}">
              @csrf

              <div class="form-group row">
                <label class="col-md-3 control-label" for="weekend">Weekend</label>
                <div class="col-md-9">
                  @include('weekend.pulldown_only', ['fieldname' => 'weekend', 'use'=>'id', 'current_selected' => $weekend->id])
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-3 control-label" for="import_data">Paste Team List </label>
                <div class="col-md-9">
                  <textarea class="form-control" name="import_data" id="import_data"
                            rows="15"
                            placeholder="Import data in FIRST,LAST,ROLENAME,EMAIL format">{{ old('import_data') }}</textarea>
                @if ($errors->has('import_data'))
                    <span class="form-text"> <strong>{{ $errors->first('import_data') }}</strong> </span>
                  @endif
                </div>
              </div>

               <div class="form-group row">
                <div class="col-md-6 offset-md-4">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-upload"></i> Import</button>
                </div>
              </div>

            </form>
            <p class="small">(Designed to import from the "<strong>Names, Positions and email (comma delimited for import)</strong>" list)</p>

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

