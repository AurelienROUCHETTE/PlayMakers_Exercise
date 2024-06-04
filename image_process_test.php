<?php

/**
 * Check that the image size is 512x512 pixels.
 *
 * @param string $image_path The path of the image to check.
 * @return bool|string Returns true if the image size is correct, otherwise a string with an error message.
 */
function check_image_size($image_path)
{
    list($width, $height) = getimagesize($image_path);
    if ($width == 512 && $height == 512) {
        return true;
    } else {
        return "Oopsy! The image size is not 512x512 pixels. 😞";
    }
}

/**
 * Check that all non-transparent pixels are within a circle.
 *
 * @param string $image_path The path of the image to check.
 * @return string Returns a success message if all non-transparent pixels are within the circle, otherwise an error message.
 */
function check_pixels_within_circle($image_path)
{
    $img = imagecreatefrompng($image_path);
    if (!$img) {
        return "Oops! Unable to load the image. 😞";
    }

    // Set the circle parameters
    $radius = 256;
    $center_x = 256;
    $center_y = 256;

    // Iterate over every pixel in the image
    for ($y = 0; $y < 512; $y++) {
        for ($x = 0; $x < 512; $x++) {
            // Calculate the distance between the pixel and the center of the circle
            $distance = sqrt(pow($x - $center_x, 2) + pow($y - $center_y, 2));
            // Get the alpha component of the pixel
            $alpha = (imagecolorat($img, $x, $y) >> 24) & 0x7F;
            
             // Debugging: Output pixel coordinates to a log file
             $file = fopen("pixel_debug.log", "a");
             fwrite($file, "Pixel coordinates: ($x, $y)\n");
             fclose($file);

            // Check if the pixel is outside the circle and not transparent
            if ($distance > $radius && $alpha < 127) {
                return "Oopsy! Some pixels are outside the circle. 🤔";
            }
        }
    }
    return "Great! All pixels are within the circle. 🎉";
}

/**
 * Check if a color gives a "happy" feeling.
 *
 * @param int $color The color value (in hexadecimal).
 * @return bool Returns true if the color gives a "happy" feeling, otherwise false.
 */
function check_happy_color($color)
{
    // Extract the RGB components from the color value
    $r = ($color >> 16) & 0xFF;
    $g = ($color >> 8) & 0xFF;
    $b = $color & 0xFF;

    // Normalize RGB values to range [0, 1]
    $r /= 255.0;
    $g /= 255.0;
    $b /= 255.0;

    // Calculate the HSV components
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $v = $max;
    $delta = $max - $min;

    // Calculate saturation
    if ($max != 0) {
        $s = $delta / $max;
    } else {
        $s = 0;
    }

    // Define thresholds for brightness and saturation
    $brightness_threshold = 0.6;
    $saturation_threshold = 0.4;

    // Check if the color meets the criteria for a "happy" feeling
    return $v > $brightness_threshold && $s > $saturation_threshold;
}

/**
 * Check that all colors in the image give a "happy" feeling.
 *
 * @param string $image_path The path of the image to check.
 * @return string Returns a success message if all colors give a "happy" feeling, otherwise an error message.
 */
function check_happy_colors($image_path)
{
    $img = imagecreatefrompng($image_path);
    if (!$img) {
        return "Oops! Unable to load the image. 😞";
    }

    // Iterate over every pixel in the image
    for ($y = 0; $y < 512; $y++) {
        for ($x = 0; $x < 512; $x++) {
            // Get the color of the pixel
            $color = imagecolorat($img, $x, $y);
            // Check if the color gives a "happy" feeling
            if (!check_happy_color($color)) {
                return "Oh no! Some colors don't give a happy feeling. 😞";
            }
        }
    }
    return "Hooray! All colors give a happy feeling. 🎉";
}


/**
 * Convert the given image to a badge object with specified criteria.
 *
 * @param string $image_path The path of the image to convert.
 * @return mixed Returns the badge object if successful, otherwise a string with an error message.
 */
function convert_image_to_badge($image_path)
{
    // Step 1: Check image size
    if (!check_image_size($image_path)) {
        return "Oops! The image size is not 512x512 pixels. 😞";
    }

    // Step 2: Check pixels within circle
    if (!check_pixels_within_circle($image_path)) {
        return "Oops! Some pixels are outside the circle. 🤔";
    }

    // Step 3: Check happy colors
    if (!check_happy_colors($image_path)) {
        return "Oops! Some colors don't give a happy feeling. 😞";
    }

    // Load the original image
    $original_img = imagecreatefrompng($image_path);
    if (!$original_img) {
        return "Oops! Unable to load the image. 😞";
    }

    // Create a blank canvas for the badge object
    $badge = imagecreatetruecolor(512, 512);
    // Set background color to white
    $white = imagecolorallocate($badge, 255, 255, 255);
    imagefill($badge, 0, 0, $white);

    // Set the circle parameters
    $radius = 256;
    $center_x = 256;
    $center_y = 256;

    echo "Center X: $center_x, Center Y: $center_y, Radius: $radius";

    echo "Before loop";

    // Copy the original image onto the badge canvas, only within the circle
    for ($y = 0; $y < 512; $y++) {
        for ($x = 0; $x < 512; $x++) {
            // Calculate the distance between the pixel and the center of the circle
            $distance = sqrt(pow($x - $center_x, 2) + pow($y - $center_y, 2));
            // Check if the pixel is within the circle
            if ($distance <= $radius && $x >= 0 && $x < 512 && $y >= 0 && $y < 512) {
                // Get the color of the pixel from the original image
                $color = imagecolorat($original_img, $x, $y);
                // Copy the pixel to the badge canvas
                // imagesetpixel($badge, $x, $y, $color);
            }
        }
    }

    echo "After loop";

    // Free memory
    imagedestroy($original_img);

    // Return the badge object
    return $badge;
}

// Code test
$image_path = "docs/img/image.png";
$result_size = check_image_size($image_path);
$result_circle = check_pixels_within_circle($image_path);
$result_colors = check_happy_colors($image_path);
$result_conversion = convert_image_to_badge($image_path);

echo "Result for image size: " . ($result_size ? "Image size is correct." : "Image size is incorrect.");
echo "Result for pixels within circle: " . $result_circle;
echo "Result for happy colors: " . $result_colors;
if (is_string($result_conversion)) {
    echo "Conversion error: " . $result_conversion;
} else {
    echo "Conversion successful!";
}

//! This part of the code, which I used for practice, does not work.