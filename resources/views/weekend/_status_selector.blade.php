<div class="form-group row{{ $errors->has('confirmed') ? ' is-invalid' : '' }}">
    <label class="col-md-2 offset-md-1 col-form-label" for="confirmed">Status:</label>
    <div class="col-md-8 mb-2">
        <select class="form-control" name="confirmed" id="confirmed">
            @foreach($statuses as $status_number => $status_text)
                <option value="{{ $status_number }}" {{ $assignment->confirmed === $status_number ? 'selected' : '' }}>{{ $status_text }}</option>
            @endforeach
        </select>
        <p class="small">
            <em>Note: only Accepted-Confirmed will show on published Team Roster.
                All others only show to Leaders team, via the Team Edit page.</em>
        </p>
    </div>
</div>
