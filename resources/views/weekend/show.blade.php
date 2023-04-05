@extends('layouts.app')

@section('title')
{{ empty($id) ? 'Weekends' : $weekend->weekend_full_name }}
@endsection

@section('body-class', 'weekends')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-sm-6">
        <div class="card mb-3">
          <div class="card-header"><strong>{{ $weekend->weekend_full_name }}</strong>&nbsp;
            @if(auth()->user()->can('edit weekends') || $weekend->rectorID == Auth::id() || auth()->user()->can('edit weekend photo'))
            <a href="/weekend/{{ $weekend->id }}/edit" class="badge badge-pill badge-secondary ml-3"> Edit </a>
            @endif
            <div class="float-right">@include('weekend.pulldownselectorbutton', ['nametype' => 'long'])</div>
          </div>
          <div class="card-body">
@if($weekend->start_date !== null && $weekend->end_date !== null)
            <p>{{ $weekend->start_date->toDayDateTimeString() }} &mdash; {{ $weekend->end_date->toDayDateTimeString() }}</p>
@endif
@if($weekend->rector)
            <p><strong>Rector:</strong> <a href="/members/{{ $weekend->rector->id }}">{{ $weekend->rector->name }}</a></p>
@endif

@if($weekend->visibility_flag >= \App\Enums\WeekendVisibleTo::ThemeVisible && !empty($weekend->weekend_theme))
            <p><strong>Theme: </strong>{{ $weekend->weekend_theme ?? '(Coming soon!)'}}</p>
            <p>{{$weekend->weekend_verse_reference}}<br>{{$weekend->weekend_verse_text}}</p>
    @if($weekend->banner_url)
            <a href="{{ $weekend->banner_url_original }}" data-featherlight="image">
              <img src="{{ $weekend->banner_url }}" class="img-fluid rounded mx-auto">
            </a>
    @endif
@endif
          </div>
        </div>
        @if($weekend->team_photo)
        <div class="card mb-3">
          <div class="card-body">
            <a href="{{ $weekend->team_photo_original }}" data-featherlight="image">
            <img src="{{ $weekend->team_photo }}" class="img-fluid rounded mx-auto">
            </a>
          </div>
        </div>
        @endif

@if($weekend->visibility_flag >= \App\Enums\WeekendVisibleTo::ThemeVisible)
        @unless($weekend->has_ended)
        <div class="card mb-3 d-print-none">
          <div class="card-body row">
            <div class="col-5">
              <a href="/prayerwheel/{{ $weekend->prayerwheel->id ?? $weekend->slug }}"><button class="btn btn-lg btn-success mb-2"><i class="fa fa-comment-o"></i> Prayer Wheel</button></a>
            </div>
            <div class="col-7">
              Sign up to pray for the Team and&nbsp;Candidates
            </div>
          </div>
        </div>
        @endunless
@endif

@if(($weekend->visibility_flag >= \App\Enums\WeekendVisibleTo::ThemeVisible && !empty($weekend->weekend_theme)) || auth()->user()->can('see weekend team roster', $weekend))
        <div class="card mb-3">
          <div class="card-body">
            <p><strong>Location:</strong>
              @if($location->location_url)
                <a href="{{ $location->location_url }}" target="_blank">{{ $location->location_name }}</a>
              @elseif ($location->id)
                <a href="/location/{{ $location->id }}" target="_blank">{{ $location->location_name }}</a>
              @else
                {{ $weekend->weekend_location }}
              @endif
            </p>
            <p><strong>Candidate Arrival Time:</strong> {{$weekend->candidate_arrival_time->format('g:i') . '-' . $weekend->candidate_arrival_time->addMinutes(30)->format('g:i a')}}<br>
            <strong>Sendoff Location:</strong> {!! $weekend->sendoff_location !!}</p>
            <p><strong>Serenade Arrival Time:</strong> {{$weekend->serenade_arrival_time->format('g:i a')}}<br>
              <strong>Serenade Practice Location:</strong> {{$weekend->serenade_practice_location }}</p>
            <p><strong>Closing Arrival Time:</strong> {{$weekend->closing_arrival_time->format('g:i a')}}, Starts: {{$weekend->closing_scheduled_start_time->format('g:i a')}}</p>
            @unless($weekend->has_ended)
            <hr>
            @if($weekend->emergency_poc_id)
            <p><strong>Emergency Contact:</strong> <a href="/members/{{$weekend->emergency_poc_id}}">{{ $weekend->emergency_poc_name ?: $weekend->emergency_poc->name }}</a><br>
              - <strong>Phone:</strong> <a href="tel:{{$weekend->emergency_poc_phone ?: $weekend->emergency_poc->cellphone ?: $weekend->emergency_poc->homephone }}">{{$weekend->emergency_poc_phone ?: $weekend->emergency_poc->cellphone ?: $weekend->emergency_poc->homephone }}</a><br>
              @if($weekend->emergency_poc_email || $weekend->emergency_poc->email)
              - <strong>Email:</strong> <a href="mailto:{{$weekend->emergency_poc_email ?: $weekend->emergency_poc->email}}" rel="noopener noreferrer" target="_blank">{{$weekend->emergency_poc_email ?: $weekend->emergency_poc->email}}</a>
              @endif
            </p>
            @endif
            @if($weekend->team_meetings)
              <strong>Team Meetings:</strong>
              <p>{!! nl2br(e($weekend->team_meetings)) !!}</p>
            @endif
            @endunless
            <hr>
            <p class="d-print-none">
              <strong>Sendoff Couple:</strong> {!! $weekend->sendoff_couple_name ?? 'TBD'!!}<br>
              @if($can_see_sendoff_point_of_contact_report)
              <br><a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/sendoff_drivers"><i class="fa fa-car"></i> Sendoff POC Report</a>
              @endif
              @can('add candidates')
              <br><a href="/weekend/{{ $weekend->id }}/roster/candidatecsv"><i class="fa fa-list"></i> Candidate-Website-Extract to CSV</a>
              @endcan
              @if($user_can_do_sendoff)
              <br><a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/sendoff_script"><i class="fa fa-paperclip"></i> Sendoff Script</a>
              @endif
            </p>
          </div>
        </div>

        @can('see weekend team roster', $weekend)
          <div class="card mb-3 d-print-none">
            <div class="card-body d-flex justify-content-around">
              <a href="/teamguide"><button class="btn btn-lg btn-primary m-2"><i class="fa fa-book"></i> Team Guide</button></a>
              <a href="/weekend/{{ $weekend->id }}/roster"><button class="btn btn-lg btn-primary m-2"><i class="fa fa-users"></i> Roster</button></a>
            </div>

            @if(!empty($weekend->share_1_doc_url) || !empty($weekend->share_2_doc_url) || !empty($weekend->share_3_doc_url) || !empty($weekend->share_4_doc_url) || !empty($weekend->share_5_doc_url))
              <div class="card-body">
                  Shared Documents:
                  <ul>
                @if (!empty($weekend->share_1_doc_url))
                <li><a href="{{ $weekend->share_1_doc_url }}" target="_blank" rel="noreferrer noopener" class="text-truncate">{{ $weekend->share_1_doc_label ?? $weekend->share_1_doc_url }}</a></li>
                @endif
                @if (!empty($weekend->share_2_doc_url))
                <li><a href="{{ $weekend->share_2_doc_url }}" target="_blank" rel="noreferrer noopener" class="text-truncate">{{ $weekend->share_2_doc_label ?? $weekend->share_2_doc_url }}</a></li>
                @endif
                @if (!empty($weekend->share_3_doc_url))
                <li><a href="{{ $weekend->share_3_doc_url }}" target="_blank" rel="noreferrer noopener" class="text-truncate">{{ $weekend->share_3_doc_label ?? $weekend->share_3_doc_url }}</a></li>
                @endif
                @if (!empty($weekend->share_4_doc_url))
                <li><a href="{{ $weekend->share_4_doc_url }}" target="_blank" rel="noreferrer noopener" class="text-truncate">{{ $weekend->share_4_doc_label ?? $weekend->share_4_doc_url }}</a></li>
                @endif
                @if (!empty($weekend->share_5_doc_url))
                <li><a href="{{ $weekend->share_5_doc_url }}" target="_blank" rel="noreferrer noopener" class="text-truncate">{{ $weekend->share_5_doc_label ?? $weekend->share_5_doc_url }}</a></li>
                @endif
                  </ul>
              </div>
            @endif

          </div>
        @endcan

        @if(($weekend->team_fees > 0 || $weekend->candidate_cost > 0) && Str::contains(config('site.payments_accepts_donations', ''), 'fees'))
        <div class="card mb-3">
          <div class="card-body">
            <p class="mb-0">
              <strong>Team Fees:</strong> ${{$weekend->team_fees}}
              <a class="small" href="{{ route('fees') }}"><u>(Click for Payment Options)</u></a>
              <br>
              <strong>Candidate Fees:</strong> ${{$weekend->candidate_cost}}
            </p>
            @can('see reports about team fees', $weekend)
              <div class="text-right">
              <a href="{{ route('unpaid_team_fees', $weekend->id) }}" role="button" class="btn btn-info m-2" style="color:black">
                  <i class="fa fa-usd" aria-hidden="true"></i> Unpaid Fees
              </a>
                @if($weekend->mayTrackTeamPayments || auth()->user()->can('record team fees paid'))
              <a href="{{ route('list_team_fees', $weekend->id) }}" role="button" class="btn btn-warning m-2" style="color:black">
                  <i class="fa fa-usd" aria-hidden="true"></i> Track Team Fee Payments
              </a>
                @endif
              </div>
            @endcan

          </div>
        </div>
        @endif

@unless($weekend->ended_over_a_month_ago)
  @if(auth()->user()->can('see section heads tools', $weekend) || auth()->user()->hasRole('Secretariat'))
        <div class="card mb-3 d-print-none">
          <div class="card-header">Section Head Tools</div>
          <div class="card-body">
            <a href="/team/{{ $weekend->id }}/email" class="float-left"><button class="btn btn-lg btn-primary pb-2"><i class="fa fa-bullhorn"></i> Email the Team</button></a>
          </div>
        @can('see section heads tools', $weekend)
          <hr>
          <div class="card-body">
            Candidate Data Reports:<br>
            <a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/notes">Head Cha / Candidate-Notes Report</a><br>
            <a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/diet">Dining Cha Report</a><br>
            <a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/dorm">Dorm Cha Report</a><br>
            <a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/meds">Medication Cha Report</a><br>
            <a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/numbered">Numbered List</a><br>
            <a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/palanca">Palanca Cha Report</a><br>
            <a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/prayer">Prayer Cha Report</a><br>
            <a href="/reports/{{ $weekend->weekend_number }}/{{ $weekend->gender }}/seating">Seating Planning Report</a><br>
          </div>
        @endcan
        </div>
  @endif
@endunless

@endif

      </div>

      <div class="col-sm-6">

@can('see weekend team roster', $weekend)
        <div class="card mb-3" id="statistics">
          <div class="card-header">Statistics <span class="float-right">(as of {{ $stats_date }})</span></div>
          @unless($weekend->has_started)
            <div class="card-footer small text-danger">(Subject to change until the weekend starts!)</div>
          @endunless
          <div class="card-body">
            <p>
              <span class="p-2 m-1 badge badge-success">Candidates: {{ $weekend->candidates->count() }}</span>
              <span class="p-2 m-1 badge badge-success">Team: {{ $weekend->totalteam }}</span>
              <span class="p-2 m-1 badge badge-warning">{{ config('site.community_acronym') }}: {{ $weekend->local }}</span>
              @if($diff = $weekend->totalteam - $weekend->local)
              <span class="p-2 m-1 badge badge-warning">Extended: {{ $diff }}</span>
              @endif
              <span class="p-2 m-1 badge badge-danger">Total People: {{ $weekend->totalteamandcandidates }}</span>
            </p>
              @if($weekend->team_fees > 0 && Str::contains(config('site.payments_accepts_donations', ''), 'fees'))
              <hr>
              <p><strong>Team fees:</strong>
                  <a class="" href="{{ url('/fees') }}"><span class="p-2 m-1 badge badge-primary">{{ config('site.community_acronym') }} Team Paid: {{ $stats['local_percent'] }}%</span></a>
                  @if($stats['extended_positions'] > 0)
                  <a class="" href="{{ url('/fees') }}"><span class="p-2 m-1 badge badge-primary">Extended Team Paid: {{ $stats['extended_percent'] }}%</span></a>
                  @endif
              </p>
              @endif
          </div>
        </div>

        @includeWhen($weekend->visibility_flag >= \App\Enums\WeekendVisibleTo::Community && $user->can('do system admin'), 'weekend._admin_tools')


      @unless($weekend->has_started)
        <div class="card border-info mb-4" id="palanca">
          <div class="card-header">Palanca</div>
          @if(!$weekend->has_started)
          <div class="card-footer small text-danger">(Subject to change until the weekend starts!)</div>
          @endif
          <div class="card-body">
            <p>Consider the following numbers when preparing palanca:</p>
            <ul style="list-style: none">
            @unless($weekend->has_started)
              <li><strong>Maximum Candidate Seating:</strong> {{$weekend->maximum_candidates}}</li>
            @endunless
              <li><strong>Candidates Registered{{ $weekend->has_started ? '' : ' (so far)' }}:</strong>
                <span class="{{ ! $weekend->has_started ? 'text-danger' : '' }}">{{ $weekend->candidates->count() }}</span>
              </li>
            <li><strong>Rollo Room:</strong>
              <span class="{{ ! $weekend->has_started ? 'text-danger' : '' }}">{{ $weekend->table_palanca_guideline_text ?? $weekend->totalrolloroom }}</span>
            </li>
            <li><strong>Team:</strong>
              <span class="{{ ! $weekend->has_started ? 'text-danger' : '' }}">{{ $weekend->totalteam }}</span>
            </li>
            <li><strong>Team and Candidates:</strong>
              <span class="{{ ! $weekend->has_started ? 'text-danger' : '' }}">{{ $weekend->totalteamandcandidates }}</span>
            </li>
            </ul>
          </div>
          <div class="card-footer"><a href="/palanca">Click here for more info about Palanca</a></div>
        </div>
        @endunless
@endcan

        <div class="card mb-3 border-primary" id="candidates">
        <div class="card-header">
          Candidate Names for {{ $weekend->short_name }}-{{ $weekend->weekend_MF }}
          <a href="{{ url('/sponsoring') }}"><button class="btn btn-info float-right"><i class="fa fa-user-plus"></i> Sponsor Someone</button></a>
        </div>
          @if($weekend->candidates && $weekend->candidates->count())
          @unless($weekend->has_started)
          <div class="card-footer small text-danger">(Subject to change until the weekend starts!)</div>
          @endunless
          @endif
          <div class="card-body">
            @if($weekend->candidates && $weekend->candidates->count())
              <ul style="list-style: none">
                @foreach($weekend->candidates as $c)
                  @can('edit community roster')
                  <li><a href="/members/{{$c->id}}">{{ $c->name }}</a></li>
                  @else
                  <li>{{ $c->name }}</li>
                  @endcan
                @endforeach
              </ul>
            @endif
          </div>
          <div class="card-footer small text-primary">NOTE: This list is provided for purposes of prayer and writing palanca letters!</div>
        </div>

@can('edit community roster')
    @if($weekend->sponsors && $weekend->sponsors->count())
        <div class="card mb-3 border-warning" id="sponsors">
        <div class="card-header">
          Sponsors for {{ $weekend->short_name }}-{{ $weekend->weekend_MF }}
          <a class="float-right" role="button" data-toggle="collapse" href="#collapseEmails" aria-expanded="false" aria-controls="collapseEmails">Emails</a>
        </div>
            <div id="collapseEmails" class="card-body collapse">
              <ul>
                @foreach($weekend->sponsors as $s)
                  <li>{{ $s->name }} &lt;{{ $s->email }}&gt;,</li>
                @endforeach
              </ul>
            </div>
          <div class="card-body">
              <ul>
                @foreach($weekend->sponsors as $s)
                  <li><a href="/members/{{$s->id}}">{{ $s->name }}</a></li>
                @endforeach
              </ul>
          </div>
        </div>
    @endif
@endcan

@includeWhen($weekend->teamCanBeViewedBy($user) || $user->can('use rector tools'), 'weekend._rector_tools')

{{--@can('make SD assignments')--}}
{{--        <div class="card border-warning">--}}
{{--          <div class="card-header bg-warning">Spiritual Advisor Tools</div>--}}
{{--          <div class="card-body">--}}
{{--            <a href="/team/{{ $weekend->id }}/edit"><button class="btn btn-lg btn-info"><i class="fa fa-edit"></i> Assign SDs</button></a>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--@endcan--}}

      </div>

    </div>
  </div>
@endsection

@section('extra_css')
  <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.2/release/featherlight.min.css" type="text/css" rel="stylesheet" />
@endsection

@section('page-js')
  <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.2/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
@endsection
