<div class="card mb-3 border-warning">
  <div class="card-header card-title">Admin</div>
  <div class="card-body">
    <p class="small">Depending on whether this weekend has started or has recently ended, buttons may appear here for candidate communication or converting them to Pescadore status.</p>
    @unless($weekend->has_started)
        <a href="{{ url('/reminders/w/' . $weekend->id) }}" onclick="return ConfirmSend();"><button class="btn btn-secondary"><i class="fa fa-suitcase fa-2x"></i> Email Packing List to all Candidates</button></a>
    @endunless

    @if($weekend->ended_this_month || $weekend->ends_today)
      <form action="{{ route('convert_weekends_candidates_to_pescadores', $weekend->id) }}" method="POST" onsubmit="return ConfirmAction();">
        @csrf
        <button class="btn btn-secondary"><i class="fa fa-exchange fa-2x"></i> (After Weekend) Convert all candidates to Pescadores</button>
      </form>
    @endif

    @if($weekend->ended_this_month || $weekend->ends_today)
      <form action="{{ route('website_access_to_candidates', $weekend->id) }}" method="POST" onsubmit="return ConfirmSend();">
        @csrf
        <button class="btn btn-secondary"><i class="fa fa-envelope-o fa-2x"></i> (After Weekend) Send Website Welcome/Login to all new Pescadores</button>
      </form>
    @endif

  </div>
</div>
