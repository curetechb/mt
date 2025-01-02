<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\StoryGroup;
use Illuminate\Http\Request;

class StoryGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $storygroups = StoryGroup::all();
        return view("backend.storygroup.index", compact("storygroups"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("backend.storygroup.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            "name" => ["required"]
        ]);

        $storygroup = new StoryGroup();
        // $storygroup->upload_id = $request->thumbnail_img;
        $storygroup->name = $request->name;
        $storygroup->is_active = $request->is_active ? true : false;
        $storygroup->save();

        return redirect()->route("story-group.index");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $storyGroup = StoryGroup::findOrFail($id);
        $storyGroup->delete();

        return redirect()->route("story-group.index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $storyGroup = StoryGroup::findOrFail($id);
        return view("backend.storygroup.edit", compact("storyGroup"));
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

        $request->validate([
            "name" => ["required"]
        ]);

        $storyGroup = StoryGroup::findOrFail($id);
        // $storyGroup->upload_id = $request->thumbnail_img;
        $storyGroup->name = $request->name;
        $storyGroup->is_active = $request->is_active ? true : false;
        $storyGroup->save();
        return redirect()->route("story-group.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
