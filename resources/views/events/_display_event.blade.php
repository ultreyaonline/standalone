@if($event->should_display_for_this_user)

<div class="card-body" style="{{ $event->is_historical || !$event->is_enabled ? 'background-color: lightgrey' : '' }}">
  <div itemscope itemtype="http://schema.org/Event">
    <p class="float-right">
      @if($event->id)
        @can('edit events')
        <a href="{{ url('/events/' . $event->edit_id . '/edit') }}"><span class="badge badge-pill badge-dark">Edit</span></a>
        @endcan
      @endif
      @if(!$event->id && $event->type === 'weekend')
        @can('edit weekends')
        <a href="{{ url('/weekend/' . str_replace('w', '', $event->edit_id) . '/edit') }}"><span class="badge badge-pill badge-dark">Edit</span></a>
        @endcan
      @endif
    </p>
    <h3 itemprop="name">{{ $event->name }}</h3>
    <div class="row">
      <span class="col-9">
          {{ $event->short_date_range_with_time }}
      </span>
    <span class="col-3 text-right d-print-none">
    @role('Member')
    <a href="/events/{{ $event->edit_id }}/ical" rel="nofollow" title="Add to Calendar"><i class="fa fa-calendar-plus-o fa-2x" aria-hidden="true" title="Add to Calendar"></i></a>
    @else
      @if($event->type === 'weekend')
    <a class="btn btn-outline-primary" role="button" href=" {{ config('site.candidate_application_url') }}" target="_blank" rel="nofollow">Apply to Attend</a>
      @endif
    @endrole
    </span>
    </div>
    @if($event->location_name)
    <div itemprop="location" itemscope itemtype="http://schema.org/Place">
      @if($event->location_url)<a href="{{$event->location_url}}" target="_blank" itemprop="url">@endif
        {{$event->location_name}}
        @if($event->location_url)</a>@endif
    </div>
    @endif
    @if($event->address_city || $event->address_street)
    <div itemscope itemtype="http://schema.org/PostalAddress">
      <a href="{{ $event->map_link }}" target="_blank">
        {{ $event->address_street ? $event->address_street . ', ' : '' }}
        <span itemprop="addressLocality">{{$event->address_city}}</span>{{ $event->address_city ? ', ' : '' }}<span itemprop="addressRegion">{{$event->address_province}}</span>
      </a>
    </div>
    @endif
    @if($event->description)
      <p>{{$event->description}}</p>
    @endif
  </div>
</div>

  @if(!empty($loop) && $loop->remaining)
    <hr>
  @endif

@endif
