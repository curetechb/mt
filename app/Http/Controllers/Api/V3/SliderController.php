<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class SliderController extends Controller
{
    public function index()
    {
        $sliders = json_decode(get_setting('home_slider_images'), true);
        $images = [];
        foreach ($sliders as $slider) {
            $img = env("AWS_URL")."/".api_asset($slider);
            array_push($images, $img);
        }

        return response()->json([
            "data" => $images
        ], 200);
    }
}
