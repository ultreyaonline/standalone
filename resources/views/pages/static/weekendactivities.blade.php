@extends('layouts.app')

@section('title')
    Weekend Committee - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">

                <div class="card">
                    <div class="card-header card-title">Weekend Committee</div>
                    <div class="card-body">

                <h2>Weekend Committee</h2>
                        <p><b>Summary of Weekend Committee role:</b></p>
                        <p>"The Weekend Committee shall be responsible for
                                overseeing the storage and delivery of all supplies, stored materials and equipment, direct parking
                                for all weekend activities, arrange for the weekend photo, prepare the 4th day packet..."
                        </p>
                <h3>Opportunity for Community Involvement:</h3>
                    <ul>
                        <li>Supplies Purchasing, Inventory Replenishment</li>
                        <li>Inventory Tallying, and Storage Review</li>
                        <li>Weekend Set-up (Wed evening / Thurs morning-afternoon)</li>
                        <li>Weekend Take-down (Sunday afternoons, around 3pm usually)</li>
                        <li>Weekend Photo (Friday morning, immediately after breakfast)</li>
                    </ul>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
