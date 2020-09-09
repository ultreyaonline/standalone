@extends('layouts.app')

@section('title', 'Payment for Candidate')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Candidate Payment</div>
          <div class="card-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ action('App\Http\Controllers\CandidatePaymentsController@update', ['candidate' => $candidate->id] ) }}">
              @csrf @method('patch')


              <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header card-title">Candidate Details</div>

                    <div class="card-body">
                      <p><strong>Name(s):</strong> {{ $candidate->names }}</p>
                      <p><strong>Sponsor:</strong> {{ $candidate->sponsor }}</p>
                      <p><strong>Weekend:</strong> {{ $candidate->weekend }}</p>

                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header card-title">Candidate Payment Information</div>

                    <div class="card-body">

                      <div class="col-md-10 offset-md-2">
                        <div class="checkbox">
                          <label for="fees_paid">
                            <input type="checkbox" name="fees_paid" id="fees_paid"
                                   value="1" {{($candidate->fees_paid ?? old('fees_paid')) ? 'checked' : ''}}{{ $user->can('record candidate fee payments') ? '' : ' disabled' }}> Fees Paid?
                          </label>
                        </div>
                      </div>

                    </div>


                    <div class="form-group{{ $errors->has('payment_details') ? ' is-invalid' : '' }}">
                      <label class="col-md-4 control-label" for="payment_details">Payment Notes</label>

                      <div class="col-md-8">
                        <input type="text" class="form-control" name="payment_details" id="payment_details" value="{{ old('payment_details') ?: $candidate->payment_details }}">
                        @if ($errors->has('payment_details'))
                          <span class="form-text"> <strong>{{ $errors->first('payment_details') }}</strong> </span>
                        @endif
                      </div>
                    </div>


                    <div class="form-group row">
                      <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-save"></i> Save</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
