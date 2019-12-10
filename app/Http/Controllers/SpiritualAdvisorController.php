<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class SpiritualAdvisorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    public function serviceHistoryForSpiritualDirectors(Request $request)
    {
        $user = $request->user();
        \abort_unless($user->can('use leaders worksheet') || $user->can('use rectors tools') || $user->can('make SD assignments') || $user->can('view SD history'), 403);


        $sd_list = User::select('*')
            ->selectRaw('(community=?) as weight', [config('site.community_acronym')])
            ->where('qualified_sd', 1)
            ->role('Member')
            ->active()
            ->orderBy('weight', 'DESC')
            ->orderBy('last')->orderBy('first')
            ->get();

        return view('reports.sd_service_history', compact('sd_list'));
    }
}
