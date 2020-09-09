@extends('layouts.app')

@section('title', 'Add Member')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">Add New Candidate or Community Member</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\MembersController@store') }}">
                            @csrf


                        @if($member->id !== null)
                            <div class="form-group row">
                                <label class="col-md-4 control-label" for="weekend">Weekend</label>
                                <div class="col-md-6">
                                    @include('weekend.pulldown_only', ['fieldname' => 'weekend', 'use'=>'name', 'current_selected' => $weekend->id])
                                </div>
                            </div>
                        @endif

                            @include('members._input_fields')

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script>
        $(document).ready(function()
        {
        @if(config('site.members_username_default') === 'Email Address')
            $(document).on("change, keyup", "#email", function () {
                $("#username").val($(this).val());
            });
        @else
            $(document).on("change, keyup", "#first", function () {
                $("#username").val($("#first").val()+$("#last").val());
            });
            $(document).on("change, keyup", "#last", function () {
                $("#username").val($("#first").val()+$("#last").val());
            });
        @endif
        });
    </script>
@endsection
