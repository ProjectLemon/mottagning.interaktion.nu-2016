<!DOCTYPE html>
<html>
  <head>
    <title>Edit: Response</title>
    <meta charset="UTF-8">
  </head>
  <body>
<?php

function verifyUploadImage($image_name, $target_dir) {
    
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
    if (isset($_POST['submit'])) {
        $check = getimagesize($tmp_file_name); // accepted hack to determine it's an image
        
        if ($check !== false) {
            echo 'File is an image - ' . $check['mime'] . '.';
            
        } else {
            throw new RuntimeException('File is not an image');
        }
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
        throw new RuntimeException('Target file name already exist');
    }

    // You should also check filesize here. 
    if ($_FILES[$image_name]['size'] > 5*1024*1024) { // 5mb
        throw new RuntimeException('Exceeded filesize limit (5mb).');
    }
    
    return $target_file;
}

function verifyForm() {
    if (!isset($_POST['title']) || $_POST['title'] == '') {
        throw new RuntimeException('No title was set');
    }
}


// define ('SITE_ROOT', realpath(dirname(__FILE__))); // may need on server

$activites_file_name = 'content/activities.json';
$target_dir = 'content/images/';
$image_file_key = 'image';


try {
    // Varify image
    $target_file = verifyUploadImage($image_file_key, $target_dir);
    
    // Validate Form Data
    verifyForm();
    
    
    // Try to upload file
    if (move_uploaded_file($_FILES[$image_file_key]['tmp_name'], $target_file)) {
        echo '<p>The file '. basename( $_FILES[$image_file_key]['name']). ' has been uploaded.</p>';
    } else {
        throw new RuntimeException('Failed to move uploaded file.');
    }
    
    
    // All data should be in order, now save it
    $formdata = array(
	    'title'=> $_POST['title'],
	    'image'=> $target_file
    );

    // Open json db
    $activites = json_decode(file_get_contents($activites_file_name), true);

    // Add new data
    array_push($activites, $formdata);

    // Convert back to json
    $jsondata = json_encode($activites, JSON_PRETTY_PRINT);
	   
    // Save to json db file
    if (file_put_contents($activites_file_name, $jsondata)) {
        echo '<p>Data successfully saved</p>';
	   
    } else {
        throw new RuntimeException('Could not save to file');
    }

} catch (RuntimeException $e) {
    echo '<h1>Error: ' . $e->getMessage() . '</h1>';
}

?>

    <a href="./">Back</a>
  </body>
</html>