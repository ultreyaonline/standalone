@if ($weekends->count())
  <select name="{{ $fieldname }}" class="{{ $class ?? '' }}">
      @foreach ($weekends as $w)
        @php($compare = ($use == 'name') ? $w->short_name : $w->id)
      <option value="{{ $use=='name' ? $w->short_name : $w->id }}"{{ $compare == $current_selected ? ' selected' : '' }}>{{ isset($nametype) && $nametype == 'short' ? $w->short_name : $w->weekend_full_name }}</option>
      @endforeach
  </select>
@endif
