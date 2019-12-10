<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // check whether the logged-in user has permissions to do the attempted action
        return Auth::user()->canEditUser($this->id);

        if (Auth::user()->can('add candidates')) {
            return true;
        }
        if (Auth::user()->can('edit candidates')) {
            return true;
        }
        if (Auth::user()->can('add community member')) {
            return true;
        }
        if (Auth::user()->can('edit community roster')) {
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'                      => 'email|required',
            'gender'                     => 'in:m,w,M,W,f,F',
            'first'                      => 'required',
            'last'                       => 'required',
            'address1'                   => 'alpha_num',
            'address2'                   => 'alpha_num',
            'city'                       => 'alpha',
            'state'                      => 'alpha',
            'postalcode'                 => 'alpha_num',
            'homephone'                  => 'regex:\D{3}-\D{3}-\D{4}',
            'cellphone'                  => 'regex:\D{3}-\D{3}-\D{4}',
            'church'                     => 'alpha',
            'weekend'                    => 'alpha_num',
            'sponsor'                    => 'alpha',
            'sponsorID'                  => 'numeric|exists:users,id',
            'interested_in_serving'      => 'boolean',
            'active'                     => 'boolean',
            'qualified_sd'               => 'boolean',
        ];
    }
}
