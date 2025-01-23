<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class TextToBmpController extends Controller
{
    public function convertToBmpsss(Request $request)
    {

        // Set content type header to display image
        header('Content-Type: image/bmp');

        $text = $request->input('text');

        // Image dimensions (adjust width as needed)
        $width = 500; // Width in pixels (adjust based on text length)
        $height = 150; // Height in pixels (maximum for 12.7 mm at 300 DPI)

        // Create a blank image
        $image = imagecreate($width, $height);

        // Set background and text colors
        $backgroundColor = imagecolorallocate($image, 255, 255, 255); // White background
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text

        // Set the font file path
        $fontPath = public_path('sf_pro.ttf'); // Replace with your actual font file

        // Font size
        $fontSize = 125; // Adjust font size as needed (in points)

        // Calculate text position
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = abs($bbox[4] - $bbox[0]);
        $textHeight = abs($bbox[5] - $bbox[1]);

        // Center the text within the image
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 + $textHeight;

        // Add the text to the image
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $text);

        // Save the image as BMP
        $filePath = public_path('text_image.bmp');
        imagebmp($image, $filePath);
   
        // Free memory
        imagedestroy($image);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }


    public function convertToBmp(Request $request)
    {
        // Set the image content type
        header('Content-Type: image/png');
        
        // Set text and font properties
        $text = $request->input('text');
        $font = public_path('sf_pro.ttf');  // Provide the path to a valid TTF font file
        $fontSize = 100;  // Set font size to 125
        // $width = 1550;  // Image width
        $width = 500;  // Image width
        $height = 150; // Image height

        // Create the image resource
        $image = imagecreatetruecolor($width, $height);

        // Set background color (white)
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $backgroundColor);

        // Set text color (black)
        $textColor = imagecolorallocate($image, 0, 0, 0);

        // Calculate the bounding box of the text
        $bbox = imagettfbbox($fontSize, 0, $font, $text);  // Get text bounding box
        $textWidth = $bbox[2] - $bbox[0];  // Width of the text
        $textHeight = $bbox[7] - $bbox[1]; // Height of the text

        // Calculate x and y coordinates to center the text
        // $x = ($width - $textWidth) / 2;  // Horizontally center the text
        $x = 0;
        $y = ($height - $textHeight) / 2 + $textHeight + 100; // Vertically center the text

        // Add the text to the image
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $text);

        // Generate a temporary file to store the image
        $filePath = public_path('uploads/generated_image.png');
        imagepng($image, $filePath);

        // Free up memory
        imagedestroy($image);

        // Return the image as a download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }


    public function createImageWithText()
    {

        $image_name = "anniversary.jpg";

        // Load the image (use your desired image path or URL)
        $imagePath = public_path("livewire/greetings/$image_name"); // Replace with your image file
        $image = Image::read($imagePath);
        

        if($image_name == "anniversary.jpg"){
            // Add text on top of the image
            $image->text('Barkat', $image->width() / 2, $image->height() / 2 - 700, function ($font) use($image) {
                $font->file(public_path('fonts/GreatVibes-Regular.ttf')); // Optional: Load a custom font
                $font->size(290);                            // Font size
                $font->color('#000000');                    // Font color
                $font->align('center');                     // Horizontal alignment
                $font->valign('middle');                    // Vertical alignment
            });

            // Add text on top of the image
            $image->text('Selim', $image->width() / 2, $image->height() / 2 + 700, function ($font) use($image) {
                $font->file(public_path('fonts/GreatVibes-Regular.ttf')); // Optional: Load a custom font
                $font->size(290);                            // Font size
                $font->color('#000000');                    // Font color
                $font->align('center');                     // Horizontal alignment
                $font->valign('middle');                    // Vertical alignment
            });
        }else if($image_name == "greeting-2.jpg"){
            // Add text on top of the image
            $image->text('Barkat', $image->width() / 2, $image->height() / 2 - 700, function ($font) use($image) {
                $font->file(public_path('fonts/BirthstoneBounce-Regular.ttf')); // Optional: Load a custom font
                $font->size(290);                            // Font size
                $font->color('#000000');                    // Font color
                $font->align('center');                     // Horizontal alignment
                $font->valign('middle');                    // Vertical alignment
            });

            // Add text on top of the image
            $image->text('Selim', $image->width() / 2, $image->height() / 2 + 700, function ($font) use($image) {
                $font->file(public_path('fonts/BirthstoneBounce-Regular.ttf')); // Optional: Load a custom font
                $font->size(290);                            // Font size
                $font->color('#000000');                    // Font color
                $font->align('center');                     // Horizontal alignment
                $font->valign('middle');                    // Vertical alignment
            });
        }else if($image_name == "eid-mubarak.jpg"){
            // Add text on top of the image
            $image->text('Barkat', $image->width() / 2, $image->height() / 2 - 100, function ($font) use($image) {
                $font->file(public_path('fonts/Hikerstone-Slant.ttf')); // Optional: Load a custom font
                $font->size(240);                            // Font size
                $font->color('#FF0000');                    // Font color
                $font->align('center');                     // Horizontal alignment
                $font->valign('middle');                    // Vertical alignment
            });

            // Add text on top of the image
            $image->text('Salim', $image->width() / 2, $image->height() / 2 + 930, function ($font) use($image) {
                $font->file(public_path('fonts/Hikerstone-Slant.ttf')); // Optional: Load a custom font
                $font->size(240);                            // Font size
                $font->color('#FF0000');                    // Font color
                $font->align('center');                     // Horizontal alignment
                $font->valign('middle');                    // Vertical alignment
            });
        }else if($image_name == "ramadan-mubarak.jpg"){
            // Add text on top of the image
            $image->text('Barkat', $image->width() / 2, $image->height() / 2 - 30, function ($font) use($image) {
                $font->file(public_path('fonts/Hikerstone-Slant.ttf')); // Optional: Load a custom font
                $font->size(220);                            // Font size
                $font->color('#FF0000');                    // Font color
                $font->align('center');                     // Horizontal alignment
                $font->valign('middle');                    // Vertical alignment
            });

            // Add text on top of the image
            $image->text('Salim', $image->width() / 2, $image->height() / 2 + 930, function ($font) use($image) {
                $font->file(public_path('fonts/Hikerstone-Slant.ttf')); // Optional: Load a custom font
                $font->size(220);                            // Font size
                $font->color('#FF0000');                    // Font color
                $font->align('center');                     // Horizontal alignment
                $font->valign('middle');                    // Vertical alignment
            });
        }

        // Save or Download the image
        $image->save(public_path('output.jpg')); // Save the image to the server

        // Alternatively, to download the image
        // return response()->make($image->encode('jpg'), 200, [
        //     'Content-Type' => 'image/jpeg',
        //     'Content-Disposition' => 'attachment; filename="custom-image.jpg"',
        // ]);
    }
}
