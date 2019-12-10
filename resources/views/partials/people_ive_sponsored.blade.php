@if ($member->sponsorees->count())
  <p>People {{ ($member->spouseID > 0) ? 'We' : 'I' }}'ve Sponsored:</p>
  <ul class="listBox">
    @foreach ($member->sponsorees as $person)
      <li><a href="{{url('/members/' . $person->id)}}">{{$person->name}}</a> - {{$person->weekend}}</li>
    @endforeach
  </ul>

@else
  <p class="">{{ ($member->id == Auth::user()->id) ? 'Click the button above to learn more about sponsoring someone.' : 'Has not sponsored anybody yet.' }}</p>
@endif
