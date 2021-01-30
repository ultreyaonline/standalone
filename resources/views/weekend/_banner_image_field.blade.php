<div class="container">
  @if($weekend->banner_url)
  <div class="col-12">
    <img src="{{ $weekend->banner_url }}" class="img-thumbnail" alt="Banner Image">
  </div>
  @endif
</div>
<br>
<input type="file" name="banner_url" aria-label="Upload a weekend banner image" accept=".png,.jpg,.jpeg,.gif,.bmp,image/*;capture=camera" required>
