<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('password.confirm')->except(['index', 'show']);
    }

    /**
     * Display a listing of locations
     *
     */
    public function index()
    {
        $locations = Location::all();

        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location
     *
     */
    public function create()
    {
        abort_unless(auth()->user()->can('edit locations'), '403', 'Must be an administrator to edit locations.');

        $location = new Location;

        return view('locations.create', \compact('location'));
    }

    /**
     * Store a newly created location
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('edit locations'), '403', 'Must be an administrator to edit locations.');

        $validated = $this->validate($request, [
            'location_name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:80',
            'location_url' => 'nullable|string|max:250',
            'address_street' => 'nullable|string|max:80',
            'address_city' => 'nullable|string|max:80',
            'address_province' => 'nullable|string|max:80',
            'address_postal' => 'nullable|string|max:20',
            'map_url_link' => 'nullable|string|max:250',
            'contact_name' => 'nullable|string|max:80',
            'contact_email' => 'nullable|string|max:80',
            'contact_phone' => 'nullable|string|max:80',
        ]);

        $location = Location::create($validated);

        flash()->success('Location: ' . $location->location_name . ' added.');

        return redirect(route('location.index'));
    }

    /**
     * Display the specified location
     *
     * @param  int  $id
     */
    public function show($id)
    {
        if (\is_numeric($id)) {
            $location = Location::find($id);
        } else {
            $location = Location::firstWhere('slug', $id);
        }

        \abort_if($location === null, '404', 'Sorry, that page could not be found.');

        return view('locations.show', \compact('location'));
    }

    /**
     * Show the form for editing the specified location
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        abort_unless(auth()->user()->can('edit locations'), '403', 'Must be an administrator to edit locations.');

        if (\is_numeric($id)) {
            $location = Location::find($id);
        } else {
            $location = Location::firstWhere('slug', $id);
        }

        return view('locations.edit', \compact('location'));
    }

    /**
     * Update the specified location
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->can('edit locations'), '403', 'Must be an administrator to edit locations.');

        if (\is_numeric($id)) {
            $location = Location::find($id);
        } else {
            $location = Location::firstWhere('slug', $id);
        }

        $validated = $this->validate($request, [
            'location_name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:80',
            'location_url' => 'nullable|string|max:250',
            'address_street' => 'nullable|string|max:80',
            'address_city' => 'nullable|string|max:80',
            'address_province' => 'nullable|string|max:80',
            'address_postal' => 'nullable|string|max:20',
            'map_url_link' => 'nullable|string|max:250',
            'contact_name' => 'nullable|string|max:80',
            'contact_email' => 'nullable|string|max:80',
            'contact_phone' => 'nullable|string|max:80',
        ]);

        if ($location) {
            $location->update($validated);
            $location->save();
            flash()->success('Location: ' . $location->location_name . ' updated.');
        } else {
            flash()->error('Could not update the location. (Not Found)');
        }

        return redirect(route('location.index'));
    }

    /**
     * Remove the specified location
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete locations'), '403', 'Must be an administrator to delete locations.');

        $location = Location::find($id);

        if ($location) {
            $location->delete();
            flash('Location deleted.', 'success');
        }

        return redirect(route('location.index'));
    }
}
