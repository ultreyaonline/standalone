@extends('layouts.app')

@section('title', 'Weekend Stats - ' . config('site.community_acronym'))

@section('content')
    <div class="container">
        <div class="card row">
            <div class="card-body col-lg-8 offset-lg-2">
                <h2>Weekend Stats - {{ config('site.community_acronym') }}</h2>
                <table class="table table-hover table-sm">
                    <thead>
                    <tr>
                        <th>Year</th>
                        <th>Weekend</th>
                        <th>Dates</th>
                        <th>Candidates</th>
                        <th>Team Members</th>
                        <th>Total People</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($candidates_sum = 0)
                    @foreach ($weekendsByYear as $weekends)
                        @php($year_total = 0)
                    <tr>
                        <td colspan="6" style="font-weight:bold">{{ $year = Illuminate\Support\Carbon::parse($weekends->first()->start_date)->format('Y') }}</td>
                    </tr>
                        @foreach ($weekends as $weekend)
                    <tr>
                        <td>&nbsp;</td>
                        <td><a href="/weekend/{{ $weekend->id }}">{{ $weekend->name_with_gender_and_number }}</a></td>
                        <td>{{ substr($weekend->short_date_range, 0, -6) }}</td>
                        <td style="text-align: center">{{ $weekend->totalcandidates }}</td>
                        <td style="text-align: center">{{ $weekend->totalteam }}</td>
                        <td style="text-align: center">{{ $weekend->totalteamandcandidates }}</td>
                    </tr>
                            @php($year_total += $weekend->totalcandidates)
                            @php($candidates_sum += $weekend->totalcandidates)
                        @endforeach
                    <tr style="border-bottom: 2px solid black">
                        <td colspan="3">&nbsp;</td>
                        <td colspan="3" class="text-success" style="font-weight: bold;"> {{ $year_total }} Candidates in {{$year}}</td>
                    </tr>
                    @endforeach

                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td colspan="6">Number of candidates added since inception: {{ $candidates_sum }}</td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
