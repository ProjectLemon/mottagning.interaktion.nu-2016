<?php
/**
 * Verifies of all inputs were set in $_POST
 * This function has a variable amount of argument which
 * each should be the name of the required input
 * 
 * @throws RuntimeException If the required input was not set
 */
function verifyForm() {
    $inputs = func_get_args(); // variable arguments
    foreach ($inputs as $input) {
        if (!isset($_POST[$input]) or $_POST[$input] == '') {
            throw new RuntimeException("No $input was set");
        }
    }
}

/**
 * Will verify that image in $_FILES is valid jpg or png. 
 * Will not move image out of tmp_dir
 *
 * @param string $image_name Image name inside $_FILES
 * @param string $target_dir Directory which a place for the image should be tested
 * @param string $max_file_size (optional) Max size of the image. Default to 5mb
 * @return string The type of the image, either 'png' or 'jpg'
 * @throws RuntimeException If image is not valid
 */
function verifyUploadImage($image_name, $target_dir, $max_file_size=5242880) {
    
    if (!isset($_FILES[$image_name]['tmp_name'])) {
        throw new RuntimeException('No image provided');
    }
    $tmp_file_name = $_FILES[$image_name]['tmp_name'];
    if (!file_exists($tmp_file_name) or $tmp_file_name == '') {
        throw new RuntimeException('No image provided');
    }
    
    // Check possible errors
    switch ($_FILES[$image_name]['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($tmp_file_name); // accepted hack to determine it's an image
    if ($check == false) {
        throw new RuntimeException('File is not an image');
    }
    

    // Check MIME Type manually
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp_file_name, FILEINFO_MIME_TYPE);
    if (!in_array(
            $mime,
            array(
                'image/jpeg',
                'image/jpg',
                'image/png'
            ), true)) { // strict

        throw new RuntimeException('Sorry, only JPG, JPEG & PNG files are allowed.');
    }
    $image_type = ($mime == 'image/png') ? 'png' : 'jpg';

    // Check filesize
    if ($_FILES[$image_name]['size'] > $max_file_size) {
        throw new RuntimeException('Exceeded filesize limit ('.round($max_file_size/1024/1024, 2).'mb).');
    }
    
    return $image_type;
}

/**
 * Gets an avaible file name in $target_dir based on unix timestamp. Will wait for
 * a maximum of 7 seconds if current timestamps is taken
 *
 * @param $target_dir Directory in which to check for new file name avaiblility
 * @param $extension Extension to set to new file name
 * @return string A file name which is not occupied
 * @throws RuntimeException If no new file name based on unix timestamp becomes avaible
 *                          under 7 seconds
 */
function getNewFilename($target_dir, $extension) {
    // Use unix timestamp as filename
    // Check if target file name already exists
    $exists = false;
    for ($i = 0; $i < 7; $i++) {
        $target_file = $target_dir . time() . '.' . $extension;
        
        if (!file_exists($target_file)) {
            $exists = false;
            break;
        }
        $exists = true;
        sleep(1); // wait 1 second, max 7
    }
    if ($exists) {
        throw new RuntimeException('To many uploads at the same time, try again later');
    }
    return $target_file;
}

/**
 * Will update image from form (if any) and save them back to $saving_object
 *
 * @param $form_name Name of form in $_POST
 * @param $image_file_key Name of image in $_FILES
 * @param $target_dir Directory to place new images
 * @param $parent_path Path to website root, for absolute paths
 * @param $saving_object Object into which previues image paths is saved
 * @param $formdata Object in which new image paths should be saved
 * @param $crop If image should be cropped, an associative array can be given with one or more of these keys:
 *                'crop_height', 'crop_width'
 * @throws RuntimeException If anything was invalid or otherwise failed with image
 */
function updateImage($form_name, $image_file_key, $target_dir, $parent_path, $path_to_content, $saving_object=null, &$formdata, $size, $crop) {
  
    // if new:
    if ((isset($_FILES[$image_file_key]) and $_FILES[$image_file_key]['error'] != UPLOAD_ERR_NO_FILE) // new image
        or $saving_object == null                                                                     // new object
        or !isset($saving_object[$image_file_key])                                                    // existing object don't have an image
        or $saving_object[$image_file_key] == null) {                                                 // existing objects image is set to null
    
        $image_type = verifyUploadImage($image_file_key, $target_dir);
        $target_file = getNewFilename($target_dir, $image_type);
        
        // Try to upload file
        if (resizeImage($_FILES[$image_file_key]['tmp_name'], $target_file, $image_type, $size, $crop)) {
            echo 'The file '. basename( $_FILES[$image_file_key]['name']). ' has been uploaded. ';
        } else {
            throw new RuntimeException('Failed to move uploaded file.');
        }
        $formdata[$image_file_key] = str_replace($path_to_content, $parent_path, $target_file); // make path absolute instead of relative
        
        // If replacing image, delete old
        if ($saving_object != null) {

            $previous_image_file_name = $saving_object[$image_file_key];
            $previous_image_file_name = str_replace($parent_path, $path_to_content, $previous_image_file_name);
            
            // delete image
            if (file_exists($previous_image_file_name)) {
                unlink($previous_image_file_name);
            }
        }
        
    } else {
        // Use existing image
        $formdata[$image_file_key] = $saving_object[$image_file_key];
    }
}

/**
 * Resize an image. Can be jpg or png and output will be the same type
 *
 * @param $src_path Path the image file
 * @param $destination_path Path to save new resized image to
 * @param $image_type Image type, should be 'png, 'jpg' or 'jpeg'
 * @param $new_width (Optional) The width to resize to. If no width is set the new width
 *                   will be based on ratio between new and old height. Meaning new height must
 *                   be set if no new width i set.
 * @param $new_height (Optional) The height to resize to. If no height is set the new height
 *                   will be based on reation between new and old width. Meaning new width must
 *                   be set if no new height is set
 * @param $crop If image should be cropped, an associative array can be given with one or more of these keys:
 *                'crop_height', 'crop_width'
 * @param $quality (Optional) Number between 0 to 100, where 0 is lowest quality and 100 highest. Default set to 80
 * @param $replace (Optional) If new image should replace any file in $destination_path. Default set to true
 * @return bool Whether the image save was successfull or not
 * @throws InvalidArgumentException If invalid image type or both $new_width and $new_height was set to NULL
 */
function resizeImage($src_path, $destination_path, $image_type, $size, $crop=NULL, $quality=100, $replace=true) {
    if ($image_type == 'png') {
       $src = imagecreatefrompng($src_path);
    } else if ($image_type == 'jpg' or $image_type == 'jpeg') {
        $src = imagecreatefromjpeg($src_path);
    } else {
        throw new InvalidArgumentException('Invalid image type');
    }
    list($src_width, $src_height) = getimagesize($src_path);
    
    if (!isset($size['new_height']) and !isset($size['new_width'])) {
        throw new InvalidArgumentException('Height or width must be provided');
        
    } else if (!isset($size['new_height'])) {
        $new_width = $size['new_width'];
        $width_ratio = $new_width/$src_width;
        $new_height = $width_ratio*$src_height;
        
    } else if (!isset($size['new_width'])) {
        $new_height = $size['new_height'];
        $height_ratio = $new_height/$src_height;
        $new_width = $height_ratio*$src_width;
        
    } else {
        if (isset($crop['crop_height']) and isset($crop['crop_width'])) {
            // if cropping both width and height, one must only take the bigger size in consideration
            if ($src_width - $src_height >= 0) {
                $new_height = $size['new_height'];
                $height_ratio = $new_height/$src_height;
                $new_width = $height_ratio*$src_width;
            } else {
                $new_width = $size['new_width'];
                $width_ratio = $new_width/$src_width;
                $new_height = $width_ratio*$src_height;
            }
            
        } else {
            $new_width = $size['new_width'];
            $new_height = $size['new_height'];   
        }
    }

    $src_x = 0;
    $src_y = 0;
    if ($crop) {
            
        // calculate new width and height for cropped image
        if (isset($crop['crop_height']) and $new_height > $crop['crop_height']) {
            $diff_h = $new_height - $crop['crop_height'];
            $diff_h_src = $diff_h*$src_width/$new_width;
            
            $new_height = $crop['crop_height'];
            $src_y = round($diff_h_src/2);
            $src_height = $src_height - round($diff_h_src);
        }
        
        if (isset($crop['crop_width']) and $new_width > $crop['crop_width']) {
            $diff_w = $new_width - $crop['crop_width'];
            $diff_w_src = $diff_w*$src_height/$new_height;
            
            $new_width = $crop['crop_width'];
            $src_x = round($diff_w_src/2);
            $src_width = $src_width - round($diff_w_src);
        }
    }

    $image = imagecreatetruecolor($new_width, $new_height);
    if ($image_type == 'png') {
        imagefill($image, 0,0, imagecolorallocatealpha($image, 255, 255, 255, 127)); // make transparent
    }
    $success_copy = imagecopyresampled($image, $src, 0,0, $src_x, $src_y, $new_width, $new_height, $src_width, $src_height);

    if (!$replace && file_exists($destination_path)) {
        return false;
    } else {
      
        if ($image_type == 'jpg' or $image_type == 'jpeg') {
            $success_save = imagejpeg($image, $destination_path, $quality); // save image as compressed jpg
        } else if ($image_type == 'png') {
            imagesavealpha($image, true);
            $success_save = imagepng($image, $destination_path, round($quality/10)); // save image as compressed png
        }
    }
    
    // Free memory
    imagedestroy($image);
    imagedestroy($src);
    
    return $success_copy and $success_save;
}

/**
 * This function will clear all values in a json by replacing them with an empty array
 *
 * @param $path Path to json file
 */
function deleteALL($path) {
  file_put_contents($path, json_encode([]));
}

/* Following validation functions requires $input_name to be name inside $_POST */
function validateLength($input_name, $lenght) {
    $input = $_POST[$input_name];
    if (mb_strlen($input, 'UTF-8') > $lenght) {
        throw new RuntimeException("String '$input' must be less than $lenght characters");
    }
}

function validateNumber($input_name) {
    $input = $_POST[$input_name];
    if (!is_numeric($input)) {
        throw new RuntimeException("Number '$input' is not a valid number");
    }
}

function validateTime($input_name) {
    $input = $_POST[$input_name];
    $regex_pattern = '/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/'; // format (H)H:MM
    $match = preg_match($regex_pattern, $input);
    if ($match == 0) {
        throw new RuntimeException("Input '$input' is not a valid time");
    }
}

function validateRadio($input_name, $variable_arguments) {
    $inputs = func_get_args(); // variable arguments
    $options = array_splice($inputs, 1); // remove first argument = $input_name
    if (!in_array($_POST[$input_name], $options)) {
        throw new RuntimeException("Bad $input_name select, should be one of: ".implode(', ', $options));
    }
}

?>