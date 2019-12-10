<form action="/prayerwheel/{{ $wheel->id }}" method="POST" class="d-print-none form-inline" onsubmit="return ConfirmDelete();">
  @csrf @method('delete')
  <input type="hidden" name="memberID" value="{{ $current }}">
  <input type="hidden" name="hour" value="{{ $timeslot}}">
  <button class="btn btn-sm btn-outline-danger" id="delete-{{ $timeslot }}_{{ $current }}" title="Delete"><i class="fa fa-trash fa-lg alert-danger"></i></button>
</form>
