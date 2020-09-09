@extends('layouts.app')

@section('title', 'Create Member')

@section('content')
    <h1>Create New Member</h1>

    <div class="main-content">

        <form action="{{ action('App\Http\Controllers\MembersController@store') }}" method="post" role="form">
            @csrf

            @include('account.member_form_partial')

            <div class="form-group row">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary form-control"><i class="fa fa-btn fa-save"></i> Add Member</button>
                </div>
            </div>

        </form>

    </div>

@endsection
