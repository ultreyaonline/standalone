@extends('layouts.app')

@section('title', 'Edit Member Profile')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-7 offset-md-1">
                <div class="card">
                    <div class="card-header">{{$member->name}}  ({{$member->weekend}}-{{$member->gender}})</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\MembersController@update', $member->id) }}">
                            @csrf @method('patch')

                            @include('members._input_fields')

                            <hr>
                            <div class="form-group row">
                                <div class="col-12">
                                    <button type="submit" class="float-sm-right btn btn-primary"><i class="fa fa-btn fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">



                <div class="card">
                    <div class="card-header">&nbsp;</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\MembersController@updateAvatar', $member->id) }}" enctype="multipart/form-data">
                            @csrf @method('patch')

                            @include('members._avatar_field')

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>



                @can('delete members')
                <div class="card border-danger mt-4">
                    <div class="card-header alert-danger">DELETE?  (CAUTION!)</div>
                    <div class="card-body">
                        Warning: Deletion is irreversible!<br>
                        <p class="small">The member will INSTANTLY be removed from ALL weekend assignments, prayer wheels, history, etc.</p>
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\MembersController@destroy', ['memberID' => $member->id]) }}" onsubmit="return ConfirmDelete();">
                            @csrf @method('delete')
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-danger float-right"><i class="fal fa-btn fa-trash-alt"></i> Delete Member</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endcan



            </div>
        </div>
    </div>
@endsection

