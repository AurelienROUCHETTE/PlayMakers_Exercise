<?php

/**
 * Verify if the badge meets the specified requirements.
 * 
 * This function checks if the image meets three criteria:
 * 1. The image is 512x512 pixels.
 * 2. All non-transparent pixels are within a circle of radius 256 pixels centered in the image.
 * 3. The majority of the colors in the image are considered "happy" colors.
 *
 * @param string $image_path The path to the PNG image file.
 * @return array An array containing a boolean indicating success or failure along with a message string.
 */
function verify_badge($image_path)
{
    // Load the image
    $image = imagecreatefrompng($image_path);
    if (!$image) {
        return [false, "Oops! Unable to load the image. Please upload a valid PNG file. 🤔"];
    }

    // Get image dimensions
    $width = imagesx($image);
    $height = imagesy($image);

    // Check image size pixels
    if ($width != 512 || $height != 512) {
        return [false, "Oopsy! The image size is not 512x512 pixels. 🤔"];
    }

    echo "Yay! The image size is 512x512 pixels. 🎉\n";

    // Check non-transparent pixels within a circle
    $centerX = $width / 2;
    $centerY = $height / 2;
    $radius = 256;
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $alpha = (imagecolorat($image, $x, $y) >> 24) & 0x7F;
            $distance = sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2));
            if ($distance > $radius && $alpha < 127) {
                echo "Non-transparent pixel found outside the circle at ($x, $y) with distance $distance\n";
                return [false, "Oopsy! There are non-transparent pixels outside the circle. 🤔"];
            }
        }
    }

    echo "Yay! All pixels are within the circle. 🎉 😊\n";
    
    // Define happy colors (as an example, using RGB values) — these are very colorful though
    $happy_colors = [
        [255, 223, 186], // Light Peach
        [255, 255, 153], // Light Yellow
        [255, 182, 193], // Light Pink
        [255, 192, 203], // Pink
        [240, 230, 140], // Khaki
        [255, 250, 205], // Lemon Chiffon
        [250, 250, 210], // Light Goldenrod Yellow
        [173, 216, 230], // Light Blue
        [255, 239, 213], // Papaya Whip
        [255, 228, 225]  // Misty Rose
    ];

    // Check if the badge colors are happy colors
    $happy_pixel_count = 0;
    $total_pixel_count = 0; 

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $color_index = imagecolorat($image, $x, $y);
            $r = ($color_index >> 16) & 0xFF;
            $g = ($color_index >> 8) & 0xFF;
            $b = $color_index & 0xFF;
            $alpha = ($color_index >> 24) & 0x7F;

            if ($alpha < 127) { // Only consider non-transparent pixels
                $total_pixel_count++;
                foreach ($happy_colors as $color) {
                    if (abs($r - $color[0]) < 30 && abs($g - $color[1]) < 30 && abs($b - $color[2]) < 30) {
                        $happy_pixel_count++;
                        break;
                    }
                }
            }
        }
    }

    $happy_pixel_ratio = $happy_pixel_count / $total_pixel_count;
    if ($happy_pixel_ratio > 0.5) { // If more than 50% of the pixels are happy colors
        echo "Yay! The badge colors give a feeling of happiness. 🎉 😊\n";
        return [true, "Yay! The badge meets all requirements. 🎉 ✌️"];
    } else {
        return [false, "Oopsy! The badge colors do not give a feeling of happiness. 🤔"];
    }
    
}

// Test badge verification
$imagePath = 'img/test/test04.png';
list($success, $message) = verify_badge($imagePath);
echo "$success: $message";

/**
 * Note:
 * The pictures, which are contained in the 'img' folder, are ones which I took while playing. Therefore, I am allowed to use them, specifying, if necessary, that the rights belong to Square Enix.
 * I've put in my best effort to complete this exercise. 
 * I conducted extensive research and utilized an AI to discuss various topics and obtain personalized explanations that the documentation doesn't provide.
 * Moreover, I would be grateful if you could bear in mind that I have very little coding experience.
 * Please feel free to provide feedback on the code, indicating what's correct and what's incorrect, as well as any areas for improvement.
 * I've read that Python would have been more suitable for this exercise. Nonetheless, since I've never used this language, I chose to use php, which I'm quite familiar with.
 */

 ?>