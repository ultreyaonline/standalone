
<div class="form-group row">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="allow_address_share">Allow all {{ config('site.community_acronym') }} members to see my street address? (Rectors and anyone you're on a team with will be able to see it regardless)</label>
    </div>
</div>


<div class="form-group row">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="interested_in_serving">Interested in Serving on {{ config('site.community_acronym') }} Weekends?</label>
    </div>
</div>

<div class="form-group row">
    {!! Form::label('skills', 'Skills which might be useful when serving on weekends. (Please describe level of proficiency.):') !!}
    {!! Form::textarea('skills', old('skills'), ['class' => 'form-control', 'placeholder' => 'If you are interested in serving, which skills might be of interest to a rector putting together their team? Please explain your level of profiency with each skill.']) !!}
</div>


<p>Do you want to receive emails about community activities, including weekends, prayer requests, secuelas, etc?</p>
<p>(You will still receive emails about any weekend which you've committed to serve. The following is for other information and news.)</p>
<div class="form-group row">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="receive_email_weekend_general">Emails: News about Weekends (such as sendoffs, serenades, closings, prayer requests)
        </label>
    </div>
</div>
<div class="form-group row">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="receive_prayer_wheel_invites">Emails: Invitations to pray for an hour on a weekend
        </label>
    </div>
</div>

<div class="form-group row">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="receive_email_community_news">Emails: Community News
        </label>
    </div>
</div>

<div class="form-group row">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="receive_email_sequela">Emails: Secuelas
        </label>
    </div>
    <p>Note: We may email you about Secuelas even if you don't check this box if the Secuela also involves a community business meeting.</p>
</div>

<div class="form-group row">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="unsubscribe">Unsubscribe ALL
        </label>
    </div>
    <p>NOTE: If you unsubscribe here, you will be deemed as inactive and you will not be notified of any {{ config('site.community_acronym') }} activities. This will also establish you as a non-member of the {{ config('site.community_acronym') }} community.</p>
</div>
