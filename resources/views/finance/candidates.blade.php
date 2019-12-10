@extends ('layouts.app')

@section('title', 'Candidate Payments')

@section('body-class', 'payments')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header card-title"><span style="font-weight: bold;font-size: larger">Candidate Payment Administration</span>
                        <div class="btn-group float-right" role="group">
                            @if ($weekends->count())
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <strong>{{ $weekend->shortname }}</strong>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach ($weekends as $w)
                                            <li class="dropdown-item"><a href="/finance/candidates/{{ $w->number_slug }}/"><strong>{{ $w->shortname }}</strong></a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                    </div>
                    <div class="card-body">

                        @if ($candidates->count())
                            <a class="float-right" download="{{ 'Candidate Payments Data ' . strtoupper($weekend->number_slug) . ' as of ' . date('Y-m-d') }}.csv" href="{{ $csvData->toInlineCsv(['name', 'status', 'sponsor', 'details']) }}"><button class="btn btn-default"><i class="fa fa-file-text-o"></i> CSV</button></a>

                            <table class="table table-hover table-sm small">
                                <thead>
                                <th>Name/s</th>
                                <th>Fees Paid?</th>
                                <th>Sponsor</th>
                                <th>Confirmed-Details?</th>
                                <th>Notes</th>
                                </thead>
                                <tbody>

                                @foreach($candidates as $c)
                                    <tr>
                                        <td><a href="{{url('/finance/candidates/' . $weekend->number_slug . '/' . $c->id)}}">{{$c->man->name ?? ''}} {{ $c->man && $c->woman ? ' and ' : '' }}{{$c->woman->name ?? ''}}</a></td>
                                        <td align="left" class="{{ ! $c->fees_paid ? 'text-danger' : 'text-success' }}"><i class="fa fa-{{ $c->fees_paid ? 'check-' : ''}}square-o" aria-hidden="true"></i><span style="{{ ! $c->fees_paid ? 'font-weight:bold' : '' }}"> {{ ! $c->fees_paid ? 'UNPAID' : 'PAID' }}</span></td>
                                        <td align="left">{{ $c->sponsor }}</td>
                                        <td align="left"><i class="fa fa-{{ $c->sponsor_confirmed_details ? 'check-' : ''}}square-o" aria-hidden="true"></i> <strong>{{ ! $c->sponsor_confirmed_details ? ' PENDING CONFIRMATION' : '' }}</strong></td>
                                        <td align="left">{{ $c->payment_details }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        @else
                            <p>Sorry, no candidates found for the specified weekend.</p>
                        @endif

                    </div>
                </div>

            </div>


        </div>
    </div>

@endsection
