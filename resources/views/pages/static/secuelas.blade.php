@extends('layouts.app')

@section('title')
    Secuela - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header card-title">Secuela</div>
                    <div class="card-body">
                        <h3>What is a Secuela?</h3>

                        <p>The Secuela (Spanish for "sequel") is an expression of unity. All who have experienced the Tres Dias
                            weekend are encouraged to come together from time to time for fellowship. Living the Christian life
                            requires support, and this is one of the ways that the Tres Dias community assists in meeting this need,
                            by providing an atmosphere of encouragement.</p>

                        <p>The time usually allotted for this meeting is about two hours. Usually we meet at a local church.
                            Refreshments are usually provided during a period of informal fellowship. Singing serves as a prelude
                            for scripture reading and an opening prayer. General community announcements and updates are made,
                            followed by a short talk. This talk is one of personal sharing, including references to his/her piety,
                            study and action. Through this sharing, openness and love, we encourage each other to grow. Communion
                            may be celebrated and the meeting ends with a prayer.</p>

                        <p>To find our next Secuela date, see our <a href="/calendar">Calendar</a> page.</p>
                    </div>
                </div>



            </div>
        </div>
    </div>
@endsection
