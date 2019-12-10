@extends('layouts.app')

@section('title', 'Mailchimp Audit - ' . config('site.community_acronym') . ' Admin')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-sm-12">

        <div class="card">
          <div class="card-header card-title">Newsletter Status - All Members</div>
          <div class="card-body">

            <table width="100%">
            @foreach($members as $member)
              <tr>
                <td>{{$member->name}}</td>
                <td>{{$member->email}}</td>
                {{--<td>{{$member->mailchimp ? 'OK' : 'MISSING'}}</td>--}}
                <td><button type="button" id="mce{{$member->id}}" onclick="checkMc({{$member->id}});">Check</button></td>
              </tr>
            @endforeach
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
function checkMc(i) {
    $.ajax('/members/' + i + '/mailchimp/check', {
        success: function() {
            $('#mce' + i).html('OK');
        },
        error: function() {
            $('#mce' + i).html('PROBLEM!');
        }
    });
}
</script>

@endsection
