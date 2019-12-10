<form action="/prayerwheel/{{ $wheel->id }}" method="POST" class="d-print-none">
  @csrf @method('patch')
  <input type="hidden" name="memberID" value="{{ $member->id }}">
  <input type="hidden" name="hour" value="{{ $timeslot }}">
  <button class="btn {{ $button_class ?? 'btn-outline-secondary' }} flex-child" id="signup-{{ $timeslot }}_{{ $member->id }}"
          title="Sign me up to pray on {{ $h['day'] }} from {{$h['hour_to']}}">@if($withIcon ?? false)<i class="fa fa-plus"></i> &nbsp;@endif{{ $button_text }}</button>
</form>
