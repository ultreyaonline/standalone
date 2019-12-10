<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Contracts\Pagination\Paginator;

class ActivitylogController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('role:Admin|Super-Admin');
    }

    public function index()
    {
        $logItems = $this->getPaginatedActivityLogItems();
        return view('admin.activitylog')->with(compact('logItems'));
    }

    protected function getPaginatedActivityLogItems(): Paginator
    {
        return Activity::with('causer')
            ->orderBy('created_at', 'DESC')
            ->paginate(100);
    }
}
