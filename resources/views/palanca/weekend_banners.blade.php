@extends ('layouts.app')

@section('title', config('site.community_acronym') . ' ' . $title_banner_type . ' Banners')

@section('body-class', 'banners')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col mb-4">
                <nav class="text-center d-print-none mb-4">
                    <a href="/palanca-banners/men" role="button" class="btn btn-info btn-sm">Men's Weekend Banners</a>
                    <a href="/palanca-banners/women" role="button" class="btn btn-info btn-sm">Women's Weekend Banners</a>
                    @if(!empty(\App\Models\Banner::first()))
                        <a href="/palanca-banners/general" role="button" class="btn btn-info btn-sm">General Banners</a>
                    @endif
                </nav>
                <h2>{{ $title_banner_type }} Banners</h2>
            </div>
        </div>

        @if ($banners->count())

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4">

                @foreach ($banners as $banner)

                    <div class="col mb-4">
                        <div class="card h-100">
                            <a href="{{ $banner->banner_url_original }}" data-featherlight="image">
                                <img src="{{ $banner->banner_url }}" class="card-img-top img-fluid rounded mx-auto" alt="{{ $banner->weekend_full_name }}">
                            </a>
                            <div class="card-body">
                                @if(auth()->user()->can('manage banners'))
                                    <div class="text-right d-print-none">
                                    <a href="/weekend/{{ $banner->id }}/edit" class="badge badge-pill badge-secondary ml-3"> Edit </a>
                                    </div>
                                @endif
                                <h5 class="card-title">{{ $banner->weekend_full_name }}</h5>
                                <p class="card-text small"><em>Rector: {{ $banner->rector->name }}</em></p>
                                <p class="card-text text-primary">{{ $banner->weekend_theme }}</p>
                                <p class="card-text small">
                                    <em>
                                        {{ $banner->weekend_verse_text }}<br>
                                        <span class="small">{{ $banner->weekend_verse_reference }}</span>
                                    </em>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-body">
                        <h3>No weekend banners found.</h3>
                        <p>Banners will appear here after they've been uploaded to individual weekends by the Rector or an Administrator.</p>
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
