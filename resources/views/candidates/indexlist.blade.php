@extends ('layouts.app')

@section('title', 'Candidate Admin')

@section('body-class', 'candidates')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header card-title"><span style="font-weight: bold;font-size: larger">Pre-Weekend Candidate Administration</span>
                        <div class="btn-group float-right" role="group">
                            @if ($weekends->count())
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <strong>{{ $weekend->shortname }}</strong>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach ($weekends as $w)
                                            <li class="dropdown-item"><a href="/candidates/{{ $w->number_slug }}/"><strong>{{ $w->shortname }}</strong></a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="btn-group float-right d-print-none" role="group" aria-label="Add Candidate Button" style="padding-right:5px">
                            <a href="/candidates/add"> <button class="btn btn-primary"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Candidate/Couple</button> </a>
                        </div>
                        <div class="btn-group float-right d-print-none" role="group" aria-label="Print Button" style="padding-right: 5px">
                            <a class="float-right" href="javascript:print()"><button class="btn btn-outline-secondary"><i class="fa fa-print"></i> Print</button></a>
                        </div>


                    </div>
                    <div class="card-body">

                        @if ($candidates->count())

                            <table class="table table-hover table-sm small">
                                <thead>
                                <th>#</th>
                                <th>Name/s</th>
                                <th>Sponsor Emailed</th>
@if(config('site.preweekend_sponsor_confirmations_enabled'))
                                <th>Sponsor Verified</th>
@endif
@if(config('site.preweekend_does_physical_mailing'))
                                <th>Ready to&nbsp;Mail?</th>
                                <th>Invite Mailed?</th>
@endif
                                <th>Welcome Emailed&nbsp;(M)</th>
                                <th>Welcome Emailed&nbsp;(W)</th>
                                <th>M&nbsp;Responded</th>
                                <th>W&nbsp;Responded</th>
                                </thead>
                                <tbody>

                                @foreach($candidates as $c)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td><a href="{{url('/candidates/' . $weekend->number_slug . '/' . $c->id)}}">{{ $c->names }}</a></td>
                                        <td align="left"><i class="fa fa-{{ $c->sponsor_acknowledgement_sent ? 'check-' : ''}}square-o" aria-hidden="true"></i> {!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->sponsor) !!}</td>
@if(config('site.preweekend_sponsor_confirmations_enabled'))
                                        <td align="left"><i class="fa fa-{{ $c->sponsor_confirmed_details ? 'check-' : ''}}square-o" aria-hidden="true"></i></td>
@endif
@if(config('site.preweekend_does_physical_mailing'))
                                        <td align="center"><i class="fa fa-{{ $c->ready_to_mail ? 'check-' : ''}}square-o" aria-hidden="true"></i></td>
                                        <td align="center"><i class="fa fa-{{ $c->invitation_mailed ? 'check-' : ''}}square-o" aria-hidden="true"></i></td>
@endif
                                        <td align="left">@if($c->man)<i class="fa fa-{{ $c->m_confirmation_email_sent ? 'check-' : ''}}square-o" aria-hidden="true"></i>@endif&nbsp;</td>
                                        <td align="left">@if($c->woman)<i class="fa fa-{{ $c->w_confirmation_email_sent ? 'check-' : ''}}square-o" aria-hidden="true"></i>@endif&nbsp;</td>
                                        <td align="left">@if($c->man)<i class="fa fa-{{ $c->m_response_card_returned ? 'check-' : ''}}square-o" aria-hidden="true"></i>@endif&nbsp;</td>
                                        <td align="left">@if($c->woman)<i class="fa fa-{{ $c->w_response_card_returned ? 'check-' : ''}}square-o" aria-hidden="true"></i>@endif&nbsp;</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        @else
                            <p>Sorry, no candidates found for the specified weekend.</p>
                        @endif

                    </div>
                </div>

                <div class="card" id="statistics">
                    <div class="card-header">Statistics <span class="float-right">(as of {{ $date }})</span></div>
                    <div class="card-body">
                        <p>
                            <span class="p-2 m-1 badge badge-danger">Men: {{ $men }}</span>
                            <span class="p-2 m-1 badge badge-success">Women: {{ $women }}</span>
                            <span class="p-2 m-1 badge badge-primary">Couples: {{ $couples }}</span>
                            <span class="p-2 m-1 badge badge-warning">Total People: {{ $total }}</span>
                        </p>
                    </div>
                </div>
            </div>


        </div>
    </div>

@endsection
