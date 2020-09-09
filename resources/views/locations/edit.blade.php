@extends('layouts.app')

@section('title', 'Edit Location')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">{{ $location->name }}</div>
                    <div class="card-body">
                        <form class="form-horizontal form-row" role="form" method="POST" action="{{ action('App\Http\Controllers\LocationController@update', $location->id) }}">
                            @csrf @method('patch')

                            @include('locations._input_fields')


                            <div class="form-group col-12">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-btn fa-save"></i> Save</button>
                            </div>
                        </form>
                        @can('delete locations')
                            <form action="{{ action('App\Http\Controllers\LocationController@destroy', ['location' => $location->id]) }}" role="form" method="POST" class="form-inline float-right" onsubmit="return ConfirmDelete();">
                                @csrf @method('delete')
                                <button type="submit" class="btn btn-danger"><i class="fa fa-btn fa-remove"></i> Delete</button>&nbsp;
                            </form>
                        @endcan

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

