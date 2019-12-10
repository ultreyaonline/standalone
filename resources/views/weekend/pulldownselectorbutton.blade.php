@if ($weekends->count())
  <div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <strong>{{ (!empty($nametype) && $nametype != 'short') ? $weekend->weekend_full_name : $weekend->shortname . ' ' . $weekend->weekend_MF }}</strong>
    </button>
    <ul class="dropdown-menu">
      @foreach ($weekends as $w)
      <li class="dropdown-item"><a href="{{ empty($route) ? '/weekend' : $route }}/{{$w->id}}"><strong>{{ (!empty($nametype) && $nametype != 'short') ? $w->weekend_full_name : $w->shortname . ' ' . $w->weekend_MF }}</strong></a></li>
        @if($w->id == $weekend->id)
          <li role="separator" class="dropdown-divider"></li>
        @endif
      @endforeach
    </ul>
  </div>
@endif


