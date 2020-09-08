<table class="table table-hover table-sm">
  <thead class="d-print-none">
  <th class="text-center">Time</th>
  <th>Person</th>
  </thead>
  <tbody>

  {{--We loop through the "spots" here, so that we primarily display all hours--}}
  @foreach($spots as $h)
<?php
$isNewRow = true;
$timeslot = $h['position'];
$signups  = $wheel->signups->where('timeslot', $h['position'])->pluck('memberID');
$count    = $signups->count();
$isEmpty  = $count === 0;
if ($isEmpty) $signups->push(0);
$filtered_signups = $signups;
// sort signups, sticking current member on top
// this also filters to not display multiple rows for a timeslot if this person has nothing to see
// related to those rows (ie: not self or can't see all names)
$containsMe = $signups->contains($member->id);
if (! $canSeeAllNames) {
    if ($containsMe) {
        // put only my own name in the list
        $filtered_signups = $signups->intersect($member->id);//->push($member->id);
    } else {
        $filtered_signups = $signups->take(1);
    }
}
$signupButtonClass = 'btn-sm btn-outline-secondary';
if ($countPositionsRemaining > 0 && $countPositionsRemaining < 15) $signupButtonClass = 'btn-danger strong';
?>
{{--Here, within each spot, we handle each signup --}}
    @foreach ($filtered_signups as $current)
<?php
        $isMe = (int)$current === $member->id;
?>
          <tr>
            <td>
@if($isNewRow)
                <span class="d-print-none d-sm-none">{{ $h['day'] }} {!! str_replace('-', '&#8209;', e($h['hour_to'])) !!}</span><span class="d-none d-sm-block d-print-inline">{{ $h['hour'] }}</span>
@endif
            </td>
            <td class="" id="position-{{ $timeslot }}">

@can('edit prayer wheel')
    {{--admin can use dropdown to assign people --}}
    @include('prayerwheel._pulldown')

    @unless($isEmpty)
      {{-- allow delete --}}
      @include('prayerwheel._delete')
    @endunless
@else


    @if($isEmpty)
      {{--Empty, so display signup button --}}
      @if($weekend->has_ended && ! $canEditPrayerWheel)
                    {{ __('Vacant') }}
      @else
        @include('prayerwheel._button', ['button_text' => __('Sign Me Up!'), 'button_class' => $signupButtonClass, 'withIcon' => true])
      @endif


    @else
      {{-- not empty, so display details or allow more signups--}}
      @if($canSeePrayerWheelNames || $isMe)
        @if($count > 1 && !$canSeePrayerWheelNames)
          <span class="d-print-none">{{ __('Filled') }} {{ $count > 1 ? '(x'.$count.')' : '' }}</span> &nbsp;
        @endif
        @if($isMe)<strong @can('see prayer wheel names')class="normalprint" @endcan>@endif
          <span id="spot-{{ $timeslot }}-{{ $current }}">{{ \App\Models\User::firstOrNew(['id' => $current])->name }}</span>
        @if($isMe)</strong>@endif

        {{-- allow delete --}}
        @if($canEditPrayerWheel || ($isMe && ! $weekend->has_ended))
          &nbsp; @include('prayerwheel._delete')
        @endif

        @if(! $containsMe && $allowDoublingUp && $isNewRow)
        &nbsp;  @include('prayerwheel._button', ['button_text' => __('Add Me Too!')])
        @endif

      @else
        @if($allowDoublingUp && ! $containsMe)
            <span class="d-print-none">{{ __('Filled') }} {{ $count > 1 ? '(x'.$count.')' : '' }}</span>
            &nbsp; @include('prayerwheel._button', ['button_text' => __('Add Me Too!')])
        @else
          @unless($current === 0)
            @if(! ($allowDoublingUp && $isNewRow))
            <span style="color: gray" id="viewPosition-{{ $timeslot }}" class="d-print-none">{{ __('This spot is taken!') }} {{ $count > 1 ? '('.$count.')' : '' }}</span>
            @endif
          @endunless
        @endif

      @endif
    @endif
@endcan

            </td>
          </tr>
<?php $isNewRow = false; ?>
      @endforeach
  @endforeach
  </tbody>
</table>
