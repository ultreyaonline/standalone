@extends('layouts.app')
@section('title')
Edit Weekend: {{ $weekend->weekend_full_name }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <?php $width2 = 8; ?>
            @if(auth()->user()->can('edit weekends') || $weekend->rectorID == Auth::id())
                <?php $width2 = 5; ?>
            <div class="col-md-7">
                <div class="card border-primary">
                    <div class="card-header alert-primary">Editing Weekend: {{$weekend->weekend_full_name}}</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\WeekendController@update', $weekend->id) }}">
                            @csrf @method('patch')

                            @include('weekend._create-edit', ['submitButtonText' => 'Save Weekend'])

                        </form>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->can('edit weekend photo') || $weekend->rectorID == Auth::id())
            <div class="col-md-{{$width2}}">
                @if($weekend->rectorID == Auth::id() || auth()->user()->hasRole('Admin'))
                <div class="card border-primary">
                    <div class="card-header alert-primary">Theme/Visual/Banner Image</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\WeekendController@updateBannerPhoto', $weekend->id) }}" enctype="multipart/form-data">
                            @csrf @method('patch')

                            @include('weekend._banner_image_field')

                            <div class="form-group row d-inline">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> Save Photo</button>
                                </div>
                            </div>
                        </form>
                        @if ($weekend->banner_url)
                        <div class="text-center">
                            <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\WeekendController@deleteBannerPhoto', $weekend->id) }}" onsubmit="return ConfirmDelete();">
                                @csrf @method('delete')
                                <div class="form-group row d-inline">
                                    <button type="submit" class="btn alert-danger"><i class="fa fa-btn fa-trash"></i> Delete Photo</button>
                                </div>
                            </form>
                        </div>
                        @endif

                    </div>
                </div>
                @endif

                @can('edit weekend photo')
                <div class="card border-success mt-4">
                    @unless(auth()->user()->can('edit weekends') || $weekend->rectorID == Auth::id())
                    <div class="card-header alert-success">Weekend: {{$weekend->weekend_full_name}}</div>
                    @endunless
                    <div class="card-header alert-success">Team Photo (taken on the weekend)</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\WeekendController@updateTeamPhoto', $weekend->id) }}" enctype="multipart/form-data">
                            @csrf @method('patch')

                            @include('weekend._teamphoto_field')

                            <div class="form-group row d-inline">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary m-2"><i class="fa fa-btn fa-save"></i> Save Photo</button>
                                </div>
                            </div>
                        </form>
                        @if ($weekend->team_photo)
                            <div class="text-center">
                                <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\WeekendController@deleteTeamPhoto', $weekend->id) }}" onsubmit="return ConfirmDelete();">
                                    @csrf @method('delete')
                                    <div class="form-group row d-inline">
                                        <button type="submit" class="btn alert-danger m-2"><i class="fa fa-btn fa-trash"></i> Delete Photo</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                @endcan


            </div>
            @endif
        </div>
    </div>
@endsection
