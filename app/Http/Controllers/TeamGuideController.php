<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamGuideController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Require login
        $this->middleware('auth');
    }


    ////// @TODO -- note: this is currently NOT USED

    /**
     * Display the requested TeamGuide page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($page, $id)
    {
        if ($page =='' || $page == '/') {
            $page = 'main';
        }
        return view('teamguide.'.$page, ['weekendid' => $id ]);
    }
}
