  <select name="{{ $fieldname }}" class="form-control"{{ !empty($autofocus) ? ' autofocus' : '' }}>
    @foreach ($roles as $r)
      <option value="{{ $r->id }}"{{ $r->id == $current ? ' selected' : '' }}>{{ $r->RoleName }}</option>
    @endforeach
  </select>
