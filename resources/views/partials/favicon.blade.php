<?php $favicon_file = strtolower(config('site.community_acronym', 'notfound') . '/favicon.png'); ?>
@if(file_exists(public_path($favicon_file)))
<link rel="icon" type="image/png" href="/{{ $favicon_file }}">
@endif
