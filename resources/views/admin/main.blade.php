@extends('layouts.app')

@section('title', 'Admin - ' . config('site.community_acronym'))

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-sm-6">

        @can('edit members')
        <div class="card mb-2">
          <div class="card-header card-title">Member Management</div>
          <div class="card-body">

            <p><a href="{{ route('admin.members_audit') }}">
                <button class="btn btn-lg btn-primary"><i class="fa fa-user-md" aria-hidden="true"></i> Edit Members Database</button>
              </a></p>
            <p><a href="{{ url('/inactive-members') }}">
                <button class="btn btn-lg btn-outline-info"><i class="fa fa-user-times"></i> Inactive Members</button>
              </a></p>
            <p><a href="/member/add">
                <button class="btn btn-lg btn-primary"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Member</button>
              </a></p>
{{--            <p><a href="{{ route('missing_avatars') }}">--}}
{{--                <button class="btn btn-primary"><i class="fa fa-file-photo-o" aria-hidden="true"></i> Members Without Avatar/Image</button>--}}
{{--              </a></p>--}}

          </div>
        </div>
        @endcan

        @if($user->can('do weekend administration') || $user->can('create locations') || $user->can('delete locations'))
        <div class="card mb-2">
          <div class="card-header card-title">Admin Controls</div>
          <div class="card-body">

            <p><a href="{{ route('weekend.create') }}">
                  <button class="btn btn-lg btn-primary"><i class="fa fa-plus" aria-hidden="true"></i>
                      Weekend Create
                  </button>
            </a></p>
            <p><a href="{{ route('location.index') }}">
                <button class="btn btn-lg btn-primary"><i class="fa fa-bars" aria-hidden="true"></i> Edit Locations</button>
            </a></p>
            <p><a href="{{ route('banners.index') }}">
                <button class="btn btn-lg btn-outline-info mr-2"><i class="fa fa-photo" aria-hidden="true"></i> Edit Banners</button> (General Community Banners)
            </a></p>
            {{--<p><a href="/team/import">--}}
                {{--<button class="btn btn-lg btn-primary"><i class="fa fa-upload" aria-hidden="true"></i> Weekend Import Roster</button>--}}
              {{--</a></p>--}}
            {{--<p><a href="/admin/email-how-to-sponsor">--}}
                {{--<button class="btn btn-lg btn-primary"><i class="fa fa-envelope" aria-hidden="true"></i> Send How-To-Sponsor email to whole community</button>--}}
              {{--</a></p>--}}

          </div>
        </div>
        @endif


        @can('add candidates')
        <div class="card mb-2">
          <div class="card-header card-title">Pre-Weekend Committee</div>
          <div class="card-body">

            <p><a href="/candidates">
                <button class="btn btn-lg btn-success"><i class="fa fa-cog" aria-hidden="true"></i> Manage Candidates</button>
              </a></p>

            <p><a href="/candidates/add">
            <button class="btn btn-lg btn-primary"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Candidate/Couple</button>
            </a></p>

            <p><a href="/preweekend/invitations">
                <button class="btn btn-lg btn-warning" style="color: black"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Invitation Preparation Worksheet</button>
              </a></p>
            <p><a href="/reports/sendoff-couples">
                <button class="btn btn-lg btn-secondary"><i class="fa fa-area-chart" aria-hidden="true"></i> Report: Send-off Couples - History</button>
              </a></p>

          </div>
        </div>
        @endcan


        @if(auth()->user()->can('record candidate fee payments') || auth()->user()->can('record team fees paid'))
        <div class="card mb-2">
          <div class="card-header card-title">Financial</div>
          <div class="card-body">

            @can('record candidate fee payments')
            <p><a href="/finance/candidates">
                <button class="btn btn-lg btn-primary"><i class="fa fa-usd" aria-hidden="true"></i> Candidate Payments</button>
              </a></p>
            @endcan

            @can('record team fees paid')
            <p><a href="/finance/team">
                <button class="btn btn-lg btn-primary"><i class="fa fa-usd" aria-hidden="true"></i> Team Member Payments</button>
              </a></p>
            @endcan

          </div>
        </div>
        @endif

      </div>



      <div class="col-sm-6">

        @can('do system admin')
        <div class="card mb-2">
          <div class="card-header card-title">Configuration</div>
          <div class="card-body">
            <p><a href="{{ route('admin-settings-edit') }}">
                <button class="btn btn-outline-success"><i class="fa fa-gears" aria-hidden="true"></i> Configuration Settings</button>
              </a></p>
            <p><a href="{{ route('showAssignedRoles') }}">
                <button class="btn btn-outline-secondary"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Role Assignments</button>
              </a></p>
            <form action="/admin/testemail" method="post" onsubmit="return ConfirmSend();">@csrf
                <p>
                    <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-envelope" aria-hidden="true"></i> Send Test Email to Admins</button>
                </p>
            </form>
          </div>
        </div>
  @if(false)
        <div class="card mb-2">
          <div class="card-header card-title">Site Reports</div>
          <div class="card-body">
@if(config('newsletter.apiKey'))
            <p><a href="/admin/mailchimpaudit">
                <button class="btn btn-primary"><i class="fa fa-calculator" aria-hidden="true"></i> Mailchimp Subscription Audit</button>
              </a></p>
@endif
          </div>
        </div>
  @endif
        @endcan

        @can('view activity logs')
        <div class="card mb-2">
          <div class="card-header card-title">Activity Logs</div>
          <div class="card-body">
              <a role="button" href="{{ route('activitylog') }}"><button class="btn btn-outline-primary"><i class="fa fa-signal" aria-hidden="true"></i> Site Activity</button></a>
          </div>
        </div>
        @endcan

        @if(Route::has('horizon.index'))
          @can('manage queues')
          <div class="card mb-2">
            <div class="card-header card-title">Queues</div>
            <div class="card-body pb-0">
                <a role="button" href="{{ route('horizon.index') }}" target="_blank" rel="noopener"><button class="btn btn-outline-primary"><i class="fa fa-database" aria-hidden="true"></i> Queue Status</button></a>
                <p class="small pt-2 pb-0">Queues are used for all email-sending (profile-related, prayer wheel-related, team/community blasts, etc) and for resizing uploaded images for profiles, teams and weekend visuals and banners. If emails aren't sending properly then it's possible the queues aren't running or things are failing. You can check status using the button above.</p>
            </div>
          </div>
          @endcan
        @endif

        @include('admin._support_options')

        @can('export member data')
            <div class="card mb-2">
                <div class="card-header card-title">Export Data</div>
                <div class="card-body justify-content-between">
                    <a role="button" href="{{ route('SelectMembersToExport') }}"><button class="btn btn-outline-secondary"><i class="fa fa-database" aria-hidden="true"></i> Export Members</button></a>
                </div>
            </div>
        @endcan


      </div>
    </div>
  </div>
@endsection
