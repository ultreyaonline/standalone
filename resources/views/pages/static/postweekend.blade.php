@extends('layouts.app')

@section('title')
    Post-Weekend - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header card-title">Post-Weekend</div>
                    <div class="card-body">

                    <h2>Post-Weekend Committee</h2>
                            <p><a href="mailto:{{ config('site.email-postweekend-mailbox') }}" rel="noopener noreferrer" target="_blank">{{ config('site.email-postweekend-mailbox') }}</a></p>
                            <p><b>Summary of Post-Weekend Committee role:</b></p>
                        <p><i>"The Post-Weekend Committee shall help form and promote reunion groups, distribute regular community communications, schedule and coordinate Secuelas, and other community gatherings..."</i></p>

                    <h3>Opportunities for Post-Weekend Involvement:</h3>
                        <ul>
                            <li><a href="/secuelas">Secuelas</a> (Help Organize food, speakers, worship team, venue)
                            <li><a href="/reuniongroups">Reunion Group</a> (Start one! Or at least participate in one!)
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
