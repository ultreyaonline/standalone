@extends('layouts.calculate')

@section('title')
    Thanks! - {{ config('site.community_long_name') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card p-4 border-success">
                    <h2>Thank you</h2>
                    <p>Thanks for your payment. You will receive an email confirmation shortly.</p>
                    <p>If your payment qualifies as a donation, a receipt will be sent in January.</p>
                    <p><a class="btn btn-primary" href="{{ url('/home') }}">Home</a></p>
                </div>
            </div>
        </div>
    </div>


@endsection
