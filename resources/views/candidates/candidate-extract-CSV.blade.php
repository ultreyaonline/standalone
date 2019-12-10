@extends('layouts.app')

@section('title')
Candidate Website Information: {{ $weekend->weekend_full_name }}
@endsection

@section('body-class','weekend_roster')

@section('content')
  <p>You may copy/paste the following content into a text file. (ie: '{{ config('site.community_acronym') }}-candidate-extract.csv') And then use File->Import in Excel to load in the data as columns.
    Or click to <a href="{{url(request()->getUri())}}-download">Download {{ $weekend->number_slug . strtolower($weekend->gender) }}-candidate-extract.csv</a>
  </p>
<pre>
Row,First,Last,Address1,City,State,Postal,MobilePhone,HomePhone,Email,Church,Sponsor,Spouse
@foreach($weekend->candidates as $c)
{{ $loop->iteration }},{{ $c->first }},{{ $c->last }},{{ str_replace(',','',$c->address1) }},{{ $c->city }},{{ $c->state }},{{ $c->postalcode }},{{ $c->cellphone }},{{ $c->homephone }},{{ $c->email }},{{ str_replace(',','',$c->church) }},{{ $c->sponsor }},{{ $c->spousename }}
@endforeach
</pre>

<p>Generated on: {{ $today }}</p>
@endsection
