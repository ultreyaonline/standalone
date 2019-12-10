@extends ('layouts.app')

@section('title')
    {{ $scope_title ?: config('site.community_acronym') . ' Community Directory' }}
@stop


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header">{{ $scope_title }}</div>
                    <div class="card-body">

                        @if ($users->count())
                            <div class="pagination">
                                {!! $users->links() !!}
                            </div>

                            <table class="table table-hover table-sm small">
                            @foreach ($users as $person)
                                    <tr>
                                        <td><a href="{{url('/members/' . $person->id)}}">{{$person->name}}</a></td>
                                        <td>{{$person->email}}</td>
                                        <td>{{$person->weekend}}</td>
                                    </tr>
                                @endforeach
                            </table>

                            <div class="pagination">
                                {!! $users->links() !!}
                            </div>
                        @else
                            <p class="alert alert-danger">Sorry, there are no matching records found.</p>
                        @endif

                    </div>
                    <div class="card-footer">Data as of {{ Illuminate\Support\Carbon::today()->toFormattedDateString() }}</div>
                </div>
            </div>
        </div>
    </div>

@stop
