@extends('layouts.app')

@section('title')
    Sample Individual Palanca Letter - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header card-title">Sample Individual Palanca Letter</div>
                    <div class="card-body">
                        <p>It has been the tradition in our community that individual Palanca letters are written by each team member for inclusion in each candidate's Sunday bag. Often, these are done as short hand-written notes, similar in nature to the general Palanca letter. They should be written in advance since many team members will not have time to write them on the weekend. They can be then individually addressed on the weekend once the candidate list is finalized. Alternately, a typewritten letter could be reproduced beforehand and addressed by hand to each candidate on the weekend. You may want to use past tense since it is normally read at the end or after the weekend. It is appropriate to mention the fourth day.</p>
                    </div>
                </div>


                <div class="card bg-info">
                    <div class="card-header card-title">Sample Individual Palanca Letter, written to each candidate separately</div>
                    <h3 class="card-header card-title bg-alert">THIS IS A SAMPLE ONLY. PLEASE CUSTOMIZE SIGNIFICANTLY!</h3>
                    <div class="card-body">

                        <p>Dear _________,</p>
                        <p>What a delight it has been to serve you on this Tres Dias weekend. I am honored that God has allowed me this privilege.</p>
                        <p>It has been a joy to watch our Father at work in all of your lives as He touched you with His love. It has been my prayer for many weeks that His perfect will would be accomplished in you during this Tres Dias weekend. I trust that He has prepared your heart to receive all that He has planned for you during these three days.</p>
                        <p>As you enter your fourth day, be open to Him. Receive His love. Yield your will to His truth that you may experience all that you are in Christ.</p>
                        <p>To God be all glory and honor and power forever.</p>
                        <p>In Jesus' love,</p>
                        <p>(Your signature)<br>(Your team position)</p>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
