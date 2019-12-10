@extends ('layouts.calculate')

@section('title', config('site.community_acronym') . ' Calendar')

@section('body-class', 'calendar')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-12 col-lg-8 offset-lg-2 mb-3">

          <div class="card">
          <div class="card-header card-title">Upcoming {{ config('site.community_acronym') }} Events
            @can('create events')
              <div class="btn-group float-right" role="group" aria-label="Add Button">
                <a href="{{url('/events/create')}}"><button type="button" class="btn btn-secondary btn-primary"><i class="fa fa-btn fa-plus"></i>Add</button></a>
              </div>
            @endcan
              <p class="small">Here are some upcoming events for the {{ config('site.community_acronym') }} community.<br>Check back often to see up-to-date events and details for weekends and secuelas.</p>

          </div>

            @if ($events->count())
              @foreach ($events as $event)
                @include('events._display_event')
              @endforeach
            @else
              <div class="card-body">
                <p>Sorry, no upcoming events found.</p>
                <p>Please check back for updates soon!</p>
              </div>
            @endif

        </div>
      </div>

      @if($reuniongroups->count() || optional($user)->can('create reunion groups'))
      <div class="col-12 col-lg-8 offset-lg-2 mb-3">
        @include('events._reunion_groups')
      </div>
      @endif

    </div>
  </div>

@endsection
