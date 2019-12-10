@extends('layouts.calculate')

@section('title')
    About {{ config('site.community_long_name') }}
@endsection

@section('body-class', 'about')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1>What is Tres Dias?</h1>
                <p>Tres Dias is an inter-denominational weekend for men and women that concentrates on the person of Jesus Christ and His teachings.</p>
                <p>While Tres Dias explores classic evangelical Christian beliefs, it is best described as a spiritual encounter with Christ. Many who have attended the weekend experience a deeper relationship with Christ as they sense His love and acceptance in a dynamic way.</p>
                <p>Tres Dias is also described as a Christian renewal movement. It encourages people to be in small groups to support each other in their lives as Christians.</p>
                <h4>What happens on a Tres Dias weekend?</h4>
                <p>The Spanish words ‘Tres Dias’ mean ‘Three Days’. Tres Dias begins on a Thursday evening and ends the following Sunday evening. The Tres Dias weekend is led by a team that is composed of former attendees of a Tres Dias or similar weekend like Cursillo or Walk to Emmaus. These volunteers invest their time for months of preparation by working and praying together as a unit. There, in a three day format, you will experience the foundations of the Christian life and the meaning of living the Christian ideal and its application in our daily lives.</p>
                <p>Tres Dias is centered around layman and clergy talks. Table groups discuss each talk. Discourse that develops from these discussions makes Christ’s teachings come alive. The atmosphere of Tres Dias is one of love, joy, Christian fellowship, singing, laughing, and worship. Tres Dias makes future Christian experiences more fruitful because of the zestful seasoning it brings to all Christian living. Tres Dias can be made only once in a lifetime. Therefore, it is not a substitute for a retreat, nor does it have the individual solitude of a retreat.</p>
                <h4>Who should attend a Tres Dias weekend?</h4>
                <p>Just as Christ meets you at your point of need, so Tres Dias is an encounter designed for those who desire to be strengthened in their faith and to experience many facets of God’s amazing grace. Tres Dias is for Christians of all denominations. It is especially gratifying for those who are in roles of leadership within the church. There are separate weekends for men and women. If you are married, the husband must attend first. Wives usually attend a week or two later. Each attendee must be sponsored by someone who has previously attended a Tres Dias or similar weekend.</p>
                <p><a class="btn btn-outline-primary" href="{{ config('site.candidate_application_url') }}" rel="nofollow" target="_blank">Apply to Attend</a></p>
                <h4>What is the history of Tres Dias?</h4>
                <p>Tres Dias was derived from Cursillo de Christianidad, a ‘short course in Christianity,’ which has been a tool of church renewal since its beginnings in Spain in 1949. Tres Dias was started in America in 1972 and is now overseen by an International organization.</p>
                <p><a class="btn btn-outline-success" href="http://www.tresdias.org" target="_blank">Visit tresdias.org</a></p>
                <h4>Find out more about our beliefs</h4>
                <p>While Tres Dias explores classic evangelical Christian beliefs, Tres Dias is an inter-denominational weekend for men and women that concentrates on the person of Jesus Christ and His teachings.</p>
                <p><a class="btn btn-primary" href="{{ url('/believe') }}">Our Beliefs</a></p>
            </div>
        </div>
    </div>
@endsection
