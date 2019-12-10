@extends('layouts.app')

@section('title', 'Role Assignments - ' . config('site.community_acronym') . ' Admin')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-7 offset-md-1">

        <div class="card">
          <div class="card-header card-title">Community Role Assignments</div>
          <div class="card-body">

            @foreach($roles as $r)
              <ul class="listBox">
                <li>{{$r->name}}
                  <ul class="listBox">
                    @foreach ($r->users->sortBy('last') as $u)
                      <li>
                        @if(
                        ($r->name !== 'Admin' && $canEdit) ||
                        ($r->name === 'Admin' && $canDeleteAdmins) ||
                        ($r->name === 'Super-Admin' && $canDeleteSuperAdmins)
                        )
                          <form class="form-inline" role="form" method="POST" action="{{ route('revokeRole') }}">
                            @csrf  @method('delete')
                            <input type="hidden" name="role_id" value="{{ $r->id }}">
                            <input type="hidden" name="member_id" value="{{ $u->id }}">
                            <a href="/members/{{ $u->id }}">{{ $u->name }}</a>
                            <input type="submit" class="text-danger d-print-none" value="x" title="Revoke" style="font-size: smaller; padding:0 0 0 0;margin-left:10px;">
                          </form>
                        @else
                          <a href="/members/{{ $u->id }}">{{ $u->name }}</a>
                        @endif
                      </li>
                    @endforeach
                      @if($canEdit)
                        <form class="form-horizontal d-print-none" role="form" method="POST" action="{{ route('assignRole') }}">
                          @csrf  @method('post')
                          <input type="hidden" name="role_id" value="{{ $r->id }}">
                          <div class="form-group row">
                            <label class="col-md-5 text-right text-muted col-form-label" for="role-id-{{ $r->id }}">Add:</label>
                            <div class="col-md-4" id="input-r-{{$r->id}}">
                              @include('members._member_selector', ['fieldname' => 'member_id', 'field_id' => 'role-id-'.$r->id, 'current' => null, 'users' => $members->sortBy('first')])
                            </div>
                            <div class="col-3">
                              <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-btn fa-save"></i> Add</button>
                            </div>
                          </div>
                        </form>
                      @endif
                  </ul>
                  <hr>

                </li>
              </ul>
            @endforeach
          </div>
        </div>
      </div>

      <div class="col-md-4">

        <div class="card">
          <div class="card-header card-title">Anomalies</div>
          <div class="card-body">

            <p class="small">Users WITHOUT "Member" role are listed below. Normally ONLY candidates should show here, until their Weekend is completed and they are converted to Pescadores. Others are likely Pending Moderator Review and should be converted to Pescadores so they can login. Otherwise they should be converted and then Unsubscribed and marked Inactive.</p>
            <ul class="listBox">
              @if($nonmembers->count())
              @foreach($nonmembers as $u)
                <li><a href="/members/{{ $u->id }}">{{ $u->name }}</a> ({{ $u->weekend }})</li>
              @endforeach
              @else
                <li>None found.</li>
              @endif
            </ul>
          </div>
        </div>


      </div>
    </div>
  </div>
@endsection
