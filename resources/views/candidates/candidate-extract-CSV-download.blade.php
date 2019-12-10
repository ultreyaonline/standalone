Row,First,Last,Address1,City,State,Postal,MobilePhone,HomePhone,Email,Church,Sponsor,Spouse
@foreach($weekend->candidates as $c)
{{ $loop->iteration }},{{ $c->first }},{{ $c->last }},{{ str_replace(',','',$c->address1) }},{{ $c->city }},{{ $c->state }},{{ $c->postalcode }},{{ $c->cellphone }},{{ $c->homephone }},{{ $c->email }},{{ str_replace(',','',$c->church) }},{{ $c->sponsor }},{{ $c->spousename }}
@endforeach
