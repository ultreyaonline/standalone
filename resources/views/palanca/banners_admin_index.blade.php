@extends ('layouts.app')

@section('title', 'Manage General Banners')

@section('body-class', 'banners')

@section('content')
    <div class="container">

        <div class="row border-bottom mb-4">
            <div class="col mb-4">
                <h2>Manage General Banners</h2>
                <p class="small text-left pb-3">Note: Weekend-specific banners are added/edited via each weekend's Edit screen.</p>

                <nav class="text-left">
                    <a href="{{ route('banners.create') }}" role="button" class="btn btn-primary"><i class="fa fa-plus"></i> Add New (Non-Weekend) Banner</a>
                </nav>
            </div>
        </div>

        @if ($banners->count())

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">

                @foreach ($banners as $banner)

                    <div class="col mb-4">
                        <div class="card h-100">
                            <a href="{{ $banner->banner_url_original }}" data-featherlight="image">
                                <img src="{{ $banner->banner_url }}" class="card-img-top img-fluid rounded mx-auto" alt="{{ $banner->title }}">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $banner->title }}</h5>
                            </div>

                            @if(auth()->user()->can('manage banners'))
                            <div class="card-footer text-right">
                                <a href="{{ route('banners.edit', ['banner' => $banner->id]) }}" role="button" class="btn btn-primary ml-3"><i class="fa fa-edit"></i> Edit/Delete</a>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-body">
                        <p>No general banners found.</p>
                    </div>
                </div>
            </div>
        @endif


    </div>

@endsection

@section('extra_css')
    <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.2/release/featherlight.min.css" type="text/css" rel="stylesheet">
@endsection

@section('page-js')
    <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.2/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
@endsection
