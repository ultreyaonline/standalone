@extends('layouts.app')

@section('title')
Member Profile: {{ $member->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header card-title"><strong style="font-size: larger">{{$member->name}}  ({{$member->weekend}}-{{$member->gender}})</strong>
                        @if(Auth::user()->canEditUser($member->id))
                        <div class="btn-group float-right d-print-none" role="group" aria-label="Edit Button">
                            <a href="{{url('/members/'.$member->id.'/edit')}}"><button type="button" class="btn btn-primary"><i class="fa fa-btn fa-edit"></i>Edit</button></a>
                        </div>
                        @endif
                        @canImpersonate
                          @canBeImpersonated($member)
                        <div class="btn-group float-right d-print-none" role="group" aria-label="Impersonate">
                            <a href="{{ route('impersonate', $member->id) }}"><button type="button" class="btn btn-success text-center"><i class="fa fa-user-circle fa-lg" title="Impersonate this user"></i></button></a>&nbsp;
                        </div>
                          @endCanBeImpersonated
                        @endCanImpersonate
                        @if($member->isOnline())
                        <div class="btn-group float-right d-print-none" role="group" aria-label="Online">
                            <i class="fa fa-chain fa-btn btn" title="Online" style="color:darkorange"></i>
                        </div>
                        @endif
                        {{--@if($user->id == $member->id))--}}
                            {{--<div class="btn-group float-right d-print-none" role="group" aria-label="Reset Password Button">--}}
                                {{--<a href="{{url('/password/reset')}}"><button type="button" class="btn btn-secondary"><i class="fa fa-btn fa-lock"></i></button></a>--}}
                            {{--</div>--}}
                        {{--@endif--}}
                    </div>

                    <div class="card-body">
                    <p><strong>Email:</strong> @if($member->email)<a href="mailto:{{$member->email}}" rel="noopener noreferrer" target="_blank">{{$member->email}}</a>@else Use phone.@endif</p>
                        <p><strong>Weekend:</strong> {{$member->weekend}} - {{$member->gender}}</p>

                        @if($member->allow_address_share || $member->id === auth()->id())
                        @if($member->address1)
                        <p class="float-right d-print-none"><a href="{{ $member->map_link }}" rel="nofollow" title="Map" target="_blank"><i class="fa fa-map-signs fa-lg{{ $member->allow_address_share ? '' : ' text-muted' }}" aria-hidden="true" title="Map"></i></a></p>
                        @endif
                        <address class="vcard{{ $member->allow_address_share ? '' : ' text-muted' }}" itemscope itemtype="http://schema.org/ContactPoint">
                          <span class="adr" itemscope itemtype="http://schema.org/PostalAddress">
                            <span class="street-address" itemprop="streetAddress">{{$member->address1}}<br>
                            {{$member->address2 ?: ''}}@if($member->address2)<br>@endif</span>
                            <span class="locality" itemprop="addressLocality">{{$member->city}}</span>{{ $member->city ? ', ' : '' }}<span class="region" itemprop="addressRegion">{{$member->state}}</span><br>
                            <span class="postal-code" itemprop="postalCode">{{$member->postalcode}}</span>
                            @if($member->country)
                            <span class="country" itemprop="addressCountry">{{$member->country}}</span>
                            @endif
                          </span>
                        </address>
                        @endif
                        @if ($member->spouse)
                        <p><strong>Spouse:</strong> <a href="{{url('/members/' . $member->spouseID)}}">{{$member->spousename}}</a></p>
                        @endif
                        @if ($member->cellphone)
                        <p><strong>Mobile:</strong> <a href="tel:{{$member->cellphone}}">{{$member->cellphone}}</a></p>
                        @endif
                        @if ($member->homephone)
                        <p><strong>Home:</strong> <a href="tel:{{$member->homephone}}">{{$member->homephone}}</a></p>
                        @endif
                        @if ($member->workphone)
                        <p><strong>Work: </strong><a href="tel:{{$member->workphone}}">{{$member->workphone}}</a></p>
                        @endif

                        @if($member->church)
                        <p><strong>Church:</strong> {{$member->church}}</p>
                        @endif

@if($member->hasRole('Member') && auth()->user()->hasrole('Member'))
                        <p><strong>Interested in serving on weekends?</strong> {{$member->interested_in_serving ? 'Yes': 'No'}}</p>

                        @if($member->skills)
                        <p><strong>Skills list:</strong> {{$member->skills}}</p>
                        @endif

                        @if($member->qualified_sd)
                        <p><strong>Qualified SD?</strong> {{$member->qualified_sd ? 'Yes': 'No'}}</p>
                        @endif

                        @if (! $member->active)
                        <p class="text-danger"><strong>Inactive: </strong> {{ $member->inactive_comments }}</p>
                        @endif
@endif

                    @if ($member->sponsor)
                        @if($member->sponsorID && $user->can('view members'))
                        <p><strong>Sponsor:</strong> <a href="{{url('/members/' . $member->sponsorID)}}">{{$member->sponsor}}</a></p>
                        @else
                        <p><strong>Sponsor:</strong> {{$member->sponsor}}</p>
                        @endif
                    @endif


                        <div class="container">
                            <div class="col-12">
                                <img src="{{ $img = $member->avatar }}" alt="{{ Str::contains($img, 'gravatar') ? '' : 'profile photo'}}" title="{{ Str::contains($img, 'gravatar') ? '' : $member->name }}" class="img-fluid rounded">
                            </div>
                        </div>

                    </div>
                </div>

            </div>


        @can('view members')
            <div class="col-sm-6">


            @includeWhen($member->id === auth()->user()->id || $member->id === auth()->user()->spouseID || Auth::user()->canEditUser($member->id), 'members._prayerwheel_signups')

            @include('members._user_sidebar_for_administrators')


            @if($member->hasRole('Member'))
                <div class="card">
                    <div class="card-header card-title">Sponsoring<div class="btn-group float-right" role="group" aria-label="Sponsor Button">
                            <a class="d-print-none" href="{{ url('/sponsoring') }}"><button class="btn btn-primary"><i class="fa fa-user-plus"></i> Sponsor Someone</button></a>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('partials.people_ive_sponsored')
                    </div>
                </div>
{{--@TODO - add another filter that lets sponsors see the service history of people they sponsored --}}
                @if($member->id === auth()->user()->id || $member->id === auth()->user()->spouseID || auth()->user()->can('view past community service')
                || auth()->user()->isAnActiveRover() || auth()->user()->isAnActiveRector())
                <div class="card mt-4">
                    <div class="card-header card-title">
                        Past Community Service
                        @can('record external positions served')
                        <div class="float-right"><a href="/admin/service/create/?id={{ $member->id }}">+</a></div>
                        @endcan
                    </div>

                    <div class="card-body">
                        @include('partials.roles_ive_served')
                    </div>
                </div>
                @endif
            @endif

            </div>
        @endcan

        </div>
    </div>




@endsection


@section('extra_css')
    <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.2/release/featherlight.min.css" type="text/css" rel="stylesheet" />
@endsection

@section('page-js')
    <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.2/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
@endsection
