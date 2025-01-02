<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RoleTranslation;
use Session;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::paginate(10);
        return view('backend.staff.staff_roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.staff.staff_roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('permissions')){
            $role = new Role;
            $role->name = $request->name;
            $role->permissions = json_encode($request->permissions);
            $role->save();

            $role_translation = RoleTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'role_id' => $role->id]);
            $role_translation->name = $request->name;
            $role_translation->save();

            Session::flash("success",translate('Role has been inserted successfully'));
            return redirect()->route('roles.index');
        }
        Session::flash("error",translate('Something went wrong'));
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $role = Role::findOrFail($id);
        return view('backend.staff.staff_roles.edit', compact('role','lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if($request->has('permissions')){
            if($request->lang == env("DEFAULT_LANGUAGE")){
                $role->name = $request->name;
            }
            $role->permissions = json_encode($request->permissions);
            $role->save();

            $role_translation = RoleTranslation::firstOrNew(['lang' => $request->lang, 'role_id' => $role->id]);
            $role_translation->name = $request->name;
            $role_translation->save();

            Session::flash("success",translate('Role has been updated successfully'));
            return redirect()->route('roles.index');
        }
        Session::flash("error",translate('Something went wrong'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        foreach ($role->role_translations as $key => $role_translation) {
            $role_translation->delete();
        }

        Role::destroy($id);
        Session::flash("success",translate('Role has been deleted successfully'));
        return redirect()->route('roles.index');
    }
}
