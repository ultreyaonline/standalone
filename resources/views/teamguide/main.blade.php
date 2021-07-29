@extends('layouts.app')

@section('title')
    Team Guide - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <h3>Team Guide and Cha Job Descriptions</h3>

        <div class="row">

            <div class="col-md-5 offset-md-1">
                <div class="card">
                    <div class="card-header card-title">Everyone</div>
                    <div class="card-body">
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Purpose of Team Meetings</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Cha - General (for Everybody)</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary"><i class="fa fa-graduation-cap"></i> Tres Dias Essentials</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary"><i class="fa fa-suitcase"></i> Packing List for the Weekend</button></a></p>
<p><a href="/vocabulary"><button class="btn btn-lg btn-primary"><i class="fa fa-file-word-o"></i> Vocabulary of Tres Dias</button></a></p>
@if(Str::contains(config('site.payments_accepts_donations', ''), 'fees'))
<p><a href="{{route('fees')}}"><button class="btn btn-lg btn-primary"><i class="fa fa-dollar"></i> Team Fees</button></a></p>
@endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-header card-title">Job Descriptions</div>
                    <div class="card-body">
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Head Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Assistant Head Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Backup Rector</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Chapel Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Dorm Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Palanca Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Dining Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Gopher Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-primary">Gopher Letter - Prayer Palanca</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-primary">Gopher Letter - Rollista</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Table Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Prayer Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Rover Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">A/V Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Storeroom Cha</button></a></p>
<p><a href="#"><button class="disabled btn btn-lg btn-primary">Floater/Supply Cha</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere><button class="btn btn-lg btn-primary">Worship Cha</button></a></p>

                      </div>
                  </div>
              </div>

              <div class="col-md-5">
                <div class="card">
                  <div class="card-header card-title">Rollos</div>
                  <div class="card-body">
For all Rollistas:
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Professor and Table Assistant Responsibilities</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Weekend Outline - Rollo Schedule</button></a></p>
<br>
                    <p class="small"><em>Talk Outlines (as approved in 2004 by Tres Dias International):</em></p>
<strong>Friday:</strong>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Ideals</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">The Church</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Piety</button></a></p>
<strong>Saturday:</strong>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Study</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Action</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Leaders</button></a></p>
<strong>Sunday:</strong>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Environments</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">CCIA</button></a></p>
<p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">Reunion Groups</button></a></p>
<p class="small">(The Rector's "Living The Fourth Day" talk outline is in the Rector's Guide.)</p>
                    </div>
                </div>

            </div>

          <div class="col-md-5">
            <div class="card">
              <div class="card-header card-title">Secuela</div>
              <div class="card-body">
                <p><a href="https://drive.google.com/open?id=insertShareLinkHere"><button class="btn btn-lg btn-primary">4th Day Secuela Talk Outline</button></a></p>
              </div>
            </div>
          </div>

        </div>
    </div>
@endsection
