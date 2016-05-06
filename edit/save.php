<?php

function verifyUploadImage($image_name, $target_file) {
    $valid = true;
    $image_file_type = pathinfo($target_file, PATHINFO_EXTENSION);
    
    // Check if file already exists
    for ($i = 0; $i < 7; $i++) {
        
        if (!file_exists($target_file)) {
            $valid = true;
            break;
        }
        
        $valid = false;
        sleep(1); // wait 1 second, max 7
    }
    
    // Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES[$image_name]["tmp_name"]); // accepted hack to determine it's an image
        
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            
        } else {
            echo "File is not an image.";
            $valid = false;
        }
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Sorry, only JPG, JPEG & PNG files are allowed.";
        $valid = false;
    } 
    
    return $valid;
}




$activites_file_name = "content/activities.json";
$target_dir = "images/";
$target_file = $target_dir . time();


/* Varify and upload image */
if (verifyUploadImage("image", $target_file)) {
    
    // Try to upload file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    
} else {
    echo "Sorry, your file was not uploaded.";
}


/* Validate Form Data */
try {
    $formdata = array(
	    'title'=> $_POST['title'],
    );

    // Open json db
    $activites = json_decode(file_get_contents($activites_file_name), true);

    // Add new data
    array_push($activites, $formdata);

    // Convert back to json
    $jsondata = json_encode($activites, JSON_PRETTY_PRINT);
	   
    // Save to json db file
    if (file_put_contents($activites_file_name, $jsondata)) {
        echo 'Data successfully saved';
	   
    } else {
        echo "error";
    }

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>