@extends ('layouts.app')

@section('title', 'Candidates - Sendoff Drivers Report')

@section('body-class', 'candidates')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <strong> Weekend: {{ $weekend }} {{ $gender }} SENDOFF DRIVERS/POINT OF CONTACT REPORT </strong>
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
                                <th>Sponsor&nbsp;Phone</th>
                                <th>Driver/POC</th>
                                <th>Driver&nbsp;Phone&nbsp;#</th>
                                <th>Emergency&nbsp;Contact</th>
                                </thead>
                                <tbody>

                                @foreach($candidates as $c)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->name) !!} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->sponsor) !!} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::phoneNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->sponsor_phone) !!} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{strtolower($gender).'_arrival_poc_person'}) !!} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::phoneNonBreaking($c->{strtolower($gender).'_arrival_poc_phone'}) !!} </td>
                                        <td>{{ $c->{strtolower($gender).'_emergency_name'} }} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::phoneNonBreaking($c->{strtolower($gender).'_emergency_phone'}) !!} </td>
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
