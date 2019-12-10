@if ($wheels->count())
  <div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <strong>{{ (!empty($nametype) && $nametype != 'short') ? $weekend->weekend_full_name : $weekend->shortname . ' ' . $weekend->weekend_MF }}</strong>
    </button>
    <ul class="dropdown-menu">
      @foreach ($wheels as $w)
      <li class="dropdown-item"><a href="{{ empty($route) ? '/prayerwheel' : $route }}/{{$w->id}}"><strong>{{ (!empty($nametype) && $nametype != 'short') ? $w->weekend->weekend_full_name : $w->weekend->shortname . ' ' . $w->weekend->weekend_MF }}</strong></a></li>
      @endforeach
    </ul>
  </div>
@endif


