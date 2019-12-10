@can('manage permissions')
<div class="card">
  <div class="card-header alert-warning">
    <div class="card-title">
      <a role="button" data-toggle="collapse" href="#collapseActivity" aria-expanded="false"
         aria-controls="collapsePermissions">
        Roles and Permissions
      </a>
    </div>
  </div>
  <div id="collapsePermissions" class="card-body collapse">
    @include('partials._data_for_roles_and_permissions')
  </div>
</div>
@endcan
