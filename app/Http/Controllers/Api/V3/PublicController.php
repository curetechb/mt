<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\StoryGroupResource;
use App\Models\Page;
use App\Models\StoryGroup;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function customPage($slug){

        $page = Page::where('slug', $slug)->first();

        return response([
            "success" => true,
            "data" => $page
        ]);
    }

    public function appCustomPage($slug){

        $page_en =  Page::where('slug', "$slug-english")->first();
        $page_bn =  Page::where('slug', "$slug-bangla")->first();

         return response([
            'result' => true,
            'content_en' => $page_en->content,
            'content_bn' => $page_bn->content,
        ], 200);
    }

    public function storyGroups(){
        $storyGroups = StoryGroup::where("is_active", true)->get();
        return StoryGroupResource::collection($storyGroups);
    }


    public function heroPrivacyPolicy(){


    }
}
