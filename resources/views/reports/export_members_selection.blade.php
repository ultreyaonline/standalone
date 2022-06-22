@extends('layouts.app', ['robots_rules'=>'noindex'])
@section('title', 'Export ' . config('site.community_acronym') . ' Member Data')

@section('content')
    <div class="container d-print-none">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h4>Export Member Data</h4>
                <div class="card">
                    <form action="{{ route('SelectMembersToExport') }}" method="post" class="form-horizontal">
                        @csrf
                        <input type="hidden" name="interactive" value="yes">

                        <div class="card-header"><strong>Filter: choose which members to include in the export</strong></div>
                        <div class="card-body">

                            <div class="form-group row">
                                <div class="col-md-11"><h5>Men/Women?</h5>
                                    <div class="offset-md-1">
                                        <div class="radio">
                                            <label for="gender_both" class="col-form-label">
                                                <input type="radio" name="filter_gender" id="gender_both" value="B" checked="checked"> Both Men and Women
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label for="gender_men">
                                                <input type="radio" name="filter_gender" id="gender_men" value="M" > Men Only
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label for="gender_women">
                                                <input type="radio" name="filter_gender" id="gender_women" value="W" > Women Only
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-11"><h5>Which Communities?</h5>
                                    <div class="offset-md-1">
                                        <div class="checkbox">
                                            <label for="community_local" class="col-form-label">
                                                <input type="checkbox" name="community_local" id="community_local" value="local" checked="checked"> all {{ config('site.community_acronym') }} members
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label for="community_other">
                                                <input type="checkbox" name="community_other" id="community_other" value="other"> all non-{{ config('site.community_acronym') }} members
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-11"><h5>Active/Inactive?</h5>
                                    <div class="offset-md-1">
                                        <div class="checkbox">
                                            <label for="filter_active" class="col-form-label">
                                                <input type="checkbox" name="filter_active" id="filter_active" value="active" checked="checked"> Active/Enabled Members
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label for="filter_inactive">
                                                <input type="checkbox" name="filter_inactive" id="filter_inactive" value="inactive"> Inactive/Unsubscribed Members
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="card-header">
                                What data/columns to include?
                            </div>



                            <div class="row">
                                <div class="col-lg-10 offset-lg-1">

                                    <div class="checkbox">
                                        <label for="name" class="col-form-label">
                                            <input id="name" type="checkbox" disabled name="name" value="yes" checked>
                                            First and Last Names (always included)
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="weekend" class="col-form-label">
                                            <input id="weekend" type="checkbox" disabled name="weekend" value="yes" checked>
                                            Weekend (always included)
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="email" class="col-form-label">
                                            <input id="email" type="checkbox" disabled name="email" value="yes" checked>
                                            Email Address (always included)
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="address" class="col-form-label">
                                            <input id="address" type="checkbox" name="address" value="yes" checked>
                                            Mailing Address
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="church" class="col-form-label">
                                            <input id="church" type="checkbox" name="church" value="yes"{{ old('church') === 'yes' ? ' checked' : '' }}>
                                            Church
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="community" class="col-form-label">
                                            <input id="community" type="checkbox" name="community" value="yes"{{ old('community') === 'yes' ? ' checked' : '' }}>
                                            Community
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="gender" class="col-form-label">
                                            <input id="gender" type="checkbox" name="gender" value="yes"{{ old('gender') === 'yes' ? ' checked' : '' }}>
                                            Gender
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="phone" class="col-form-label">
                                            <input id="phone" type="checkbox" name="phone" value="yes"{{ old('phone') === 'yes' ? ' checked' : '' }}>
                                            Phone Numbers
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="sponsor" class="col-form-label">
                                            <input id="sponsor" type="checkbox" name="sponsor" value="yes"{{ old('sponsor') === 'yes' ? ' checked' : '' }}>
                                            Name of Sponsor
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="prefs_email" class="col-form-label">
                                            <input id="prefs_email" type="checkbox" name="prefs_email" value="yes"{{ old('prefs_email') === 'yes' ? ' checked' : '' }}>
                                            Preferences Regarding Which Emails To Receive
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="active_status" class="col-form-label">
                                            <input id="active_status" type="checkbox" name="active_status" value="yes"{{ old('active_status') === 'yes' ? ' checked' : '' }}>
                                            Active/Inactive Status
                                        </label>
                                    </div>


                                    <div class="checkbox">
                                        <label for="interested_in_serving" class="col-form-label">
                                            <input id="interested_in_serving" type="checkbox" name="interested_in_serving" value="yes"{{ old('interested_in_serving') === 'yes' ? ' checked' : '' }}>
                                            Interested in Serving
                                        </label>
                                    </div>

                                    <div class="checkbox">
                                        <label for="extras" class="col-form-label">
                                            <input id="extras" type="checkbox" name="extras" value="yes"{{ old('extras') === 'yes' ? ' checked' : '' }}>
                                            Extra/Additional Meta Data (admin purposes)
                                        </label>
                                    </div>

                                    <div class="checkbox">
                                        <label for="service_history" class="col-form-label">
                                            <input id="service_history" type="checkbox" name="service_history" value="yes"{{ old('service_history') === 'yes' ? ' checked' : '' }}>
                                            Weekend Service History
                                        </label>
                                    </div>

                                    <div class="checkbox">
                                        <label for="candidates_sponsored" class="col-form-label">
                                            <input id="candidates_sponsored" type="checkbox" name="candidates_sponsored" value="yes"{{ old('candidates_sponsored') === 'yes' ? ' checked' : '' }}>
                                            Candidates Sponsored
                                        </label>
                                    </div>

                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        Export CSV
                                    </button>
                                </div>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
