@if($ourCandidates = isset($member) ? $member->sponsorees : auth()->user()->sponsorees)
<?php
$candidates = $ourCandidates->filter(function ($person) {
    // filter to display only unconfirmed candidates
    $candidate = \App\Candidate::query()
        ->where('m_user_id', $person->id)
        ->orWhere('w_user_id', $person->id)
        ->first();
    return $candidate ? $candidate->sponsor_confirmed_details === false : false;
})->sortBy('weekend');
$name = isset($member) ? $member->name : auth()->user()->name;
?>
 @if($candidates->count())
  <div class="card my-2 border-danger">
    <div class="card-header card-title bg-danger"><strong>Candidate Confirmation Required for {{ $name }}</strong></div>
    <div class="card-body">
        <p class="small alert-danger">NOTE: We have not received confirmation from you that the personal details for the following candidates has been recorded correctly in our database. We are unable to send invitations without you verifying their details.</p>
        <p class="small alert-danger">Upon registering them, we sent you an email with this information. Please review the email and click the confirmation link in that email.</p>
        <ul>
        @foreach($candidates as $candidate)
            <li>{{ $candidate->name }} - {{ $candidate->weekend }}</li>
        @endforeach
        </ul>
    </div>
  </div>
 @endif
@endif
