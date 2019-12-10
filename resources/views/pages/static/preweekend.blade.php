@extends('layouts.app')

@section('title')
    Pre-Weekend Committee - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header card-title">Pre-Weekend</div>
                    <div class="card-body">

                    <h2>Pre-Weekend Committee</h2>
                            <p><a href="mailto:{{ config('site.email-preweekend-mailbox') }}" rel="noopener noreferrer" target="_blank">{{ config('site.email-preweekend-mailbox') }}</a></p>
                            <p><b>Summary of Pre-weekend Committee role:</b>
                              <i>"The Pre-Weekend Committee shall receive candidate
                                    applications, maintain files of Candidate applications for the Weekend, coordinate Candidate
                                    communications, provide applications and materials for Sponsor(s), report the status of the
                                    Candidate roster, coordinate Weekend registration and send-off ceremony..."</i>
                                </p>

                    <h3>Candidate Application</h3>
                            <p>Each candidate sponsored to attend a Tres Dias weekend must complete and sign a weekend application.
                              Completed applications should be submitted to the Pre-Weekend Committee by the SPONSOR, along with the associated candidate fees.</p>
                            <button><a href="{{ config('site.candidate_application_url') }}" target="_blank">Candidate Application in PDF Format</a></button>
                        <br>
                        <br>
                    <h3>Being a Sponsor</h3>
                            <p>Sponsoring candidates to attend a Tres Dias weekend is a blessing, not only to the candidate, but to the sponsor as well. With the blessing comes responsibility as well. The following page will provide you with detailed information about what is required.</p>
                            <button><a href="/sponsoring">Sponsor Responsibilities</a></button>
                        <br>
                        <br>
                    <h3>Sendoff</h3>
                    Opportunities for Community Involvement:
                        <ul>
                            <li>Greeters (at dorm, at sendoff, at check-in, etc)</li>
                            <li>Luggage handlers (carrying candidates' luggage to their dorms)</li>
                            <li>Sendoff or 'master of ceremony' couple</li>
                            <li>Refreshments setup (if any)</li>
                        </ul>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
