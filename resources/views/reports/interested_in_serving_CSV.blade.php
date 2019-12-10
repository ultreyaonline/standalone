Name,Email,Phone,Weekend,Served
@foreach($members as $member)
{{ $member->name }}{{ $member->community !== config('site.community_acronym') ? ' (' . preg_replace('/[^\w\s#]/', '', $member->community) . ')' : ''}},{{ $member->email }},{{ str_replace('&nbsp;', ' ', trim($member->phone)) }},{{ $member->weekend }},@foreach($member->serving_history as $h){{str_replace("'", '', $h['name'])}}: {{$h['position']}}@if($loop->remaining);@endif @endforeach

@endforeach
