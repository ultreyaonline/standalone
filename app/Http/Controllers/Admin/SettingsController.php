<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * @var \Illuminatech\Config\PersistentRepository persistent config repository, which is set at `ConfigServiceProvider`.
     */
    private $config;

    public function __construct(Container $app)
    {
        $this->middleware('auth');
        $this->middleware('role:Admin|Super-Admin');

        parent::__construct();

        $this->config = $app->get('config');
    }

    public function index()
    {
        $this->config->restore(); // ensure config values restored from database

        return view('settings.edit', ['settings' => $this->config->getItems()]);
    }

    public function update(Request $request)
    {
        $validatedData = $this->config->validate($request->all());

        $this->config->save($validatedData);

        return redirect(route('admin'))->with('status', 'Settings Saved.');
    }

    public function restoreDefaults()
    {
        $this->config->reset();

        return back()->with('status', 'Settings Restored from Defaults');
    }

}
