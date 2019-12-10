<?php

namespace App\Http\Controllers;

use App\Candidate;
use App\User;
use App\Weekend;

class CandidateReportsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('role:Member|Pre-Weekend|Admin');

        // @TODO - restrict to Heads and Preweekend and Admin
    }

    public function special_notes($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $weekend_short_name = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::where('weekend', $weekend_short_name)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->whereNotNull(strtolower($gender) . '_special_notes')
            ->orderBy(strtolower($gender) . '_special_notes', 'desc')
            ->with(['man', 'woman'])
            ->get();

        return view('candidates.report_special_notes', compact('candidates', 'gender'))->withWeekend($weekend_short_name);
    }

    public function dorm_needs($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $weekend_short_name = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::where('weekend', $weekend_short_name)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->orderBy(strtolower($gender) . '_special_dorm', 'desc')
            ->with(['man', 'woman'])
            ->get();

        return view('candidates.report_dorm', compact('candidates', 'gender'))->withWeekend($weekend_short_name);
    }

    public function meds($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $weekend_short_name = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::where('weekend', $weekend_short_name)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->whereNotNull(strtolower($gender) . '_special_medications')
            ->orderBy(strtolower($gender) . '_special_medications', 'desc')
            ->with(['man', 'woman'])
            ->get();

        return view('candidates.report_meds', compact('candidates', 'gender'))->withWeekend($weekend_short_name);
    }

    public function diet($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $weekend_short_name = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::where('weekend', $weekend_short_name)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->whereNotNull(strtolower($gender) . '_special_diet')
            ->orderBy(strtolower($gender) . '_special_diet', 'desc')
            ->with(['man', 'woman'])
            ->get();

        return view('candidates.report_diet', compact('candidates', 'gender'))->withWeekend($weekend_short_name);
    }

    public function prayer_requests($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $weekend_short_name = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::where('weekend', $weekend_short_name)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->orderBy(strtolower($gender) . '_special_prayer', 'desc')
            ->with(['man', 'woman'])
            ->get();

        return view('candidates.report_prayer', compact('candidates', 'gender'))->withWeekend($weekend_short_name);
    }

    public function numbered_list($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $weekend_short_name = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::where('weekend', $weekend_short_name)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->with(['man', 'woman'])
            ->get();

        return view('candidates.report_numbered_list', compact('candidates', 'gender'))->withWeekend($weekend_short_name);
    }

    public function seating_info($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $constraint = $gender == 'M' ? 'man' : 'woman';

        $weekend_shortname = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::where('weekend', $weekend_shortname)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->with([$constraint])
            ->get()
            ->sortBy($constraint . '.last')
            ->sortBy($constraint . '.church');


        $csvData = null;
        foreach($candidates as $c) {
            $csvData[] = [
                'name' => $c->{$gender === 'W' ? 'woman' : 'man'}->name,
                'married' => $c->{strtolower($gender).'_married'} ? 'Married' : 'No',
                'age' => $c->{strtolower($gender).'_age'},
                'sponsor' => $c->{$gender === 'W' ? 'woman' : 'man'}->sponsor,
                'church' => $c->{strtolower($gender).'_church'},
                'minister' => $c->{strtolower($gender).'_vocational_minister'} ? 'Pastor' : 'No',
            ];
        }
        $csvData = collect($csvData);

        return view('candidates.report_seating', compact('candidates', 'gender', 'csvData', 'weekend_shortname'))->withWeekend($weekend_shortname);
    }

    public function palanca($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $constraint = $gender == 'M' ? 'man' : 'woman';
        $weekend_short_name = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::where('weekend', $weekend_short_name)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->with([$constraint])
            ->get()
            ->sortBy($constraint . '.first');

        return view('candidates.report_palanca', compact('candidates', 'gender'))->withWeekend($weekend_short_name);
    }

    public function sendoff_script($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $weekend = Weekend::numberAndGender($weekend, $gender)->first();
        $candidates = Candidate::where('weekend', $weekend->shortname)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->with(['man', 'woman'])
            ->get();

        $head_cha_name = null;
        if ($head_cha_id = $weekend->head_cha->first()) {
            $head_cha = User::findOrFail($head_cha_id);
            $head_cha_name = $head_cha->name;
        }

        $sendoff_person1 = User::find($weekend->sendoff_couple_id1);
        $sendoff_person2 = User::find($weekend->sendoff_couple_id2);

        return view('candidates.report_sendoff_script', compact('candidates', 'weekend', 'head_cha_name', 'sendoff_person1', 'sendoff_person2'));
    }

    public function sendoff_drivers($weekend, $gender)
    {
        if (!is_numeric($weekend)) {
            return false;
        }
        if (!in_array($gender, ['M', 'W'])) {
            return false;
        }

        $weekend_short_name = config('site.community_acronym') . ' #' . $weekend;
        $candidates = Candidate::query()
            ->where('weekend', $weekend_short_name)
            ->where(strtolower($gender) . '_user_id', '>', 0)
            ->with(['man', 'woman'])
            ->get();

        return view('candidates.report_sendoff_drivers', compact('candidates', 'gender'))->withWeekend($weekend_short_name);
    }
}
