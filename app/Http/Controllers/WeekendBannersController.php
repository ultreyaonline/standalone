<?php

namespace App\Http\Controllers;

use App\Models\Weekend;
use Illuminate\Http\Request;

class WeekendBannersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request, string $type = null)
    {
        // The route definition should enforce this already, but checking anyway to prevent errors
        if (!in_array($type, ['men', 'women'])) {
            return redirect()->route('palanca');
        }

        if ($type === 'men') {
            $title_banner_type = "Men's Weekend";
            $weekend_mf = 'M';
        }

        if ($type === 'women') {
            $title_banner_type = "Women's Weekend";
            $weekend_mf = 'W';
        }

        $banners = Weekend::local() // only include weekends for the current community (because we don't have the banners from other communities)
            ->active($request->user()->id) // exclude weekends that are hidden to the current user
            ->where('weekend_MF', $weekend_mf) // match gender
            ->get()
            ->reject(function ($b) {
                return empty($b->banner_url); // ignore empty banners by using the model's mutators so the Media Library can respond with its records
            });

        return view('palanca.weekend_banners', compact('banners', 'title_banner_type', 'type'));
    }

    /**
     * @param Request $request
     * @param Weekend $weekend
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateBannerPhoto(Request $request, Weekend $weekend)
    {
        if (!$weekend) {
            return redirect('/weekend/'. $weekend);
        }

        if (! $request->user()->can('edit weekends') && ! $weekend->rectorID == $request->user()->id) {
            abort(404);
        }

        $this->validate($request, [
            'banner_url' => 'mimes:jpg,jpeg,png,gif,bmp'
        ], [
            'image' => 'The photo must be a valid image file (PNG JPG GIF formats allowed)',
        ]);

        $file = request()->file('banner_url');
        if ($file) {
            $weekend->addMedia($file)->toMediaCollection('banner');
            flash()->success('Photo updated.');
        }
        return redirect('/weekend/'. $weekend->id);
    }

    public function deleteBannerPhoto(Request $request, Weekend $weekend)
    {
        if (!$weekend) {
            return redirect('/weekend/'. $weekend);
        }

        if (! $request->user()->can('edit weekends') && ! $weekend->rectorID == $request->user()->id) {
            abort(404);
        }

        $weekend->clearMediaCollection('banner');

        flash()->success('Photo deleted.');

        return redirect('/weekend/'. $weekend->id);
    }
}
