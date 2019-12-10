<?php

namespace App\DataTables;

use App\Events\UserAdded;
use App\User;
use App\Events\UserDeleted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;

class UsersDataTablesEditor extends DataTablesEditor
{
    protected $model = User::class;

    /**
     * Get create action validation rules.
     *
     * @return array
     */
    public function createRules()
    {
        if (!Auth::user()->can('create members')) {
            abort(403, 'Unauthorized add.');
        }

        return [
            'first' => 'required|string|max:191',
            'last' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'weekend' => 'nullable|string|max:25',
            'cellphone' => 'nullable|string|max:191',
            'homephone' => 'nullable|string|max:191',
            'church' => 'nullable|string|max:191',
        ];
//      'email', 'first', 'last', 'address1', 'address2', 'city', 'state', 'postalcode',
//      'homephone', 'cellphone', 'workphone', 'spouseID', 'church', 'weekend',
//      'sponsor', 'sponsorID', 'gender', 'community',
//      'inactive_comments', 'skills', 'avatar', 'username', 'emergency_contact_details'
//    ];
//
//    /** @var array other casts */
//    protected $casts = [
//        'active'                                    => 'boolean',
//        'qualified_sd'                              => 'boolean',
//        'interested_in_serving'                     => 'boolean',
//        'receive_prayer_wheel_invites'              => 'boolean',
//        'receive_prayer_wheel_reminders'            => 'boolean',
//        'receive_email_weekend_general'             => 'boolean',
//        'receive_email_community_news'              => 'boolean',
//        'okay_to_send_serenade_and_palanca_details' => 'boolean',
//        'unsubscribe'                               => 'boolean',
//    ];
    }

    /**
     * Get edit action validation rules.
     *
     * @param Model $model
     * @return array
     */
    public function editRules(Model $model)
    {
        if (!$model) {
            return abort(404);
        }

        if (!Auth::user()->canEditUser($model->id)) {
            abort(403, 'Unauthorized edit');
        }

        return [
            'first' => 'sometimes|string|max:191',
            'last' => 'sometimes|string|max:191',
            'email' => 'sometimes|email|max:191',
            'weekend' => 'sometimes|string|max:25',
            'cellphone' => 'sometimes|string|max:191',
            'homephone' => 'sometimes|string|max:191',
            'church' => 'sometimes|string|max:191',
        ];
    }

    /**
     * Get remove action validation rules.
     *
     * @param Model $model
     * @return array
     */
    public function removeRules(Model $model)
    {
        if (Auth::user()->cannot('delete members')) {
            abort(403, 'Unauthorized edit.');
        }

        if (!$model) {
            abort(204, 'Nothing to delete.');
        }

        return [
        ];
    }
}
