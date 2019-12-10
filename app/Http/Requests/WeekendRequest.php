<?php
namespace App\Http\Requests;

use App\Enums\WeekendVisibleTo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class WeekendRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::user()->can('create a weekend')) {
            return true;
        }
        if (Auth::user()->can('edit weekends')) {
            return true;
        }
        if ($this->input('rectorID') == Auth::id()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $dates = [
            'start_date',
            'end_date',
            'candidate_arrival_time',
            'sendoff_start_time',
            'closing_arrival_time',
            'closing_scheduled_start_time',
            'serenade_arrival_time',
            'serenade_scheduled_start_time',
        ];
        foreach ($dates as $date) {
            if (strlen($this->input($date)) === 16) {
                $this->request->set($date , $this->input($date) . ':00');
            }
        }

        $rector = [];
        if ($this->input('rectorID') && $this->input('visibility_flag', 0) > WeekendVisibleTo::Calendar) {
            $rector = [
                'rectorID' => 'required|exists:users,id', // @TODO: also should not be an SD
            ];
        }
        return $rector + [
            'weekend_full_name'             => 'required|max:80',
            'weekend_number'                => 'required|numeric',
            'weekend_MF'                    => 'required|in:M,m,W,w,F,f',
            'tresdias_community'            => 'required|max:20',
            'start_date'                    => 'required|date',
            'end_date'                      => 'required|date|after:start_date',
            'candidate_arrival_time'        => 'required|date',
            'sendoff_start_time'            => 'required|date',
            'serenade_arrival_time'         => 'required|date|before:end_date',
            'serenade_scheduled_start_time' => 'date|after:serenade_arrival_time',
            'closing_arrival_time'          => 'required|date|before:end_date',
            'closing_scheduled_start_time'  => 'required|date|before:end_date',
            'weekend_location'              => 'required|max:100',
            'sendoff_location'              => 'nullable|max:100',
            'serenade_practice_location'    => 'nullable|max:255',
            'maximum_candidates'            => 'required|numeric',
            'candidate_cost'                => 'required|numeric',
            'team_fees'                     => 'required|numeric',
            'visibility_flag'               => 'numeric|between:0,10',
            'table_palanca_guideline_text'  => 'nullable|max:255',
            'weekend_verse_reference'       => 'nullable|max:255',
            'weekend_theme'                 => 'nullable|max:255',
            'sendoff_couple_name'           => 'nullable|max:80',
            'emergency_poc_name'            => 'nullable|max:100',
            'emergency_poc_email'           => 'nullable|max:100',
            'emergency_poc_phone'           => 'nullable|max:100',
        ];
    }
}
