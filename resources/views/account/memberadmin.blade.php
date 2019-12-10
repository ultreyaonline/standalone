@extends('layouts.app')

@section('title', 'Members List')
{-- this is for listing multiple members found in search results, for the purpose of doing administration activities --}


@section('content')
    <h2>Members</h2>
    <hr>

    @foreach($members as $member)

        <h2>{{ $member->first }} {{ $member->last }}</h2>

        <article>
            <div class="body">{{ $member->weekend }}</div>
        </article>

    @endforeach

@endsection
