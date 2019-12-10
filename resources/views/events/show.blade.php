@extends ('layouts.app')

@section('title')
{{ $event->name }} | {{ config('site.community_acronym') }}
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10 offset-sm-1">

                <div class="card">
                    <div class="card-header card-title">{{ config('site.community_acronym') }} Event: {{ $event->name }}</div>
                    <div class="card-body">

                    @include('events._display_event')

                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
