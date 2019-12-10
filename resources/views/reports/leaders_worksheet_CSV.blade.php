@extends('layouts.app')

@section('title')
  Leaders Worksheet
@endsection


@section('content')
  <p>You may copy/paste the following content into a text file (ie: leaders-worksheet.csv), and then use File->Import in Excel to load in the data as columns.</p>
  <pre>
Name,Weekend,Community,Total Service,@foreach($roles as $role){{ $role->ReportName }},@endforeach,Core Talks,Basic Talks,Talks Given
@foreach($members as $member)
{!! \App\Helpers\HtmlEntity::spacesNonBreaking($member->name) !!},{!! \App\Helpers\HtmlEntity::spacesNonBreaking($member->weekend) !!},{{ $member->community }},{{ $member->times_served }},@foreach($roles as $role)@if($taRoles->contains('id', $role->id))
{{ $member->weekendAssignments->filter(function($position) use ($taRoles, $member) {
    return $taRoles->contains('id', $position->roleID);
  })->count() +
  $member->weekendAssignmentsExternal->filter(function($position) use ($taRoleNames, $member) {
    return $taRoleNames->contains($position->RoleName);
  })->count() }}@else{{ $member->weekendAssignments->filter(function($position) use ($role) {
  return $position->roleID == $role->id;
})->count() +
$member->weekendAssignmentsExternal->filter(function($position) use ($role) {
  return $position->RoleName == $role->RoleName;
})->count() }}@endif,@endforeach{{ $member->core_talks_count }},{{ $member->basic_talks_count }},"{{ implode(", ", \array_merge($member->talks_local->toArray(), $member->talks_external->toArray())) }}"
@endforeach
</pre>

<p>Printed on: {{ $today }}</p>
@endsection

