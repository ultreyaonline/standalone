@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="main-content">

        <img src="http://placehold.it/250x250/d3d3d3" class="member-photo" loading="lazy">

        <div>Name/Weekend: {{ $member->firstName . ' ' . $member->lastName }} {{ $member->weekend }}</div>
        <div>Spouse: {{ $member->spouseDetails }} </div>
        <div>Sponsor: {{ $member->sponsor }} </div>

        <form action="{{ url('/account/edit') }}" method="post" role="form">
            @csrf


            @include('account.member_form_partial')

        <div class="form-group row">
            <label for="photo">Upload/Replace Profile Photo:</label>
            <input type="file" id="photo" name="photo" class="form-control">
        </div>

        <div class="form-group row">
                <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> Update Profile</button>
        </div>

        </form>

        <div class="service-history">
            <p>Past Community Weekend Service as recorded in our database:</p>
            <ul class="served-weekends">
                @foreach ($member->servedWeekends() as $service)
                    <li class="served-row">{{ $service }}</li>
                @endforeach
            </ul>

        </div>



    </div>

@endsection
