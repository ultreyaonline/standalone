@if ($member->weekendAssignments->count() || $member->weekendAssignmentsExternal->count())
  <p>Service records in database:</p>
  <ul class="listBox">
    @foreach ($member->weekendAssignments as $p)
        <li><a href="{{url('/weekend/' . $p->weekend->id)}}">{{$p->weekend->weekend_full_name}}</a> - {{$p->role->RoleName}}</li>
    @endforeach
    @foreach ($member->weekendAssignmentsExternal as $p)
        <li>{{ $p->WeekendName }} - {{ $p->RoleName }} <a href="/admin/service/{{ $p->id }}/edit" class="d-print-none ml-1"><i class="fa fa-edit"></i></a></li>
    @endforeach
  </ul>
@endif
