@extends ('layouts.app')

@section('title', 'Add Banner')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-primary">
                    <div class="card-header alert-primary">Banner Image</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data">
                            @csrf

                            @include('palanca.banners_input_fields')

                            <div class="form-group row d-inline">
                                <nav class="text-center">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> Save Photo</button>
                                    <a href="javascript:history.back();"><button type="button" class="btn btn-secondary"><i class="fa fa-btn fa-times-circle"></i> Cancel</button></a>
                                </nav>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
