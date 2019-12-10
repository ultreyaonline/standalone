<?php

namespace App\Http\Controllers;

use App\Weekend;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WeekendStatsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('role:Member');
    }

    public function index(Request $request)
    {
        $weekendsByYear = Weekend::query()
            ->local()
            ->ended()
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->start_date)->format('Y');
            });

        return view('admin.weekend_stats', ['weekendsByYear'=>$weekendsByYear]);
    }
}
