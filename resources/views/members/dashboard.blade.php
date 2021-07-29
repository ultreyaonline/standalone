@extends('layouts.app')

@section('title')
  {{ config('site.community_acronym') }} Members Area
@endsection

@section('content')
  <div class="container">
    <h3>{{ config('site.community_long_name') }} Members Area</h3>

    <div class="row">
      <div class="col-sm-6">

        @can('view members')
        <div class="card mb-2 d-print-none d-block d-sm-none">
          <div class="card-header card-title">Navigation</div>
          <div class="card-body">
            <p><a href="{{ url('/calendar') }}"><i class="fa fa-calendar"></i> Calendar - Upcoming Events, Secuelas</a></p>
            <p><a href="{{ url('/weekend') }}"><i class="fa fa-hotel"></i> Weekends</a></p>
            <p><a href="{{ url('/directory') }}"><i class="fa fa-users"></i> Community Directory</a></p>
          @can('menu-see-admin-pane')
            <p><a href="{{ url('/admin') }}"><i class="fa fa-cog"></i> Admin</a></p>
            @endcan
          </div>
        </div>
        @endcan

          @include('members._prayerwheel_signups')
          @includeWhen(config('site.preweekend_sponsor_confirmations_enabled'), 'members._candidate_verifications')

        @can('view members')
        <div class="card my-2">
          <div class="card-header card-title">Community</div>
          <div class="card-body">

              {{--<p><a href="{{ url('/directory') }}"><i class="fa fa-users"></i> Community Directory</a></p>--}}
              {{--<p><a href="{{ url('/calendar') }}"><i class="fa fa-calendar"></i> Calendar - Upcoming Events, Secuelas</a></p>--}}

            @can('view members')
@unless(empty(config('site.newsletter_archive_url')))
            <p class="alert alert-success" style="padding:1.5rem; border-radius: 3rem"><a href="{{ config('site.newsletter_archive_url') }}"><strong><i class="fa fa-newspaper-o"></i> Latest Newsletter Issue!</strong></a></p>
@endunless
@if(config('site.facebook_page'))
            <p><a href="{{ config('site.facebook_page') }}"><i class="fa fa-facebook"></i> {{config('site.community_acronym')}} Facebook Group</a></p>
@endif
            <p><a href="{{ url('/reuniongroups') }}"><i class="fa fa-users"></i> What are Reunion Groups?</a></p>
            <p><a href="{{ url('/secuelas') }}"><i class="fa fa-cutlery"></i> What are Secuelas?</a></p>
            <p><a href="{{ url('/vocabulary') }}"><i class="fa fa-info-circle"></i> Vocabulary of Tres Dias</a></p>
            <p><a href="{{ url('/tdi_files/Essentials (2019 Revision) in PDF format.pdf') }}"><i class="fa fa-info-circle"></i> Essentials of Tres Dias</a></p>
            @endcan

            <p><a href="{{ url('/secretariat') }}"><i class="fa fa-university"></i> Secretariat</a></p>
          </div>
        </div>
        @endcan

        @can('view members')
        <div class="card my-2">
          <div class="card-header card-title">Ways to Serve!</div>
          <div class="card-body">
            <p><a href="{{ url('/sponsoring') }}"><i class="fa fa-user-plus"></i> Sponsor Someone</a></p>
            <p><a href="{{ url('/prayerwheel') }}"><i class="fa fa-comment-o"></i> Prayer Wheel</a></p>
            <p><a href="{{ url('/palanca') }}"><i class="fa fa-gift"></i> Palanca</a></p>
            <hr>
            <p><a href="/teamguide"><i class="fa fa-book"></i> Team Guide and Cha Job Descriptions</a></p>
            <hr>
            <p>Additionally, see the various committees listed below; contact them to offer a hand!</p>
          </div>
        </div>
        @endcan

        @can('view members')
        <div class="card my-2">
          <div class="card-header card-title">Committees</div>
          <div class="card-body">
            <p><a href="{{ url('/preweekend') }}"><i class="fa fa-hourglass-start"></i> Pre-Weekend (Registration) Committee</a></p>
            <p><a href="{{ url('/postweekend') }}"><i class="fa fa-hourglass-end"></i> Post-Weekend (Secuela) Committee</a></p>
            <p><a href="{{ url('/weekendcommittee') }}"><i class="fa fa-bus"></i> Weekend (Supplies Preparation) Committee</a></p>
          </div>
        </div>
        @endcan

      </div>

      <div class="col-sm-6">

        <div class="card mb-2">
          <div class="card-header card-title">My Information</div>
          <div class="card-body">

            <p><a href="{{ url('/profile') }}"><i class="fa fa-btn fa-user"></i>My Profile</a>@can('view members') (and view weekends served, people sponsored)@endcan</p>
          </div>
        </div>


    @if($user->can('use leaders worksheet') || $user->can('use rectors tools') || $user->can('make SD assignments') || $user->can('email entire community'))
        <div class="card my-2">
         <div class="card-header card-title">Leaders Tools
          <div class="btn-group float-right" role="group" aria-label="Help Button">
            <a href="{{ url('/leaders-help') }}"><i class="fa fa-question-circle"></i> Leaders Tools Help</a>
          </div>
         </div>
          <div class="card-body">
            <p><a href="/reports/stats"><i class="fa fa-line-chart" aria-hidden="true"></i> Weekend Stats</a></p>

            @if($user->can('use leaders worksheet') || $user->can('use rectors tools'))
            <p><a href="/reports/leaders-worksheet"><i class="fa fa-list"></i> Leaders Worksheet</a></p>
            <p><a href="{{ url('/reports/interested_in_serving') }}"><i class="fa fa-tint"></i> Members Interested in Serving</a></p>
            <p><a href="{{ url('/reports/byposition') }}"><i class="fa fa-tint"></i> Service By Position</a></p>
            <p><a href="{{ url('/inactive-members') }}"><i class="fa fa-user-times"></i> Inactive Members</a></p>
            @endif

            @if($user->can('use leaders worksheet') || $user->can('use rectors tools') || $user->can('make SD assignments') || $user->can('view SD history'))
            <p><a href="{{ url('/reports/sd-history') }}"><i class="fa fa-binoculars"></i> SD Service History</a></p>
            @endif

            @can('email entire community')
            Email The Community:
            <p><a href="{{ url('/communication') }}"><i class="fa fa-bullhorn"></i> Communication / Email</a></p>
            @endcan

         </div>
       </div>
    @endif

        @can('view whosonline statistics')
        <div class="card my-2">
          <div class="card-header card-title">Currently Online...<span class="float-right">Users online: {{ count($onlineUsers) }}</span> </div>
          <div class="card-body">
            @if(count($onlineUsers))
              <ul style="list-style-type: circle">
                @foreach($onlineUsers as $u)
                  <li><a href="/members/{{ $u->id }}">{{ $u->name }}</a></li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
        @endcan


      @if(!empty(config('site.payments_accepts_donations', '')))
      @can('view members')
        <div class="card my-2">
          <div class="card-header card-title">{{ Str::title(config('site.payments_accepts_donations', 'donations')) }}</div>
          <div class="card-body">
            @if(Str::contains(config('site.payments_accepts_donations', ''), 'donations'))
                <p><a href="{{ url('/donate') }}"><i class="fa fa-usd"></i> Submit {{ Str::title(config('site.payments_accepts_donations', 'donations')) }} to {{ config('site.community_acronym') }}</a></p>
            @endif
            @if(Str::contains(config('site.payments_accepts_donations', ''), 'fees'))
                @can('record team fees paid')
                <p><a href="/finance/team">
                    <button class="btn btn-sm btn-outline-secondary"><i class="fa fa-usd" aria-hidden="true"></i> Record Team Fee Payments</button>
                  </a></p>
                @endcan
                @can('record candidate fee payments')
                  <p><a href="/finance/candidates">
                      <button class="btn btn-sm btn-outline-secondary"><i class="fa fa-usd" aria-hidden="true"></i> Record Candidate Payments</button>
                    </a></p>
                @endcan
            @endif
          </div>
        </div>
      @endcan
      @endif


      </div>


    </div>
  </div>
@endsection
