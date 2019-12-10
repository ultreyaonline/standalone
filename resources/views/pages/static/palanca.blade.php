@extends('layouts.app')

@section('title')
    Palanca - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header card-title">Palanca</div>
                    <div class="card-body">

<p>We all remember what Palanca meant on our weekend.  To some of us it meant unconditional love from people we did not know.  To some it helped to change the hardest heart into one of love and forgiveness.  Our gifts of Palanca for the weekend may be God's way for us to minister to many candidates. You may create a Palanca blessing by yourself, but also consider working with your reunion group or a friend to enjoy fellowship while you create your Palanca.</p>

<h2>Palanca Committee</h2>
    <p><a href="mailto:{{ config('site.email-palanca-mailbox') }}" rel="noopener noreferrer" target="_blank">{{ config('site.email-palanca-mailbox') }}</a></p>
    <p><b>Summary of Palanca Committee role :</b>
        <i>"The Palanca Committee will oversee all palanca issues for the community including the Prayer Wheel, Banners, Oven Palanca, the gathering of community
            letters including those for the International Communities, and provides Palanca letters to other
            community weekends; receive and manage all palanca brought to send off..."</i>
    </p>
<h2>Here are some ways you can participate:</h2>
<ul>
    <li><b>72-hour Prayer Wheel Palanca</b> - The community provides prayer throughout the entire 72-hour weekend.
        Even if a person is not serving on the weekend, he or she can be an essential part by praying for an hour.
        It is vitally important that prayer cover the whole weekend.
        <a href="{{url('/prayerwheel')}}">Signup for the 72-hour Prayer Wheel</a>
    </li>
    <li><b>General Palanca Letter</b> - General Palanca Letters are read during the Holy Spirit Rollo.  This is one written by you to the entire group of Candidates as a whole. <a href="/palanca-sample-general-letter">(click to view a sample, and then customize it!)</a></li>
    <li><b>Individual Palanca Letters</b> - Placed in Sunday Bags for individual Candidates and/or Team Members. <a href="/palanca-sample-individual-letter">(click to view a sample to customize)</a></li>
    <li><b>Rollo Room Palanca</b> - 7-8 table-sized containers or approx. 40-45 pieces. One for each table, including Head/SD's table, or individual items for candidates and SD's, Rector, Head Cha, Rover, BUR, Worship Chas, etc.</li>
    <li><b>Dining Room Palanca</b> - Approx. 95-105 items presented to both Candidates and Team Members at meals and is greatly appreciated.  (This includes a few extras "just in case"!)</li>
    <li><b>Team Bed Palanca</b> - Approx. 70-75 items, for all Team Members.  (This includes a few extras "just in case"!).  You may also give Team Bed Palanca to individual team members.</li>
    <li><b>Candidates Bed Palanca</b> - Approx. 25-30 items, one for each Candidate. (Include a few extras "just in case"!)  We do not put out individual Candidate Bed Palanca.</li>
    <li><b>Oven Palanca</b> - Home baked goods</li>
    <li><b>Banner Palanca</b> - Banners may be "weekend theme" banners or an appropriate design of your choice.  They should be no larger than four (4) feet wide by six (6) feet long.  Please give banners to the Palanca Chas with a list of those who helped create it and any special instructions.  All banners become the property of {{ config('site.community_long_name') }}.</li>
</ul>
<p>(The amounts/quantities listed here are generalized. Please contact the Palanca Committee Chair for specific numbers for a particular weekend.)</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
