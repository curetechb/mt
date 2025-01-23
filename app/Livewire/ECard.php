<?php

namespace App\Livewire;

use Livewire\Component;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\WithFileUploads;

class ECard extends Component
{

    use WithFileUploads;

    public $loading = false;
    public $output = "";
    public $occasion = "eid";
    public $to = "";    
    public $from = "";    
    public $image = "";
    public $images = [
        "eid" => "livewire/greetings/eid-mubarak.jpg",
        "ramadan" => "livewire/greetings/ramadan-mubarak.jpg",
        "Anniversary" => "livewire/greetings/anniversary.jpg",
        "get-well" => "livewire/greetings/getwell.jpg",
        "thank-you" => "livewire/greetings/thank-you.jpg",
        "congratulation" => "livewire/greetings/congratulations.jpg",
    ];
    public $error = "";

    public function mount(){
        $this->image = asset("livewire/greetings/eid-mubarak.jpg");
    }

    public function render()
    {
        return view('livewire.e-card');
    }

    public function updateGreetingCard(){
        
        $this->render();
    }

    public function updateOccasion(){
        
        $this->image = $this->images[$this->occasion];
        $this->output = "";
        $this->render();
    }


    public function generateGreetingCard()
    {

        if(!$this->to){
            $this->error = "Please fill up the To field.";
            return;
        }
        $this->error = "";

        $this->loading = true;

        $parts = explode("/", $this->images[$this->occasion]);
        $image_name = end($parts);

        // Load the image (use your desired image path or URL)
        $imagePath = public_path("livewire/greetings/$image_name"); // Replace with your image file
        $image = Image::read($imagePath);
        

        if($image_name == "anniversary.jpg"){
            // Add text on top of the image
            if($this->to){
                $image->text($this->to, $image->width() / 2, $image->height() / 2 - 700, function ($font) use($image) {
                    $font->file(public_path('fonts/GreatVibes-Regular.ttf')); // Optional: Load a custom font
                    $font->size(290);                            // Font size
                    $font->color('#AEAA69');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }

            if($this->from){
                // Add text on top of the image
                $image->text($this->from, $image->width() / 2, $image->height() / 2 + 700, function ($font) use($image) {
                    $font->file(public_path('fonts/GreatVibes-Regular.ttf')); // Optional: Load a custom font
                    $font->size(290);                            // Font size
                    $font->color('#AEAA69');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }
        }else if($image_name == "getwell.jpg"){
            // Add text on top of the image
            if($this->to){
                $image->text($this->to, $image->width() / 2, $image->height() / 2 - 245, function ($font) use($image) {
                    $font->file(public_path('fonts/MyriadPro-Regular.ttf')); // Optional: Load a custom font
                    $font->size(240);                            // Font size
                    $font->color('#536558');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }

            // Add text on top of the image
            if($this->from){
                $image->text($this->from, $image->width() / 2, $image->height() / 2 + 750, function ($font) use($image) {
                    $font->file(public_path('fonts/MyriadPro-Regular.ttf')); // Optional: Load a custom font
                    $font->size(240);                            // Font size
                    $font->color('#536558');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }
        }else if($image_name == "thank-you.jpg"){
            // Add text on top of the image
            if($this->to){
                $image->text($this->to, $image->width() / 2, $image->height() / 2 - 230, function ($font) use($image) {
                    $font->file(public_path('fonts/MyriadPro-Regular.ttf')); // Optional: Load a custom font
                    $font->size(240);                            // Font size
                    $font->color('#536558');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }

            // Add text on top of the image
            if($this->from){
                $image->text($this->from, $image->width() / 2, $image->height() / 2 + 650, function ($font) use($image) {
                    $font->file(public_path('fonts/MyriadPro-Regular.ttf')); // Optional: Load a custom font
                    $font->size(240);                            // Font size
                    $font->color('#536558');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }
        }
        else if($image_name == "congratulations.jpg"){
            // Add text on top of the image
            if($this->to){
                $image->text($this->to, $image->width() / 2, $image->height() / 2 - 205, function ($font) use($image) {
                    $font->file(public_path('fonts/MyriadPro-Regular.ttf')); // Optional: Load a custom font
                    $font->size(240);                            // Font size
                    $font->color('#536558');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }

            // Add text on top of the image
            if($this->from){
                $image->text($this->from, $image->width() / 2, $image->height() / 2 + 650, function ($font) use($image) {
                    $font->file(public_path('fonts/MyriadPro-Regular.ttf')); // Optional: Load a custom font
                    $font->size(240);                            // Font size
                    $font->color('#536558');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }
        }else if($image_name == "eid-mubarak.jpg"){
            // Add text on top of the image
            if($this->to){
                $image->text($this->to, $image->width() / 2, $image->height() / 2 - 100, function ($font) use($image) {
                    $font->file(public_path('fonts/Hikerstone-Slant.ttf')); // Optional: Load a custom font
                    $font->size(240);                            // Font size
                    $font->color('#000000');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }

            // Add text on top of the image
            if($this->from){
                $image->text($this->from, $image->width() / 2, $image->height() / 2 + 930, function ($font) use($image) {
                    $font->file(public_path('fonts/Hikerstone-Slant.ttf')); // Optional: Load a custom font
                    $font->size(240);                            // Font size
                    $font->color('#000000');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }
        }else if($image_name == "ramadan-mubarak.jpg"){
            // Add text on top of the image
            if($this->to){
                $image->text($this->to, $image->width() / 2, $image->height() / 2 - 30, function ($font) use($image) {
                    $font->file(public_path('fonts/Hikerstone-Slant.ttf')); // Optional: Load a custom font
                    $font->size(220);                            // Font size
                    $font->color('#000000');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }

            if($this->from){
                // Add text on top of the image
                $image->text($this->from, $image->width() / 2, $image->height() / 2 + 930, function ($font) use($image) {
                    $font->file(public_path('fonts/Hikerstone-Slant.ttf')); // Optional: Load a custom font
                    $font->size(220);                            // Font size
                    $font->color('#000000');                    // Font color
                    $font->align('center');                     // Horizontal alignment
                    $font->valign('middle');                    // Vertical alignment
                });
            }
        }


        // Encode the image to base64
        $base64Image = $image->toJpeg()->toDataUri(); // Correct way

        $this->output = $base64Image;
        // Save or Download the image
        // $image->save(public_path('output.jpg')); // Save the image to the server

        // Alternatively, to download the image
        // return response()->make($image->encode('jpg'), 200, [
        //     'Content-Type' => 'image/jpeg',
        //     'Content-Disposition' => 'attachment; filename="custom-image.jpg"',
        // ]);
        $this->loading = false;
    }
}
