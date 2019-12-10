@extends ('layouts.app')

@section('title', 'Candidates - Palanca Report')

@section('body-class', 'candidates')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <strong> Weekend: {{ $weekend }} {{ $gender }} PALANCA REPORT </strong>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <div class="card">

                    <div class="card-body">
                        @if ($candidates->count())

                            <table class="table table-hover table-sm small">
                                <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Sponsor</th>
                                <th>Sponsor Phone</th>
                                <th>Candidate Church</th>
                                <th>Married?</th>
                                </thead>
                                <tbody>

                                @foreach($candidates as $c)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->name) !!} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->sponsor) !!} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::phoneNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->sponsor_phone) !!} </td>
                                        <td>{{$c->{$gender === 'W' ? 'woman' : 'man'}->church }} </td>
                                        <td>{{$c->{strtolower($gender).'_married'} ? 'Married' : 'No' }} </td>
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
