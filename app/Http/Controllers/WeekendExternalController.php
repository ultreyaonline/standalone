<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WeekendAssignmentsExternal;
use Illuminate\Http\Request;

class WeekendExternalController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('role:Admin|Super-Admin');
        $this->middleware('password.confirm')->only(['destroy']);
    }

    public function create(Request $request)
    {
        $mode = 'create';
        $service = new WeekendAssignmentsExternal(['memberID' => $request->input('id', 0)]);

        // build list for pulldowns // @TODO - if request('id') is not in onlyLocal() scope, do full lookup immediately
        if ($request->withExternal) {
            $this->users = User::active()->orderBy('first')->orderBy('last')->get();
        } else {
            $this->users = User::active()->orderBy('first')->orderBy('last')->onlyLocal()->get();
        }
        $this->users->prepend(User::make(['id' => 0, 'first' => ' Please ', 'last' => ' select']));

        return view('admin.add_external_service', compact('service', 'mode'))->withUsers($this->users);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'memberID' => 'required|numeric|exists:users,id',
            'WeekendName' => 'required',
            'RoleName' => 'required',
        ]);
        WeekendAssignmentsExternal::create($request->only(['memberID', 'WeekendName', 'RoleName']));

        flash()->success('Weekend assignment added.');
        event(
            'WeekendAssignmentAdded',
            ['external' => $request->only(['memberID', 'WeekendName', 'RoleName']), 'by' => $request->user()]
        );

        return redirect('/members/' . (int)$request->input('memberID'));
    }

    public function edit(Request $request, $id)
    {
        $mode = 'edit';
        $service = WeekendAssignmentsExternal::find($id);

        // build list for pulldowns
        if ($request->withExternal) {
            $this->users = User::active()->orderBy('first')->orderBy('last')->get();
        } else {
            $this->users = User::active()->orderBy('first')->orderBy('last')->onlyLocal()->get();
        }
        $this->users->prepend(User::make(['id' => 0, 'first' => ' Please ', 'last' => ' select']));

        return view('admin.add_external_service', compact('service', 'mode'))->withUsers($this->users);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'WeekendName' => 'required',
            'RoleName' => 'required',
        ]);

        $record = WeekendAssignmentsExternal::find($id);

        if (null === $record) {
            flash()->error('Error: record not found');
        }

        $record->update($request->only(['WeekendName', 'RoleName']));
        flash()->success('Weekend assignment updated.');

        return redirect('/members/' . $record->memberID);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = WeekendAssignmentsExternal::find($id);

        if (null === $record) {
            flash()->error('Error: record not found');
        }

        $record->delete();
        flash()->success('Weekend assignment deleted.');

        return redirect('/members/' . $record->memberID);
    }
}
