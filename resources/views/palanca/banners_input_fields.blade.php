<div class="row">
    <div class="col">
        @if($banner->banner_url)
            <div class="form-group row">
                <img src="{{ $banner->banner_url }}" class="card-img-top img-fluid rounded mx-auto" alt="{{ $banner->title }}">
            </div>
        @endif
        <div class="form-group row col">
            <div class="custom-file">
                <label class="col custom-file-label" for="bannerImage">
                    {{ $banner->banner_url ? 'Click to choose a new image' : 'Select an image with the Browse button ...' }}
                </label>
                <input class="col custom-file-input" type="file" name="banner_image" id="bannerImage" aria-label="Upload a banner image" accept=".png,.jpg,.jpeg,.gif,.bmp,image/*;capture=camera" required>
            </div>
        </div>

        <div class="form-group row{{ $errors->has('title') ? ' is-invalid' : '' }}">
            <label class="col-12" for="title">Description/Name</label>

            <div class="col-12">
                <input type="text" class="form-control" name="title" id="title" value="{{ old('title') ?: $banner->title }}" placeholder="short description of banner" required>
                @if ($errors->has('title'))
                    <span class="form-text"> <strong>{{ $errors->first('title') }}</strong> </span>
                @endif
            </div>
        </div>
    </div>
</div>
