<?php

/**
 * Convert an image to the specified badge format.
 *
 * @param string $image_path The path to the image file.
 * @param string $output_path The path to save the converted image.
 * @return array An array indicating whether the conversion was successful along with a message.
 */
function convert_to_badge($image_path, $output_path)
{
    // Load the image
    $image_info = getimagesize($image_path);
    $mime_type = $image_info['mime'];

    switch ($mime_type) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($image_path);
            break;
        case 'image/png':
            $image = imagecreatefrompng($image_path);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($image_path);
            break;
        default:
            return [false, "Oopsy! This image format is not supported. Please upload a JPEG, PNG, or GIF file. 🤔"];
    }

    if (!$image) {
        return [false, "Oopsy! Unable to load the image. Please upload a valid image file. 🤔"];
    }

    // Create a new 512x512 image
    $new_image = imagecreatetruecolor(512, 512);
    imagesavealpha($new_image, true);
    $transparency = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
    imagefill($new_image, 0, 0, $transparency);

    // Get original dimensions
    $orig_width = imagesx($image);
    $orig_height = imagesy($image);

    // Resize the original image into the new 512x512 image
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, 512, 512, $orig_width, $orig_height);

    // Create a circular mask
    $mask = imagecreatetruecolor(512, 512);
    imagesavealpha($mask, true);
    imagefill($mask, 0, 0, $transparency);
    $white = imagecolorallocate($mask, 255, 255, 255);
    imagefilledellipse($mask, 256, 256, 512, 512, $white);

    // Apply the mask to the new image
    for ($y = 0; $y < 512; $y++) {
        for ($x = 0; $x < 512; $x++) {
            $alpha = (imagecolorat($mask, $x, $y) >> 24) & 0x7F;
            if ($alpha == 127) {
                imagesetpixel($new_image, $x, $y, $transparency);
            }
        }
    }

    // Define happy colors (as an example, using RGB values)
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

    // Adjust colors to happy colors
    for ($y = 0; $y < 512; $y++) {
        for ($x = 0; $x < 512; $x++) {
            $color_index = imagecolorat($new_image, $x, $y);
            $r = ($color_index >> 16) & 0xFF;
            $g = ($color_index >> 8) & 0xFF;
            $b = $color_index & 0xFF;
            $alpha = ($color_index >> 24) & 0x7F;

            if ($alpha < 127) { // Only adjust non-transparent pixels
                $closest_color = $happy_colors[0];
                $closest_distance = PHP_INT_MAX;

                foreach ($happy_colors as $color) {
                    $distance = sqrt(pow($r - $color[0], 2) + pow($g - $color[1], 2) + pow($b - $color[2], 2));
                    if ($distance < $closest_distance) {
                        $closest_distance = $distance;
                        $closest_color = $color;
                    }
                }

                $new_color = imagecolorallocatealpha($new_image, $closest_color[0], $closest_color[1], $closest_color[2], $alpha);
                imagesetpixel($new_image, $x, $y, $new_color);
            }
        }
    }

    // Save the new image
    imagepng($new_image, $output_path);

    // Free memory
    imagedestroy($image);
    imagedestroy($new_image);
    imagedestroy($mask);

    return [true, "Yay! The image has been successfully converted to the specified badge format. 🎉 ✌️"];
}

// 
$outputDirectory = realpath('img/output/');
if (!file_exists($outputDirectory)) {
    mkdir($outputDirectory, 0777, true); 
}

$outputDirectory = realpath($outputDirectory);

// Test conversion to badge format
$imagePath = 'img/test/test02.png';
$outputPath = $outputDirectory . '/badge_test06.png';
list($success, $message) = convert_to_badge($imagePath, $outputPath);
echo "$success: $message\n";

?>