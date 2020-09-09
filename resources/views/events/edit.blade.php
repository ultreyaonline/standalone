@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">{{ $event->name }}</div>
                    <div class="card-body">
                        <form class="form-horizontal form-row" role="form" method="POST" action="{{ action('App\Http\Controllers\EventController@update', $event->id) }}">
                            @csrf @method('patch')

                            @include('events._input_fields')


                            <div class="form-group col-12">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-btn fa-save"></i> Save</button>
                            </div>
                        </form>
                        @can('delete events')
                            <form action="{{ action('App\Http\Controllers\EventController@destroy', ['event' => $event->id]) }}" role="form" method="POST" class="form-inline float-right" onsubmit="return ConfirmDelete();">
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

