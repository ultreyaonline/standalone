<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DataExportController extends Controller
{
    public function __construct(User $user)
    {
        parent::__construct();
        $this->middleware(['auth', 'password.confirm']);
    }

    public function membersExportSelection(Request $request)
    {
        abort_unless($request->user()->can('export member data'), '403', 'ERROR: Not authorized to export data.');

        return view('reports.export_members_selection');
    }

    public function memberDataAsCsv(Request $request)
    {
        abort_unless($request->user()->can('export member data'), '403', 'ERROR: Not authorized to export data.');

        $query = User::select('*')
            // weighted to show local community members first, and then other communities afterward
            ->selectRaw('(community=?) as local_community_first', [config('site.community_acronym')]);

        if ($request->input('service_history', 'no') === 'yes') {
            $query = $query->with(['weekendAssignments', 'weekendAssignmentsExternal'])
                ->withCount('weekendAssignments')
                ->withCount('weekendAssignmentsExternal');
        }

        if ($request->has('filter_gender') && \in_array($request->input('filter_gender'), ['M', 'W'], false)) {
            $gender = strtoupper($request->input('filter_gender'));
            $query = $query->where('gender', $gender);
        }

        if ($request->input('interactive') !== 'yes' || ($request->input('community_local', 'no') === 'local' && $request->input('community_other', 'no') === 'no')) {
            $query = $query->onlyLocal();
        }
        if ($request->input('community_local', 'no') === 'no' && $request->input('community_other', 'no') === 'other') {
            $query = $query->onlyNonlocal();
        }

        if ($request->input('interactive') !== 'yes' || ($request->input('filter_active', 'na') === 'active' && $request->input('filter_inactive', 'na') === 'na')) {
            $query = $query->active();
        }
        if ($request->input('filter_active', 'na') === 'na' && $request->input('filter_inactive', 'na') === 'inactive') {
            $query = $query->inactive();
        }

        $members = $query
            ->orderBy('local_community_first', 'DESC') // sorted by local-first, then others
            ->orderBy('last')
            ->orderBy('first')
            ->get();


        $columns = [];
        $columns[] = 'name'; // getter attribute combines first and last into 'name'
        $columns[] = 'weekend';
        $columns[] = 'email';

        if ($request->input('phone') === 'yes') {
            $columns[] = 'phone';
        }

        if ($request->input('church') === 'yes' || $request->input('interactive') !== 'yes') {
            $columns[] = 'church';
        }

        if ($request->input('address') === 'yes' || $request->input('interactive') !== 'yes') {
            $columns[] = 'address1';
            $columns[] = 'address2';
            $columns[] = 'city';
            $columns[] = 'state';
            $columns[] = 'postalcode';
            $columns[] = 'country';
        }

        if ($request->input('community') === 'yes') {
            $columns[] = 'community';
        }

        if ($request->input('gender') === 'yes') {
            $columns[] = 'gender';
        }

        if ($request->input('sponsor') === 'yes') {
            $columns[] = 'sponsor';
        }

        if ($request->input('prefs_email') === 'yes') {
            $columns[] = 'receive_email_weekend_general';
            $columns[] = 'receive_email_community_news';
            $columns[] = 'receive_email_sequela';
            $columns[] = 'receive_email_reunion';
            $columns[] = 'receive_prayer_wheel_invites';
            $columns[] = 'receive_prayer_wheel_reminders';
        }

        if ($request->input('active_status') === 'yes') {
            $columns[] = 'active';
            $columns[] = 'inactive_comments';
            $columns[] = 'unsubscribe';
            $columns[] = 'unsubscribe_date';
        }

        if ($request->input('interested_in_serving') === 'yes') {
            $columns[] = 'interested_in_serving';
        }

        if ($request->input('service_history') === 'yes') {
            $columns[] = 'service_history';
        }

        if ($request->input('extras') === 'yes') {
            $columns[] = 'allow_address_share';
            $columns[] = 'last_login_at';
            $columns[] = 'spousename';
        }


        $csvData = [];
        $headings_array = [];
        $headings_have_been_set = false;
        $headings = '';

        foreach($members as $member) {
            $row = [];
            foreach($columns as $column) {
                $data = '';
                $heading = '';
                switch ($column) {
                    case 'name':
                        $data = $member->name;
                        if (! \in_array('community', $columns, false)) {
                            // also includes community name in this field if community not requested as specific column
                            $data .= $member->community !== config('site.community_acronym') ? ' (' . preg_replace('/[^\w\s#]/', '', $member->community) . ')' : '';
                        }
                        break;
                    case 'phone':
                        // note: $member->phone combines home/cell/work into one field
                        $data = \str_replace('&nbsp;', ' ', trim($member->phone));
                        $heading = 'Phone Numbers';
                        break;

                    case 'service_history':
                        // convert service data to semicolon-delimited data
                        $service = [];
                        foreach($member->serving_history as $h) {
                            $service[] = str_replace("'", '', $h['name']) . ': ' . $h['position'];
                        }
                        $data = implode('; ', $service);
                        $heading = 'Service History';
                        break;

                    default:
                        $data = $member->{$column};
                }

                if (empty($heading)) {
                    $heading = Str::title($column);
                }
                if ($headings_have_been_set !== true) {
                    $headings_array[] = '"' . $this->formatForCsv($heading) . '"';
                }

                $row[] = '"' . $this->formatForCsv($data) . '"';
            }
            if ($headings_have_been_set !== true) {
                $headings_have_been_set = true;
                $headings = implode(',', $headings_array);
            }
            $csvData[] = implode(',', $row);
        }

        return response(view('reports.export_members_csv', compact('headings', 'csvData')), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . config('site.community_acronym') . '-member-data-' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Converts double-quotes to double-double-quotes for CSV purposes
     * Other escaping isn't required since all fields are delimeted with double-quotes.
     *
     * @param string $string
     * @return string
     */
    protected function formatForCsv(?string $string)
    {
        if ($string === null) return '';

        return \str_replace(['"'], ['""'], $string);
    }
}
