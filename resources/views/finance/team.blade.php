@extends ('layouts.app')

@section('title', 'Team Payments')

@section('body-class', 'teampayments')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-header card-title"><span style="font-weight: bold;font-size: larger">Team Fees Payment Administration</span>
                        <div class="float-right">
                            @if ($payments->count())
                            <a class="px-1" download="{{ 'Team Fee Payments Data ' . strtoupper($weekend->number_slug) . ' as of ' . date('Y-m-d') }}.csv" href="{{ $csvData->toInlineCsv(['first', 'last', 'amount', 'date', 'notes', 'recorded_by', 'recorded_at', 'last_updated']) }}"><button class="btn btn-outline-secondary"><i class="fa fa-file-text-o"></i> CSV</button></a>
                            @endif

                            @if ($weekends->count() && auth()->user()->can('record team fees paid'))
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <strong>{{ $weekend->name_with_gender_and_number }}</strong>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach ($weekends as $w)
                                            <li class="dropdown-item"><a href="/finance/team/{{ $w->id }}/"><strong>{{ $w->name_with_gender_and_number }}</strong></a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <strong>{{ $weekend->name_with_gender_and_number }}</strong>
                            @endif
                        </div>

                    </div>
                    <div class="card-body">
                        @if ($payments->count())
                            <div class="row">
                                <div class="small col-3"><strong>Name</strong></div>
                                <div class="small col-2"><strong>Amount Paid</strong></div>
                                <div class="small col-2"><strong>Date</strong> YYYY-MM-DD</div>
                                <div class="small col-3"><strong>Notes</strong></div>
                                <div class="small col-2"></div>
                            </div>
                                @foreach($payments as $p)
                                    <form class="form-inline row" action="{{ route('record_team_fee_payment', $weekend->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="memberID" value="{{ $p->memberID }}">

                                        <div class="col-3">{{ $p->user->first }} <strong>{{ $p->user->last }}</strong></div>
                                        <div class="col-2 input-group">
                                            <div class="input-group input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control" title="amount paid" name="total_paid" value="{{ $p->payment->total_paid }}" aria-label="Amount">
                                            </div>
                                        </div>
                                        <div class="col-2 input-group">
                                            <input type="text" class="form-control" title="payment date" name="date_paid" value="{{ $p->payment->date_paid }}" style="width:90%" placeholder="yyyy-mm-dd" aria-label="Date">
                                        </div>
                                        <div class="col-3 input-group">
                                            <input type="text" class="form-control" title="comments/notes" name="comments" value="{{ $p->payment->comments }}" style="width:100%" aria-label="Notes">
                                        </div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-secondary btn-sm">Save</button>
                                            <span style="font-size:14px; font-family:sans-serif" title="Created at: {{ $p->payment->created_at }}, Updated at {{ $p->payment->updated_at }}">{{ $p->payment->recorded_by }}</span>
                                        </div>
                                    </form>
                                    {{--<td class="{{ $p->payment->complete ? 'text-success' : 'text-danger' }}">{{ $p->payment->complete ? 'Yes' : 'No' }}</td>--}}
                                @endforeach

                        @else
                            <p>Sorry, there are no (visible) team members assigned for the specified weekend. Once the Rector has assigned team members and made the team available for viewing, then this list will show those names.</p>
                        @endif

                    </div>
                </div>

            </div>


        </div>
    </div>

@endsection
