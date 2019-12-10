@extends('layouts.app', ['robots_rules'=>'noindex'])
@section('title', 'Set Password')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="username" class="col-lg-4 col-form-label text-lg-right">{{ __('Username') }}</label>

                            <div class="col-lg-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ $username ?? old('username') }}" required autocomplete="username" autofocus>

                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-lg-4 col-form-label text-lg-right">{{ __('Password') }}</label>

                            <div class="col-lg-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-lg-4 col-form-label text-lg-right">{{ __('Confirm Password') }}</label>

                            <div class="col-lg-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-lg-6 offset-lg-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <p><strong>Passwords should be at least 7 characters.</strong></p>
                    <p>NOTE: If you get an "invalid token" message, it's possible that your reset-token has expired (for security)
                        ... in which case, you may <a href="/password/reset">click here to send yourself a new token.</a></p>
{{--                    <p><strong>Remember: </strong>If your email address is used by ONLY you, then your username is your email address.--}}
{{--                    But if you share an email with your spouse, then your username is your first and last name, without spaces, ie: FredSmith</p>--}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
