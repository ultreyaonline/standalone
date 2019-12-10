@extends('layouts.app')

@section('title')
    Secretariat - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-10 offset-lg-1">
                <div class="card border-dark">
                    <div class="card-body">
                        <p><b>Secretariat Meetings:</b> See our Calendar page for details on the next meeting</p>
                        <br>
                        <p><b>The Secretariat consists of the following community members:</b></p>
                        <p>Spiritual Advisory Team: </p>
                        <p>A/V Committee: </p>
                        <p>Communications Committee: </p>
                        <p>Men's Rector Selection Committee: </p>
                        <p>Women's Rector Selection Committee: </p>
                    </div>
                </div>

                @include('pages.static.secretariat_org_chart')

                @can('email secretariat members')
                    <div class="card border-success mt-2">
                        <div class="card-header"><h3>Secretariat Members Access Only</h3></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p><a href="/secretariat/PandP.pdf"><button class="btn btn-info"><i class="fa fa-file-word-o"></i> Policies and Practices</button></a></p>
                                    <p><a href="/secretariat/secretariatservice.pdf"><button class="btn btn-info"><i class="fa fa-file-word-o"></i> Secretariat Service Descriptions</button></a></p>
                                    <p><a href="/secretariat/bylaws.pdf"><button class="btn btn-info"><i class="fa fa-file-word-o"></i> Community By-laws</button></a></p>
                                    <p><a href="/secretariat/articlesofincorporation.pdf"><button class="btn btn-info"><i class="fa fa-file-word-o"></i> Articles of Incorporation</button></a></p>
                                </div>
                                <div class="col-6">
                                    <p><a href="/reports/stats">
                                            <button class="btn btn-outline-success"><i class="fa fa-line-chart" aria-hidden="true"></i> Weekend Statistics</button>
                                        </a> <small>(Takes a minute to load)</small></p>

                                    <p><a href="{{ url('/leaders-help') }}">
                                            <button class="btn btn-outline-info"><i class="fa fa-question-circle"></i> Leaders Tools Website Instructions</button>
                                        </a></p>

                                    @can('create a weekend')
                                        <p><a href="/weekend/create">
                                                <button class="btn btn-outline-info"><i class="fa fa-plus" aria-hidden="true"></i> Add A Weekend</button>
                                            </a></p>
                                    @endcan

                                    @can('record candidate fee payments')
                                        <p><a href="/finance/candidates">
                                                <button class="btn btn-outline-info"><i class="fa fa-usd" aria-hidden="true"></i> Track Candidate Payments</button>
                                            </a></p>
                                    @endcan

                                    @can('record team fees paid')
                                        <p><a href="/finance/team">
                                                <button class="btn btn-outline-info"><i class="fa fa-usd" aria-hidden="true"></i> Track Team Member Payments</button>
                                            </a></p>
                                    @endcan

                                </div>
                            </div>

                        </div>
                    </div>
                @endcan

            </div>
        </div>
    </div>
@endsection
