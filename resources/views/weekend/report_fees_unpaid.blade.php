@extends ('layouts.app')

@section('title', 'Team Fees Outstanding')

@section('body-class', 'team-fees-unpaid')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <strong> Weekend: {{ $weekend->name_with_gender_and_number }} Team Fees Due </strong>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <div class="card">

                    <div class="card-body">

                            <table class="table table-hover table-sm small">
                                <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Community</th>
                                </thead>
                                <tbody>

                                @foreach($unpaid as $key => $community)
                                    <tr>
                                        <td colspan="4"><strong>{{ $key }}</strong></td>
                                    </tr>
                                    @foreach($community as $j)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{$j->user->name }} </td>
                                        <td>{{$j->role->RoleName }} </td>
                                        <td>{{$j->user->community }} </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>

                    </div>
                </div>

            </div>


        </div>
    </div>

@endsection
