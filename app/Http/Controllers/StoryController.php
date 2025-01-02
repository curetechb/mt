<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{

    public function index()
    {
        $stories = Story::where("story_group_id", request("storygroup"))->get();
        return view("backend.stories.index", compact("stories"));
    }


    public function create()
    {
        return view("backend.stories.create");
    }


    public function store(Request $request)
    {

        // $request->validate([
        //     'thumbnail_img' => ['required'],
        //     'story_group_id' => ['required'],
        //     'type' => ['required'],
        //     'duration' => ['required']
        // ]);

        $story = new Story();
        $story->upload_id = $request->thumbnail_img;
        $story->story_group_id = $request->story_group_id;
        $story->type = $request->type;
        $story->duration = $request->duration;
        $story->navigation_url = $request->navigation_url;
        $story->is_active = $request->is_active ? true : false;
        $story->show_on_front = $request->show_on_front ? true : false;
        $story->save();

        return redirect()->route('stories.index', ['storygroup' => $request->story_group_id]);
    }


    public function show($id)
    {
        $story = Story::find($id);
        $story->delete();

        return redirect()->route('stories.index');
    }


    public function edit($id)
    {
        $story = Story::find($id);
        return view("backend.stories.edit", compact("story"));
    }


    public function update(Request $request, $id)
    {

        $story = Story::find($id);
        $story->upload_id = $request->thumbnail_img;
        // $story->story_group_id = $request->story_group_id;
        $story->type = $request->type;
        $story->duration = $request->duration;
        $story->navigation_url = $request->navigation_url;
        $story->is_active = $request->is_active ? true : false;
        $story->show_on_front = $request->show_on_front ? true : false;
        $story->save();

        return redirect()->route('stories.index', ['storygroup' => $request->story_group_id]);
    }


    public function destroy($id)
    {
        $story = Story::find($id);
        $story->delete();

        return redirect()->route('stories.index', ['storygroup' => $id]);
    }
}
