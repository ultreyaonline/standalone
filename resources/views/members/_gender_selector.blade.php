  <select name="{{ $fieldname ?? 'gender'}}" class="form-control flex-child{{ !empty($class) ? ' ' . $class : '' }}"{{ !empty($autofocus) ? ' autofocus' : '' }} id="{{ $field_id ?? $fieldname }}">
      <option value="M" {{ ($current ?? null) === 'M' ? ' selected' : '' }}>{{ isset($mode) && $mode === 'plural' ? "Men's" : 'Man' }}</option>
      <option value="W" {{ ($current ?? null) === 'W' ? ' selected' : '' }}>{{ isset($mode) && $mode === 'plural' ? "Women's" : 'Woman' }}</option>
  </select>
