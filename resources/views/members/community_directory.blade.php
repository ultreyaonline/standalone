@extends ('layouts.app', ['robots_rules'=>'noindex'])

@section('title')
    {{ $scope_title ?: config('site.community_acronym') . ' Community Directory' }}
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                    @if ($users->count())
                        @livewire('members-directory')
                    @else
                        <p class="alert alert-danger">Sorry, there are no matching records found.</p>
                    @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('extra_css')
    @livewireStyles
    <style>th, .nowrap {white-space: nowrap; }</style>
    <style>
        #searchClear {
            position: absolute;
            right: 1.5rem;
            top: 0;
            bottom: 0;
            height: 1.5rem;
            margin: auto;
            font-size: 1rem;
            cursor: pointer;
            color: #ccc;
        }
    </style>@endsection

@section('page-js')
    @livewireScripts
@endsection
