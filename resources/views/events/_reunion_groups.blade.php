<div class="card">
  <div class="card-header card-title">Reunion Groups
    @can('create reunion groups')
      <div class="btn-group float-right" role="group" aria-label="Add Button">
        <a href="{{url('/events/create?type=reunion')}}"><button type="button" class="btn btn-secondary btn-primary"><i class="fa fa-btn fa-plus"></i>Add Group</button></a>
      </div>
    @endcan
  </div>

    @if ($reuniongroups->count())
      @foreach ($reuniongroups as $event)
        @include('events._display_event')
      @endforeach
    @else
        <div class="card-body">
          <p>Sorry, no Reunion Groups found.</p>
          <p>Please check back for updates soon!</p>
        </div>
    @endif

</div>
