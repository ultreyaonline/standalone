@extends ('layouts.app')

@section('title', 'Candidates - Medications Cha Report')

@section('body-class', 'candidates')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <strong> Weekend: {{ $weekend }} {{ $gender }} MEDICATIONS CHA REPORT </strong>
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
                                <th>Medications Info</th>
                                <th>Sponsor</th>
                                </thead>
                                <tbody>

                                @foreach($candidates as $c)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->name) !!} </td>
                                        <td>{{$c->{strtolower($gender).'_age'} }} </td>
                                        <td>{{$c->{strtolower($gender).'_special_medications'} }} </td>
                                        <td>{!! \App\Helpers\HtmlEntity::spacesNonBreaking($c->{$gender === 'W' ? 'woman' : 'man'}->sponsor) !!} </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        @else
                            <p>Good news! So far no candidates have reported any medication considerations for this particular weekend.</p>
                        @endif

                    </div>
                </div>

            </div>


        </div>
    </div>

@endsection
