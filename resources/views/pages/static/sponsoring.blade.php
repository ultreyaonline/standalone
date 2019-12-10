@extends('layouts.app')

@section('title')
    Sponsoring Someone - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header card-title">Sponsoring</div>
                    <div class="card-body">

                <h2>How do I Sponsor Someone?</h2>

                <p>Summary of the Sponsorship Process:</p>
                <ol>
                    <li>Pray that God would give you love, wisdom and discernment in sponsoring a candidate.</li>
                    <li>Read through the <strong><a href="/member_files/Sponsor%20Responsibilities.pdf" target="_blank">Sponsorship Responsibilities</a></strong> document
                        and download a copy of the <strong><a href="{{ config('site.candidate_application_url') }}" target="_blank">Candidate Application Form</a></strong>.</li>
                    <li>If you feel led to sponsor someone, pray God will give you the courage (if needed) along with the right heart and words in asking them.</li>
                    <li>Suggested things to tell them about the weekend:
                    <ul>
                        <li>There will be 15 total talks during the weekend, each building on the previous talks, and opportunity to discuss those talks</li>
                        <li>We will have lots of times to sing together and worship</li>
                        <li>There will be several drama skits, some entertaining and some quite serious</li>
                        <li>There will be a Chapel and communion time at least once each day</li>
                        <li>The food will be great, and refreshments always available</li>
                        <li>Several people will share very openly about how God has met them, even unexpectedly, in both painful and joyful seasons of life</li>
                        <li>I met some really wonderful brothers and sisters from other churches and have new friends as a result</li>
                        <li>God showed me __________ (ie: about how much I didn't understand His love), and ________ (ie: set me free from things I'd been carrying needlessly for years).
                            (Your personal story is significant here, so don't be afraid to share it!) </li>
                    </ul>
                    </li>
                    <li>If your candidate says 'OK', have them complete and sign their portion of the registration form and return it to you. A scan or photo is fine.</li>
                    <li>You fill out the Sponsor portion and send it in with a payment ({{ config('site.candidate_fee_text_for_emails') }}).<br>
                        Submit it by sending in the mail (see the address on the bottom of the application), or by emailing a scanned copy (or a clear photo from your phone) to {{ config('site.email-preweekend-mailbox') }}<br>
                        You can mail a cheque when you send the candidate form by mail, or you can send an email-money-transfer.<br>
                        All the details for paying fees, including online options, are viewable at: <a href="{{route('fees')}}">{{ route('fees') }}</a> .
                    </li>
                    <li>Start collecting Palanca letters, letting friends know that your candidate will
                        be attending a Tres Dias weekend, and enlisting their prayers.<br>
                        Collect letters from your candidate's spouse (if applicable), children, family members,
                        pastor, friends, co-workers, etc (should be someone who would write something encouraging and positive).
                        Don't forget to write one yourself!
                        If the candidate is married, we suggest that you explain privately that it's traditional for one spouse to receive a love letter from the other.
                        <p class="small">And, since the husband will (almost always) attend before the wife, you can certainly ask the wife to write a letter.
                            Perhaps phrase it like: “Your husband is going to be away on this weekend, spending time with God.
                            Do you think maybe he might appreciate receiving a letter from you about how much he means to you,
                            how much his kids appreciate him, and even perhaps some good things about him he might not easily see about himself?
                            Would you consider writing an encouraging letter like that, that I could make sure he gets while he’s letting God speak to him on the weekend?”
                            (She might ask if he’ll be writing a letter for her; if she asks, you can say yes, but if she doesn’t even ask, don’t mention anything about it so that it’s a surprise for her.
                            Then when his weekend is done, ask him to write a letter for her, now that he’s experienced how meaningful the letters were for him! He can also help collect letters from family and friends for his wife.)
                        </p>
                        Please don't ask the candidate’s spouse to notify family members or to collect letters, this is your role as a sponsor.
                        This also helps to minimize knowledge about the scope of the letter collection, which helps protect the specialness of the surprise.<br>
<br>
                        Mark the letters in an upper corner with “spouse”, “son”, “daughter”, “family”, “friend”, etc.
                        You can deliver the letters to the Palanca box (labelled "Paul Anka") at Send-Off.<br>

                        <p>To help request letters, click here to download an example: <a href="/member_files/TD%20Request%20for%20family%20letters%20generic.docx">Sample 'Request for letters from family and friends' Letter</a></p>
                    </li>
                    <li>Bring the candidate to Camp (or coordinate a person to drive them to/from the weekend site).</li>
                    <li>Drop off Palanca letters and any additional Palanca to the Palanca team. Remember, Palanca is a surprise so be discrete.</li>
                    <li>Pray for your candidate and the team throughout the weekend.</li>
                    <li>Come to closing ceremonies, bring your candidate home and continue to build them up as they go into their 4th day.
                        Encourage them to join a reunion group and attend Secuelas.</li>
                </ol>

                <p><a href="/member_files/Sponsor%20Responsibilities.pdf" target="_blank"><button class="btn btn-lg btn-primary">Sponsorship Responsibilities</button></a></p>
                <p><a href="{{ config('site.candidate_application_url') }}" target="_blank"><button class="btn btn-lg btn-primary">Candidate Application Form PDF</button></a></p>
                <p><a href="{{ url('/palanca') }}"><button class="btn btn-lg btn-primary"><i class="fa fa-gift"></i> More about Palanca Letters, gifts, food, etc</button></a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
