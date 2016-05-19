<?php
/**
 * Verifies of all inputs were set in $_POST
 * This function has and variable amount of argument which
 * each should be the name of the required input
 * 
 * @throws RuntimeException If the required input was not set
 */
function verifyForm() {
    $inputs = func_get_args(); // variable arguments
    foreach ($inputs as $input) {
        if (!isset($_POST[$input]) || $_POST[$input] == '') {
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
 * @return string A valid filename/path in which the file should be saved.
 *                Name is taken from a unix timestamp
 * @throws RuntimeException If image is not valid
 */
function verifyUploadImage($image_name, $target_dir, $max_file_size=5242880) {
    
    if (!isset($_FILES[$image_name]['tmp_name'])) {
        throw new RuntimeException('No image provided');
    }
    $tmp_file_name = $_FILES[$image_name]['tmp_name'];
    if (!file_exists($tmp_file_name) || $tmp_file_name == '') {
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
    $extension = ($mime == 'image/png') ? '.png' : '.jpg';
    
    
    // Use unix timestamp as filename
    // Check if target file name already exists
    $exists = false;
    for ($i = 0; $i < 7; $i++) {
        $target_file = $target_dir . time() . $extension;
        
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

    // Check filesize
    if ($_FILES[$image_name]['size'] > $max_file_size) {
        throw new RuntimeException('Exceeded filesize limit ('.round($max_file_size/1024/1024, 2).'mb).');
    }
    
    return $target_file;
}

/**
 * 
 *
 * @param $form_name
 * @param $image_file_key
 * @param $target_dir
 * @param $parent_path
 * @param $saving_object
 * @param $formdata
 */
function updateImage($form_name, $image_file_key, $target_dir, $parent_path, $saving_object, &$formdata) {
    
    // if new:
    if (!array_key_exists($_POST[$form_name], $saving_object)               // activity does not exist
            || !isset($saving_object[$_POST[$form_name]][$image_file_key])  // activity exist but with no image
            || $saving_object[$_POST[$form_name]][$image_file_key] == null  // activity exist but image is set to null
            || isset($_FILES[$image_file_key]) && $_FILES[$image_file_key]['error'] != UPLOAD_ERR_NO_FILE) {  // new file is uploaded
        
        $target_file = verifyUploadImage($image_file_key, $target_dir);
        
        // Try to upload file
        if (move_uploaded_file($_FILES[$image_file_key]['tmp_name'], $target_file)) {
            echo 'The file '. basename( $_FILES[$image_file_key]['name']). ' has been uploaded. ';
        } else {
            throw new RuntimeException('Failed to move uploaded file.');
        }
        $dir_name = basename((dirname(__FILE__))); // get current directory
        $formdata[$image_file_key] = str_replace('..', $parent_path, $target_file);
        
        
        // If replacing image
        if (   array_key_exists($_POST[$form_name], $saving_object)             // object exist
            && isset($saving_object[$_POST[$form_name]][$image_file_key])       // image is set in object
            && $saving_object[$_POST[$form_name]][$image_file_key] != null      // image is not set to null
            ) {
                
            $previous_image_file_name = $saving_object[$_POST[$form_name]][$image_file_key];
            $previous_image_file_name = str_replace($parent_path, '..', $previous_image_file_name);
            
            // delete image
            if (file_exists($previous_image_file_name)) {
                unlink($previous_image_file_name);
            }
        }
        
    } else {
        // Use existing image
        $formdata[$image_file_key] = $saving_object[$_POST[$form_name]][$image_file_key];
    }
}

/**
 * 
 */
function resize_image($image_path, $width, $height, $quality=60) {
    list($orig_width, $orig_height) = getimagesize($filename);
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