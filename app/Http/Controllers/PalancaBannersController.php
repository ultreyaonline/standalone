<?php

namespace App\Http\Controllers;

use App\Models\Weekend;
use Illuminate\Http\Request;

class PalancaBannersController extends Controller
{
    public function show(Request $request, string $type = null)
    {
        // Auth
        $member = $request->user();
        if (!$member) {
            return redirect()->route('palanca');
        }

        if (!empty($type) && !in_array($type, ['men', 'women', 'general'])) {
            return redirect()->route('palanca');
        }


        if ($type === 'men') {
            $title_banner_type = "Men's Weekend";
            $weekend_mf = 'M';
            $banners = Weekend::local()->active($request->user()->id)->where('weekend_MF', $weekend_mf)->get();
            return view('palanca.weekend_banners', compact('banners', 'title_banner_type', 'type'));
        }

        if ($type === 'women') {
            $title_banner_type = "Women's Weekend";
            $weekend_mf = 'W';
            $banners = Weekend::local()->active($request->user()->id)->where('weekend_MF', $weekend_mf)->get();
            return view('palanca.weekend_banners', compact('banners', 'title_banner_type', 'type'));
        }

        $type = 'general';
        $title_banner_type = 'General Community';
        // @TODO
        $banners = collect();
        return view('palanca.general_banners', compact('banners', 'title_banner_type', 'type'));
    }
}
