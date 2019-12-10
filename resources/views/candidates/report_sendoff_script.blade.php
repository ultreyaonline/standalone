@extends ('layouts.app')

@section('title', 'Sendoff Script')

@section('body-class', 'candidates')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <strong> Weekend: {{ $weekend->weekend_full_name }} Sendoff Script</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <div class="card">

                    <div class="card-body">
                        @if ($candidates->count())

                        <p>
                            <em>(Pre-Weekend Team will let you know when everyone has arrived.)</em><br>
                            <em>(Consider asking the Head Cha and Rector to walk forward when they are introduced, and wait at the candidates' exit-door, so the candidates may follow them.)</em><br>
                            <br>
                            <strong>Husband:</strong><br>
                            Good Evening.  Welcome!<br>
                            <br>
                            <strong>Wife:</strong><br>
                            My name is: {{ $sendoff_person2 ? $sendoff_person2->name : '__________' }}.
                            I attended {{ $sendoff_person2 ? $sendoff_person2->weekend_long_name : '_________ Tres Dias #____' }}.<br>
                            <br>
                            <strong>Husband:</strong><br>
                            My name is: {{ $sendoff_person1 ? $sendoff_person1->name : '__________' }}.
                            I attended {{ $sendoff_person1 ? $sendoff_person1->weekend_long_name : '_________ Tres Dias #____' }}.<br>
                            My wife and I attend {{ $sendoff_person1 ? $sendoff_person1->church : '_________'}} (church).<br>
                            <br>
                            <strong>Wife</strong><br>
                            It’s our pleasure to welcome you to the Send-off for:<br>
                            <br>
                            {{ $weekend->long_name_with_number }} !<br>
                            <br>
                            <em>(Let any clapping and cheering happen. Then once quieted down, continue….)</em><br>
                            <br>
                            <strong>Wife:</strong> Let’s begin this weekend with a prayer!<br>
                            <br>
                            <strong>Husband:</strong> <em>(You may use this one or say one of your own)</em><br>
                            Dear Heavenly Father,<br>
                            We thank You for everyone here tonight, particularly those who are serving and those who are being served for the next three days.<br>
                            May You give each of them peace and assurance that they can put aside their worldly cares and focus upon You, Lord!
                            Protect their families and jobs while they are away.  Most importantly, we pray that Your Will be done on this weekend.
                            May all the glory, honor, and praise be unto you.  We pray this in Jesus’ precious name… Amen!<br>
                            <br>
                            <strong>Wife:</strong><br>
                            Thank you!<br>
                            At this time it is our privilege to introduce you to 2 people who are the Leadership Team for this weekend. <br>
                            When I call your name please stand and be recognized.<br>
                            <em>(Lead in clapping for each person)</em><br>
                            <br>
                            <strong>Our Head Cha:  {{ $head_cha_name ?? '__________' }}</strong><br>
                            <br>
                            <strong>Husband:</strong><br>
                            And it gives me great pleasure to introduce the <strong>Rector:   {{ $weekend->rector->name }} !!</strong><br>
                            <br>
                            <em>(Allow clapping and cheering to happen.  When it quiets enough, continue and speak clearly so the candidates can hear the next instructions.)</em><br>
                            <br>
                            <strong>Wife:</strong><br>
                            We are looking forward to a great weekend!  Just before we go to dinner, we are privileged to introduce the class of candidates for {{ $weekend->long_name_with_number }}.<br>
                            When I call your name, please stand up, give us your biggest smile, and exit out the ________ door. <em>(point to that door)</em><br>
                            After all the introductions we will be going outside and over to the dining hall. Please take your belongings with you (including your coats).<br>
                            <br>
                            <em>(Take turns calling out the names of the candidates.  However you’d like to do this.  Some have had one person begin at the top of the list and the other at the bottom.)</em><br>
                        </p>

                            <table class="table table-hover table-sm small">
                                <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Pronunciation</th>
                                </thead>
                                <tbody>

                                @foreach($candidates as $c)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$weekend->gender === 'W' ? 'woman' : 'man'}->name) !!} </td>
                                        <td>{{$c->{strtolower($weekend->gender).'_pronunciation'} }} </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        <p>
                            <strong>Husband:</strong><br>
                            The candidates and team are heading to dinner.<br>
                            <br>
                            Before we dismiss you, just one quick announcement:<br>
                            @if($weekend->gender == 'M')
                            Sunday's Closing ceremony starts at {{ $weekend->closing_scheduled_start_time->format('g:i a') }}. You should plan to arrive for {{ $weekend->closing_arrival_time->format('g:i a') }}.<br>
                            @else
                            Husbands and sponsors and community members are invited back to the closing ceremony on Sunday afternoon.
                            Please arrive by {{ $weekend->closing_arrival_time->format('g:i a') }}.<br>
                            @endif
                            <br>
                            Now, please take all your belongings with you.<br>
                            We ask that you quickly line the path outside, clapping and cheering for our candidates as they go to dinner.<br>
                            Thank you for coming!!<br>
                            You are dismissed!
                        </p>

                        @else
                            <p>Sorry, no upcoming candidates found.</p>
                            <p>Please check back for updates soon!</p>
                        @endif

                    </div>
                </div>

            </div>


        </div>
    </div>

@endsection
