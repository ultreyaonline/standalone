@extends ('layouts.app')

@section('title', 'Candidates - Seating Information Report')

@section('body-class', 'candidates')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <strong> Weekend: {{ $weekend }} {{ $gender }} SEATING PLANNING REPORT </strong>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <div class="card">

                    <div class="card-body">
                        @if ($candidates->count())
                            <a class="pull-right" download="{{ 'Candidate Seating Data ' . $weekend_shortname . $gender . ' as of ' . date('Y-m-d') }}.csv" href="{{ $csvData->toInlineCsv(['name', 'married', 'age', 'sponsor', 'church', 'minister']) }}"><button class="btn btn-default"><i class="fa fa-file-text-o"></i> CSV</button></a>

                            <table class="table table-hover table-sm small">
                                <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Married?</th>
                                <th>Age</th>
                                <th>Sponsor</th>
                                <th>Church</th>
                                <th>Minister?</th>
                                </thead>
                                <tbody>

                                @foreach($candidates as $c)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->name) !!} </td>
                                        <td>{{$c->{strtolower($gender).'_married'} ? 'Married' : 'No' }} </td>
                                        <td>{{$c->{strtolower($gender).'_age'} }} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->sponsor) !!} </td>
                                        <td>{{$c->{$gender === 'W' ? 'woman' : 'man'}->church }} </td>
                                        <td>{{$c->{strtolower($gender).'_vocational_minister'} ? 'Pastor' : 'No' }} </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

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
