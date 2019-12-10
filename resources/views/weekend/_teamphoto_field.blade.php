<div class="container">
  @if($weekend->team_photo)
  <div class="col-12">
    <img src="{{ $weekend->team_photo }}" class="img-thumbnail" alt="team photo">
  </div>
  @endif
</div>
<br>
<input type="file" name="teamphoto" aria-label="Upload a team photo">
