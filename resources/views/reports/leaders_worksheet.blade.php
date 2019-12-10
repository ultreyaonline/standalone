<title>{{config('site.community_acronym') }} Leaders Worksheet</title>
<style>
  th { vertical-align: bottom;}
  th, .left { text-align: left;}
  td, .center { text-align: center;}
</style>
<div class="container-fluid d-print-none">
  <div class="row d-print-none">
    <div class="col-12 d-print-none">
      <form action="/reports/leaders-worksheet" method="post" class="d-print-none" style="display: inline-block">
        @csrf

        <select name="mw" class="form-control" autofocus>
            <option value="M"{{ request()->input('mw', $gender) == 'M' ? ' selected' : ''}}>Men</option>
            <option value="W"{{ request()->input('mw', $gender) == 'W' ? ' selected' : ''}}>Women</option>
        </select>
        who have served
        <select name="t" class="form-control">
          @foreach(range(0,20) as $t)
            <option value="{{ $t }}"{{ request()->input('t', 3) == $t ? ' selected' : ''}}>{{ $t }} time{{ $t != 1 ? 's' : '' }} or more</option>
          @endforeach
        </select>
        in this section:
        <select name="s" class="form-control">
          <option value="0"{{ request()->input('s', 0) == 0 ? ' selected' : ''}}>(All)</option>
          @foreach($sections as $s)
            <option value="{{ $s->id }}"{{ request()->input('s', 0) == $s->id ? ' selected' : ''}}>{{ $s->name }}</option>
          @endforeach
        </select>
        <select name="r" class="form-control">
            <option value="0"{{ request()->input('r', 1) == '0' ? ' selected' : ''}}>Exclude Rector Service</option>
            <option value="1"{{ request()->input('r', 1) == '1' ? ' selected' : ''}}>Include Rector Service</option>
        </select>
        &nbsp;
        From <!-- Community -->
        <label for="c_local">{{ $default = config('site.local_community_filter', config('site.community_acronym')) }}:</label>
        <input id="c_local" type="checkbox" name="c[]" value="Local"{{ in_array('Local', request()->input('c', ['Local'])) ? ' checked' : '' }}> |
        <label for="c_other">Other:</label>
        <input id="c_other" type="checkbox" name="c[]" value="Other"{{ in_array('Other', request()->input('c', [])) ? ' checked' : '' }}>

        &nbsp;&nbsp;
        Sorted by:
        <select name="sort" class="form-control">
          @foreach(['last' => 'Name (last)', 'times' => 'Times Served', 'core' => 'Core Talks Given', 'talks' => 'All Talks Given'] as $key => $val)
          <option value="{{ $key }}"{{ request()->input('sort', 'last') == $key ? ' selected' : ''}}>{{ $val }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary"> Submit</button>
        <a href="/dashboard"><button type="button">Back to Main Menu</button></a>
      </form>
        @if ($reportData->count())
            <a style="text-align: right; margin-left: 20px;" download="{{ 'LeadersWorksheet ' . strtoupper(config('site.community_acronym')) . ' as of ' . date('Y-m-d') }}.csv" href="{{ $reportData->toInlineCsv() }}"><button class="btn btn-outline-secondary"><i class="fa fa-file-text-o"></i> CSV</button></a>
        @endif

    </div>
  </div>
</div>
<table border="1" cellpadding="0" cellspacing="0">
  <thead>
    <th>Name</th>
    <th>Weekend</th>
@if(request()->input('c', ['Local']) !== ['Local'])
    <th>Community</th>
@endif
    <th class="center">Total Service</th>
    @foreach($roles as $role)
    <th>{{ $role->ReportName }}</th>
    @endforeach
    <th>Core Talks*</th>
    <th>Basic Talks*</th>
    <th>Talks Given</th>
  </thead>
  <tbody>
  @foreach($members as $member)
    <tr>
      <td class="left"><a href="/members/{{ $member->id }}">{!! \App\Helpers\HtmlEntity::spacesNonBreaking($member->name) !!}</a></td>
      <td class="left">{!! \App\Helpers\HtmlEntity::spacesNonBreaking($member->weekend) !!}</td>
@if(request()->input('c', ['Local']) !== ['Local'])
      <td>{{ $member->community }}</td>
@endif
      <td>{{ $member->times_served }}</td>
      {{--Show counts for all non-talk roles--}}
    @foreach($roles as $role)
{{--Special rules for Table Chas--}}
        @if($taRoles->contains('id', $role->id))
          <td>
          {{ ($member->weekendAssignments->filter(function($position) use ($taRoles, $member) {
            return $taRoles->contains('id', $position->roleID);
          })->count() +
          $member->weekendAssignmentsExternal->filter(function($position) use ($taRoleNames, $member) {
            return $taRoleNames->contains($position->RoleName);
          })->count() ) ?: ' '}}
          </td>
        @else
{{--All other Chas--}}
          <td>
        {{ ( $member->weekendAssignments->filter(function($position) use ($role) {
          return $position->roleID === $role->id;
        })->count() +
        $member->weekendAssignmentsExternal->filter(function($position) use ($role) {
          return $position->RoleName === $role->RoleName;
        })->count() ) ?: ' '}}
          </td>
        @endif
    @endforeach
      {{--Show count of Core talks, for all communities--}}
      <td>
        {{ $member->core_talks_count ?: ' '}}
      </td>
      {{--Show count of basic talks, for all communities--}}
      <td>
        {{ $member->basic_talks_count ?: ' '}}
      </td>
      {{--Extract which talks have been given --}}
      <td>
        {{ implode(",\n", \array_merge($member->talks_local->toArray(), $member->talks_external->toArray())) }}
      </td>
    </tr>
    @endforeach
  </tbody>

</table>
<p>
* Table Assistant (TA) includes Silent Professor and Table Leader<br>
** Core talks include: Piety, Action, Leaders, Environment, CCIA<br>
*** Basic talks include: Ideal, Church, Study, Reunion Group, Fourth Day<br>
Rector Service roles include: Rector, Backup Rector, Rover. Also includes Special Cha<br>
</p>
