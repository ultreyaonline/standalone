@extends('layouts.app')

@section('title', 'Edit Banner')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-primary">
                    <div class="card-header alert-primary">Banner Image</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('banners.update', ['banner' => $banner->id]) }}" enctype="multipart/form-data">
                            @csrf @method('patch')

                            @if($banner->title)
                                <div class="card-header">{{ $banner->title }}</div>
                            @endif

                            @include('palanca.banners_input_fields')

                            <div class="form-group row d-inline">
                                <nav class="text-center">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> Update</button>
                                    <a href="javascript:history.back();"><button type="button" class="btn btn-secondary"><i class="fa fa-btn fa-times-circle"></i> Cancel</button></a>
                                </nav>
                            </div>
                        </form>

                        @if ($banner->banner_url)
                            <div class="text-center">
                                <form class="form-horizontal" role="form" method="POST" action="{{ route('banners.destroy', ['banner' => $banner->id]) }}" onsubmit="return ConfirmDelete();">
                                    @csrf @method('delete')
                                    <div class="form-group row d-inline">
                                        <button type="submit" class="btn btn-outline-danger"><i class="fa fa-btn fa-trash"></i> Delete Photo</button>
                                    </div>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


