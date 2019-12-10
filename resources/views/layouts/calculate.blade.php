{{--This is really a check to see whether the person should be allowed to see the logged-in dashboard, or only the public-facing pages--}}
@can('view members')
  @include('layouts.app')
@else
  @include('layouts.public')
@endcan
