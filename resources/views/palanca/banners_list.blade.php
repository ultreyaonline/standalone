@extends ('layouts.app')

@section('title', config('site.community_acronym') . ' General Community Banners')

@section('body-class', 'banners')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col mb-4">
                <nav class="text-center d-print-none mb-4">
                    <a href="/palanca-banners/men" role="button" class="btn btn-info btn-sm">Men's Weekend Banners</a>
                    <a href="/palanca-banners/women" role="button" class="btn btn-info btn-sm">Women's Weekend Banners</a>
                    <a href="/palanca-banners/general" role="button" class="btn btn-info btn-sm">General Banners</a>
                </nav>
                <h2>General Community Banners</h2>
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
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-body">
                        <p>Sorry, no banners found.</p>
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
