@extends('layouts.calculate')

@section('title'){{ $location->location_name }} @endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-body mb-4">
                        <h2>{{ $location->location_name }}</h2>

                        @if($location->location_name)
                            <div itemprop="location" itemscope itemtype="http://schema.org/Place">
                                @if($location->location_url)<a href="{{$location->location_url}}" target="_blank" itemprop="url">@endif
                                    {{$location->location_name}}
                                    @if($location->location_url)</a>@endif
                            </div>
                        @endif
                        @if($location->address_city || $location->address_street)
                            <div itemscope itemtype="http://schema.org/PostalAddress">
                                <a href="{{ $location->map_url_link }}" target="_blank">
                                    {{ $location->address_street ? $location->address_street . ', ' : '' }}
                                    <span itemprop="addressLocality">{{$location->address_city}}</span>{{ $location->address_city ? ', ' : '' }}<span itemprop="addressRegion">{{$location->address_province}}</span>
                                </a>
                            </div>
                        @endif
                        @if($location->contact_phone)
                            <p>{{$location->contact_phone}}</p>
                        @endif
                    </div>

                </div>


            </div>
        </div>
    </div>
@endsection
