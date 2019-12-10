<form action="/prayerwheel/{{ $wheel->id }}" method="POST" onchange="this.submit()" class="d-print-none form-inline">
  @csrf @method('patch')
  @include('members._member_selector', ['fieldname'=>'memberID', 'current'=> $current, 'class' => 'pw-selector ' . ($current ? 'btn-success': $signupButtonClass)])
  <input type="hidden" name="hour" value="{{ $timeslot }}">
  <input type="hidden" name="old" value="{{ $current }}">
</form>
@if($canSeePrayerWheelNames)
  <span class="d-print-inline d-none" id="spot-{{ $timeslot }}-{{ $current }}">{{ \App\User::firstOrNew(['id' => $current], ['first'=>'', 'last'=> ''])->name }}</span>
@endif
