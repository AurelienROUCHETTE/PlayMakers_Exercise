<?php

/**
 * Verify if the badge meets the specified requirements.
 *
 * @param string $image_path The path to the image file.
 * @return array Returns true if image requirements are met and a success message, otherwise false and an error message, at each step. An array indicating if the badge is valid along with a message.
 */
function verify_badge($image_path)
{
    // Load the image
    $image = imagecreatefrompng($image_path);
    if (!$image) {
        return [false, "Oops! Unable to load the image. 😞"];
    }

    // Get image dimensions
    $width = imagesx($image);
    $height = imagesy($image);

    // Check image size pixels
    if ($width == 512 && $height == 512) {
        return [true, "Yay! The image size is 512x512 pixels. 🎉"];
    } else {
        return [false, "Oopsy! The image size is not 512x512 pixels. 🤔"];
    }

    // Check non-transparent pixels within a circle
    $centerX = $width / 2;
    $centerY = $height / 2;
    $radius = 256;
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $alpha = (imagecolorat($image, $x, $y) >> 24) & 0x7F;
            $distance = sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2));
            if ($distance > $radius && $alpha < 127) {
                return [false, "Oopsy! There are non-transparent pixels outside the circle. 🤔"];
            }
        }
    }

    return [true, "Yay! All pixels are within the circle. 🎉 😊"];


    // // Check if the image is 512x512 pixels, if not, resize it
    // if ($width != 512 || $height != 512) {
    //     $resized_image = imagecreatetruecolor(512, 512);
    //     imagecopyresampled($resized_image, $image, 0, 0, 0, 0, 512, 512, $width, $height);
    //     imagedestroy($image);
    //     // return [false, "Oopsy! The image size is not 512x512 pixels. 😞"];
    //     $image = $resized_image;
    // }


    // Apply circular mask
    // $radius = 256; // Radius of the circle

    // $center_x = 256; // X-coordinate of the center of the circle
    // $center_y = 256; // Y-coordinate of the center of the circle

    // // Loop through each pixel of the image
    // $happy_color_count = 0;
    // $total_pixels = 0;

    // // Limit the number of pixels displayed to avoid overloading the output
    // $debug_limit = 10;

    // //! Unworking! 
    // for ($y = 0; $y < 512; $y++) {
    //     echo "Valeur de y : $y\n"; 
    //     for ($x = 0; $x < 512; $x++) {
    //         echo "Valeur de x : $x\n"; 
    //         // Calculate the distance from the current pixel to the center of the circle
    //         $distance_from_center = sqrt(abs(pow($x - $center_x, 2)) + abs(pow($y - $center_y, 2)));


    //         // Get the color of the current pixel
    //         $color_index = imagecolorat($image, $x, $y);
    //         $alpha = ($color_index >> 24) & 0x7F;

    //         echo "Pixel à la position ($x, $y) : distance_from_center = $distance_from_center, alpha = $alpha\n";

    //         // Limit the number of pixels displayed to avoid overloading the output
    //         $debug_limit--;
    //         if ($debug_limit <= 0) {
    //             break 2;
    //         }

    //         // Check if the pixel is outside the circle and non-transparent
    //         if ($distance_from_center > $radius && $alpha < 127) {
    //             imagedestroy($image);
    //             return [false, "Oopsy! Some pixels are outside the circle. 🤔"];
    //         }

    //         // If the pixel is non-transparent, check if it gives a "happy" feeling
    //         if ($alpha < 127) { // Non-transparent pixel
    //             $total_pixels++;
    //             $r = ($color_index >> 16) & 0xFF;
    //             $g = ($color_index >> 8) & 0xFF;
    //             $b = $color_index & 0xFF;

    //             // Example condition for "happy" colors: bright and vibrant colors
    //             $brightness = ($r + $g + $b) / 3;
    //             $saturation = max($r, $g, $b) - min($r, $g, $b);

    //             if ($brightness > 150 && $saturation > 100) {
    //                 $happy_color_count++;
    //             }
    //         }
    //     }
    // }

    // imagedestroy($image);

    // // Check if the percentage of "happy" colors is less than 10%
    // if ($happy_color_count / $total_pixels < 0.1) { // Example threshold
    //     return [false, "Oh no! Some colors don't give a happy feeling. 😞"];
    // }

    // return [true, "The badge is valid"];
}

// test of image size pixels
$imagePath = 'img/ff/zack.png';
list($success, $message) = verify_badge($imagePath);
echo "$success: $message";

// test of non-transparent pixels within a circle

// test of colors in the badge

// // Example usage
// list($result, $message) = verify_badge("img/ff/zack.png");
// echo "$result: $message";

/**
 * Note:
 * The pictures, which are contained in the 'img' folder, are ones which I took while playing. Therefore, I am allowed to use them, specifying, if necessary, that the rights belong to Square Enix.
 * I've put in my best effort to complete this exercise. 
 * I conducted extensive research and utilized an AI to discuss various topics and obtain personalized explanations that the documentation doesn't provide.
 * Moreover, I would be grateful if you could bear in mind that I have very little coding experience.
 * Please feel free to provide feedback on the code, indicating what's correct and what's incorrect, as well as any areas for improvement.
 * I've read that Python would have been more suitable for this exercise. Nonetheless, since I've never used this language, I chose to use php, which I'm quite familiar with.
 */
