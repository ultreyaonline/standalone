<?php

namespace App\Http\Controllers;

class GuestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Guest Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders the "public-facing" static pages
    */

    /**
     * Home page for guests (ie: not logged in)
     */
    public function index()
    {
        // NOTE: same as HomeController@index when not logged in
        return view('public.home');
    }

    /**
     * About Us
     */
    public function about()
    {
        return view('public.about');
    }
    /**
     * Statement of Faith / What We Believe
     */

    public function believe()
    {
        return view('public.believe');
    }

    /**
     * History page
     */
    public function history()
    {
        return view('pages.static.history_of_tres_dias');
    }
}
