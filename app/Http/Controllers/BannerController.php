<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $banners = Banner::all();

        if ($request->routeIs('banners.index')) {
            abort_unless($request->user()->can('manage banners'), '403', 'ERROR: Not authorized to manage banners.');

            return view('palanca.banners_admin_index', compact('banners'));
        }

        return view('palanca.banners_list', compact('banners'));
    }

    /**
     * Show the form for creating/editing a banner.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort_unless($request->user()->can('manage banners'), '403', 'ERROR: Not authorized to manage banners.');

        $banner = new Banner;

        return view('palanca.banners_create', \compact('banner'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->can('manage banners'), '403', 'ERROR: Not authorized to manage banners.');

        $validated = $this->validate($request, [
            'title' => 'required|string',
            'banner_image' => 'required|mimes:jpg,jpeg,png,gif,bmp'
        ], [
            'image' => 'The photo must be a valid image file (PNG JPG GIF formats allowed)',
        ]);

        $banner = Banner::create($validated);

        $file = $request->file('banner_image');
        if ($file && \array_key_exists('banner_image', $validated)) {
            $banner->addMedia($file)->toMediaCollection('banner_general');
        }

        flash()->success('Banner added: ' . $banner->title);

        return redirect(route('banners.index'));
    }

    /**
     * Show the form for editing a banner.
     *
     * @param Request $request
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Banner $banner)
    {
        abort_unless($request->user()->can('manage banners'), '403', 'ERROR: Not authorized to manage banners.');

        return view('palanca.banners_edit', compact('banner'));
    }


    /**
     * Update a banner
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Banner $banner
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Banner $banner)
    {
        abort_unless($request->user()->can('manage banners'), '403', 'ERROR: Not authorized to manage banners.');

        $validated = $this->validate($request, [
            'title' => 'required|string',
            'banner_image' => 'required|mimes:jpg,jpeg,png,gif,bmp'
        ], [
            'image' => 'The photo must be a valid image file (PNG JPG GIF formats allowed)',
        ]);

        if ($banner) {
            $banner->update($validated);
            $banner->save();

            $file = $request->file('banner_image');
            if ($file && \array_key_exists('banner_image', $validated)) {
                $banner->addMedia($file)->toMediaCollection('banner_general');
            }
            flash()->success('Banner updated: ' . $banner->title);
        }

        return redirect(route('banners.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Banner $banner
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request, Banner $banner)
    {
        abort_unless($request->user()->can('manage banners'), '403', 'ERROR: Not authorized to delete banners.');

        if (!$banner->id) {
            flash()->error('Unable to determine which record to delete.');
            return redirect()->route('banners.index');
        }

        $message = 'Banner deleted: ' . $banner->title;

        $banner->delete();
        // Note: Model already takes care of deleting the MediaCollection entry as part of the 'deleting' Eloquent event declared on the Model.

        flash()->success($message);

        return redirect()->route('banners.index');
    }
}
