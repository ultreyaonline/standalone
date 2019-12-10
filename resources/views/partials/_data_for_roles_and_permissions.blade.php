@can('manage permissions')

    @if ($member->roles->count())
      <p>Assigned roles:</p>
      <ul class="listBox">
        @foreach ($member->roles as $r)
          <li>{{ $r->name }}
            @if ($r->permissions->count())
              <ul class="listBox">
                @foreach ($r->permissions as $p)
                  <li>{{$p->name}}</li>
                @endforeach
              </ul>
            @endif
          </li>
        @endforeach
      </ul>
    @endif
    @if ($member->permissions->count())
      <p>Assigned permissions:</p>
      <ul class="listBox">
        @foreach ($member->permissions as $p)
          <li>{{$p->name}}</li>
        @endforeach
      </ul>
    @endif

@endcan
