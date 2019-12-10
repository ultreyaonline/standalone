@extends('layouts.app')

@section('title', 'Add Candidate Couple')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Add New Candidates</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('CandidateController@store') }}">
                            @csrf

                            <div class="form-group row">
                                <label class="col-md-4 control-label" for="weekend">Weekend</label>
                                <div class="col-md-6">
                                    @include('weekend.pulldown_only', ['fieldname' => 'weekend', 'use'=>'name', 'class' => 'btn-outline-primary', 'nametype' => 'short', 'current_selected' => $candidate->weekend])
                                </div>
                            </div>

                            @include('candidates._input_fields')

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
            $(document).on("change, keyup", "#m_email", function () {
                $("#m_username").val($(this).val());
            });
            $(document).on("change, keyup", "#w_email", function () {
                $("#w_username").val($(this).val());
            });
        @else
            $(document).on("change, keyup", "#m_first", function () {
                $("#m_username").val($("#m_first").val()+$("#m_last").val());
            });
            $(document).on("change, keyup", "#m_last", function () {
                $("#m_username").val($("#m_first").val()+$("#m_last").val());
            });
            $(document).on("change, keyup", "#w_first", function () {
                $("#w_username").val($("#w_first").val()+$("#w_last").val());
            });
            $(document).on("change, keyup", "#w_last", function () {
                $("#w_username").val($("#w_first").val()+$("#w_last").val());
            });
        @endif

            $(document).on("change", "#m_is_married", function () {
                $("#w_is_married").val($(this).val()).prop('checked', this.checked);
            });
            $(document).on("change", "#w_is_married", function () {
                $("#m_is_married").val($(this).val()).prop('checked', this.checked);
            });
        });
    </script>
@endsection
