Row,Position,Name,Email,Weekend,Church,Phone
@foreach($weekend->team_and_candidates as $p)
{{ $loop->iteration }},{{ $p['role'] }},{{ $p['name'] }},{{ $p['email'] }},{{ $p['weekend'] }},{{ $p['church'] }},{{ $p['phone'] }}
@endforeach
