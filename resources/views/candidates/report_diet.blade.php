@extends ('layouts.app')

@section('title', 'Candidates - Dining Cha Report')

@section('body-class', 'candidates')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <strong> Weekend: {{ $weekend }} {{ $gender }} DINING CHA REPORT </strong>
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
                                <th>Age</th>
                                <th>Dietary Requests</th>
                                <th>Sponsor</th>
                                </thead>
                                <tbody>

                                @foreach($candidates as $c)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->name) !!} </td>
                                        <td>{{$c->{strtolower($gender).'_age'} }} </td>
                                        <td>{{$c->{strtolower($gender).'_special_diet'} }} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->sponsor) !!} </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        @else
                            <p>Good news! No candidates have indicated any critical dietary requirements for this particular weekend!</p>
                        @endif

                    </div>
                </div>

            </div>


        </div>
    </div>

@endsection
