@extends('layouts.app')

@section('title', 'Edit Candidates')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Edit Candidate
                      @if(config('site.preweekend_sponsor_confirmations_enabled'))
                        @if(($candidate->m_sponsorID || $candidate->w_sponsorID) && !$candidate->sponsor_acknowledgement_sent)
                            <p class="float-right d-print-none"><a href="/candidates/{{ $weekend->number_slug }}/{{$candidate->id}}/ack">
                                    <button class="btn btn-warning" style="color:black;"><i class="fa fa-share" aria-hidden="true"></i> Email Sponsor to Verify</button>
                                </a></p>
                        @endif
                        @if(($candidate->m_sponsorID || $candidate->w_sponsorID) && $candidate->sponsor_acknowledgement_sent && !$candidate->sponsor_confirmed_details)
                            <p class="float-right clearfix d-print-none"><a href="/candidates/{{ $weekend->number_slug }}/{{$candidate->id}}/remind">
                                    <button class="btn btn-warning" style="color:black;"><i class="fa fa-share" aria-hidden="true"></i> Remind Sponsor to Verify</button>
                                </a></p>
                        @endif
                      @else
                        @if(($candidate->m_sponsorID || $candidate->w_sponsorID))
                          <p class="float-right d-print-none"><a href="/candidates/{{ $weekend->number_slug }}/{{$candidate->id}}/ack">
                              <button class="btn {{ $candidate->sponsor_acknowledgement_sent ? 'btn-primary' : 'btn-warning' }}" style="color:black;"><i class="fa fa-share" aria-hidden="true"></i> {{ $candidate->sponsor_acknowledgement_sent ? 'Re-' : '' }}Notify Sponsor</button>
                            </a></p>
                        @endif
                      @endif
                      @can('delete candidates')
                          <form action="{{ action('App\Http\Controllers\CandidateController@destroy', ['candidate' => $candidate->id]) }}" role="form" method="POST" class="form-inline float-right d-print-none" onsubmit="return ConfirmDelete();">
                              @csrf @method('delete')
                          <button type="submit" class="btn btn-danger"><i class="fa fa-btn fa-times"></i> Delete</button>&nbsp;
                          </form>
                      @endcan
                      <div class="btn-group float-right d-print-none" role="group" aria-label="Manage Candidates Button" style="padding-right: 5px">
                        <a href="/candidates"><button class="btn btn-success"><i class="fa fa-cog" aria-hidden="true"></i> Manage Candidates</button></a>
                    </div>

                    </div>
                    <div class="card-body d-print-none">
                      @if($candidate->man)
                        <form action="{{ action('App\Http\Controllers\CandidateEmailsController@sendCandidateConfirmationEmail', ['user' => $candidate->man->id]) }}" role="form" method="POST" class="form-inline float-right">
                            @csrf
                          <button class="btn btn-primary"><i class="fa fa-envelope"></i> Man - {{ $candidate->m_confirmation_email_sent ? 'RE-' : '' }}Send Welcome Email</button>
                        </form>
                        <br style="clear:both;">
                      @endif
                      @if($candidate->woman)
                        <form action="{{ action('App\Http\Controllers\CandidateEmailsController@sendCandidateConfirmationEmail', ['user' => $candidate->woman->id]) }}" role="form" method="POST" class="form-inline float-right">
                            @csrf
                          <button class="btn btn-primary"><i class="fa fa-envelope"></i> Woman - {{ $candidate->w_confirmation_email_sent ? 'RE-' : '' }}Send Welcome Email</button>
                        </form>
                      @endif
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\CandidateController@update', ['candidate' => $candidate->id] ) }}">
                            @csrf @method('patch')

                                    <div class="form-group row">
                                        <label class="col-md-4 control-label" for="weekend">Weekend</label>
                                        <div class="col-md-6">
                                            @include('weekend.pulldown_only', ['fieldname' => 'weekend', 'use'=>'name', 'class' => 'btn-secondary', 'nametype' => 'short', 'current_selected' => $candidate->weekend])
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
