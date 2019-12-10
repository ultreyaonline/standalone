@extends('layouts.app')

@section('title')
    Reunion Groups - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header card-title">Reunion Groups</div>
                    <div class="card-body">
                        <h2>Where Do I Find Reunion Groups?</h2>
                        <p>Our <a href="{{ config('site.facebook_page') }}">Facebook group</a> is where we currently announce member-organized meetups such as reunion groups,
                            picnics, etc.</p>
                        <p>If you're interested in starting a Reunion Group, or interested in joining a Reunion Group, please
                            post it in our <a href="{{ config('site.facebook_page') }}">Facebook group.</a></p>
                    </div>

                    @if(config('site.reunion_groups_google_sheet_url'))
                    <div class="card-body">
                        <p>Here is a periodically-updated list of reunion groups. If yours isn't on here, please let us know so we can add it!</p>
                        <iframe src="{{ config('site.reunion_groups_google_sheet_url') }}" width="100%" height="300"></iframe>
                    </div>
                    @endif

                    <div class="card-body">

<h3>
    Importance of Reunion Groups
</h3>
<em>by JR Davison, NGTD #34</em>

<p>The importance of being in a reunion group, as an essential for our Christian growth, cannot be over-communicated.
    The reunion group is where we find encouragement and personal insights and prayer for the issues in our lives.
    Without close relationships with several other men or women on a regular basis, we will remain in isolation making
    little progress toward finding what it means to have a dependant and meaningful relationship with our Lord. Isolation with
    our thoughts, problems and fears is one of our Adversary's greatest weapons.</p>
<p>If we want to see the importance of having others around us we need to only look to see the example Christ showed us.
    As he began to share the love of his Father with the world he chose 12 men to walk with him daily. If Christ Himself
    knew the importance of having men around him why do we continue to try to live the Christian life alone?
    I've come to believe it's because of our fears of what others think, and don't really want to hear what others might think.
    We fear rejection, which is the very reason why Adam and Eve hid from God.</p>
<p>The main reason Christ came was to reconnect man back into a relationship with his Father and to remove them from a
    system that focused on their performance as a way to be accepted. Because of our relationship with Christ, we know
    the love of the Father and unknowingly He has placed into us the desire for meaningful relationship with those who
    are precious to Him. The Tres Dias Weekends and Reunion Groups are our opportunity to reveal His life to those who
    are looking for Him and one another. This is why the Reunion Group is important. Our time together is a reminder of
    our Father's love and acceptance of us in our daily lives.</p>
<p>If you aren't in a group I'd encourage you to seek one out or start one yourself and ask those men or women who are
    precious to you to join you for a time together with Him.
    You will be blessed by taking this next step to know Him in a more personal way.
</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
