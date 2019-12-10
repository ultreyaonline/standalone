@can('menu-see-admin-pane')
  <div class="card border-warning mb-2">
    <div class="card-header card-title alert-warning">Admin
        <div class="small float-right" style="text-align: right">
            <em>Last login date: {{ $member->last_login_at ?? 'Never'}}
                @if($member->updated_by && $member->updatedBy)
                    <br>Last updated by: {{ $member->updatedBy->name}}
                @endif
            </em>
        </div>
    </div>
    <div class="card-body">

      @if(! $member->hasRole('Member'))
        <p><a href="{{ url('/reminders/c/' . $member->id) }}">
          <button class="btn btn-outline-secondary"><i class="fa fa-suitcase"></i> Email Candidate Packing List</button>
        </a></p>
        <form action="/convert/c/p/{{ $member->id }}" method="POST" onsubmit="return ConfirmAction();">
          @csrf
          <button class="btn btn-outline-success"><i class="fa fa-user-md"></i> Convert to Pescadore</button>
        </form>
        <hr>
      @endif
@if(config('newsletter.apiKey'))
      @if($member->inMailchimp === true)
        @can('mailchimp admin')
          <form action="/members/{{ $member->id }}/mailchimp/unsubscribe" method="POST">
            @csrf @method('delete')
            <button class="btn btn-sm btn-secondary"><i class="fa fa-user-minus"></i> Unsubscribe from Mailchimp</button>
          </form>
          <form action="/members/{{ $member->id }}/mailchimp/archive" method="POST">
            @csrf @method('delete')
            <button class="btn btn-sm btn-secondary"><i class="fa fa-user-minus"></i> Remove/Archive from Mailchimp</button>
          </form>
        @endcan
      @else
        <form action="/members/{{ $member->id }}/mailchimp/add" method="POST">
          @csrf @method('put')
          <button class="btn btn-sm btn-secondary"><i class="fa fa-user-plus"></i> Add to Mailchimp</button>
        </form>
      @endif
      <hr>
@endif
      @if($member->hasRole('Member'))
        <form action="{{ route('website_access_reminder', $member->id) }}" method="POST">
          @csrf
          <button class="btn btn-outline-secondary"><i class="fa fa-globe"></i> Send Website Login/Password Instructions by email</button>
        </form>
        <hr>
      @endif
      {{--@can('see coming soon')--}}
      {{--<span class="text-danger">These features are coming soon:</span><br>--}}
      {{--<a href="{{ url('/admin') }}"><button class="btn btn-disabled"><i class="fa fa-user-secret"></i> Send Password Reset</button></a><br>--}}
      {{--<hr>--}}
      {{--@endcan--}}


    @if(auth()->user()->id != $member->id && auth()->user()->can('demote members') && !$member->hasRole('Admin'))
        @if($member->hasRole('Member'))
            <form action="/demote/{{ $member->id }}" method="POST" onsubmit="return ConfirmAction();">
                @csrf
                <button class="btn btn-outline-secondary btn-sm small"><i class="fa fa-user-times"></i> Demote Member to no-access.</button>
                <p class="small">Demoting means they will only be able to login and see/edit their own profile, but not access any community information. They will be treated as inactive. To reverse it you will need to Convert To Pescadore again.</p>
            </form>
        @endif
    @endif

    </div>

    @can('manage permissions')
      <div class="card-header mt-2">
        <div class="card-title">
          <a role="button" data-toggle="collapse" href="#collapsePermissions" aria-expanded="false"
             aria-controls="collapsePermissions">
            Roles and Permissions <i class="fa fa-arrow-down"></i>
          </a>
        </div>
      </div>
      <div id="collapsePermissions" class="card-body collapse">
        @include('partials._data_for_roles_and_permissions')
      </div>
    @endcan
  </div>
@endcan
