<div class="card border-warning mb-4">
  <div class="card-header bg-warning">Rector Tools</div>
  <div class="card-body">
    @if($weekend->teamCanBeViewedBy(auth()->user()))
      <a href="/team/{{ $weekend->id }}/edit"><button class="btn btn-lg btn-success m-1"><i class="fa fa-edit"></i> Edit Team Roster</button></a>
    @endif
    @if($weekend->teamCanBeViewedBy(auth()->user()))
      <a href="{{ url('/reports/interested_in_serving') }}"><button class="btn btn-lg btn-info m-1"><i class="fa fa-tint"></i> Members Interested in Serving</button></a>
      <a href="{{ url('/reports/byposition') }}"><button class="btn btn-lg btn-info m-1"><i class="fa fa-tint"></i> Service By Position</button></a>
    @endif
    @if($weekend->teamCanBeViewedBy(auth()->user()) || auth()->user()->can('use leaders worksheet') || auth()->user()->can('use rectors tools'))
      <a href="/reports/leaders-worksheet?mw={{ $weekend->weekend_MF }}"><button class="btn btn-lg btn-info m-1"><i class="fa fa-list"></i> Leaders Worksheet</button></a>
    @endif
    <a href="http://www.tresdias.org/wp-content/uploads/2017/05/Rectors-Guide-1985.pdf" target="_blank"><button class="btn btn-lg btn-info m-1"><i class="fa fa-book"></i> Rector Guide from TDI</button></a>
    <br>
    {{--<a href="/reports/persons-positions"><button class="disabled btn btn-lg btn-primary"><i class="fa fa-list"></i> Alpha with Service ("The Rector List")</button></a><br>--}}
    {{--"Rector List" (Active and part of the 'local' community)<br>--}}

    {{--<a href="/reports/service-roles"><button class="disabled btn btn-lg btn-primary"><i class="fa fa-list"></i> Service listed by position</button></a><br>--}}
    {{--(Everyone)<br>--}}

    {{--<a href="/reports/never-served"><button class="disabled btn btn-lg btn-primary"><i class="fa fa-list"></i> Never served a weekend</button></a><br>--}}
    {{--(Active and part of the 'local' community)<br>--}}

    {{--<a href="/reports/non-team/{{ $weekend->id }}"><button class="disabled btn btn-lg btn-primary"><i class="fa fa-list"></i> Not serving on this weekend</button></a><br>--}}
    {{--(Active and part of the 'local' community)<br>--}}

  </div>
</div>
