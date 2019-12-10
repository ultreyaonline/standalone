  <select name="{{ $fieldname }}" class="form-control">
    @foreach ($types as $type => $name)
      <option value="{{ $type }}"{{ $type == $current ? ' selected' : '' }}>{{ $name }}</option>
    @endforeach
  </select>
