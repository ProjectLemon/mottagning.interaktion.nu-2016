<?php
header('Content-Type: text/html; charset=utf-8');
    
require '../saving_resources.php';

function save($contacts_file_name, $target_dir, $parent_path, $path_to_content) {

    // Validate Form Data
    verifyForm('name', 'mail', 'phone', 'group');
    
    validateLength('name', 100);
    validateLength('mail', 100);
    validateLength('phone', 20);
    validateRadio('group', 'red', 'yellow', 'blue', 'general');
    
    // All form data should be in order (not image)
    $formdata = array(
	    'name' => $_POST['name'],
	    'mail' => $_POST['mail'],
	    'phone' => $_POST['phone'],
	    'group' => $_POST['group']
    );

    // Open json data file
    $contacts = json_decode(file_get_contents($contacts_file_name), true);
    
    $exists = false;
    $index = NULL;
    $contact = NULL;
    // linear search for value
    foreach ($contacts as $i => $c) {
        if ($c['name'] == $_POST['contact']) {
            $exists = true;
            $index = $i;
            $contact = $c;
        }
    }

    // Verify image if new
    updateImage('contact', 'image', $target_dir, $parent_path, $path_to_content, $contact, $formdata, ['new_width' => 300, 'new_height' => 300], ['crop_width' => 300, 'crop_height' => 300]);

    // Add new data
    if ($exists) {
        $contacts[$index] = $formdata;
    } else {
        $contacts[] = $formdata; // insert in end
    }
        

    // Convert back to json
    $jsondata = json_encode($contacts);
	   
    // Save to json data file
    if (file_put_contents($contacts_file_name, $jsondata)) {
        echo 'Contact successfully saved';
	   
    } else {
        throw new RuntimeException('Could not save to file');
    }
}

function delete($contacts_file_name) {

    verifyForm('contact');
    validateLength('contact', 100);
    
    // Open json data file
    $contacts = json_decode(file_get_contents($contacts_file_name), true);
    
    $index = NULL;
    foreach ($contacts as $i => $contact) {
        if ($contact['name'] == $_POST['contact']) {
            $index = $i;
        }
    }
    
    if ($index == NULL) {
        throw new RuntimeException('No such contact to delete');
    }

    $image_path = str_replace($parent_path, '..', $contacts[$index][$image_file_key]);
    if (file_exists($image_path)) {
        unlink($image_path);
    }
    unset($contacts[$index]); // Delete activity
    
    // Convert back to json
    $jsondata = json_encode($contacts);
       
    // Save to json data file
    if (file_put_contents($contacts_file_name, $jsondata)) {
        echo 'Contact successfully deleted';
       
    } else {
        throw new RuntimeException('Could not save to file');
    }
}

$image_dir = '../../content/images/';
$path_to_content = '../../';
$parent_path = '/';
$contacts_file_name = '../../content/contacts.json';
if (!file_exists($contacts_file_name)) {
    // create file if not exist:
    $file = fopen($contacts_file_name, 'w');
    fwrite($file, '[]');
    fclose($file); 
}

try {
    if (isset($_POST['save'])) {
        
        save($contacts_file_name, $image_dir, $parent_path, $path_to_content);
        
    } elseif (isset($_POST['delete'])) {
        
        delete($contacts_file_name, $parent_path, 'image');
        
    } elseif (isset($_POST['delete-all'])) {
      
        deleteALL($contacts_file_name);
        
    } else {
      
        throw new RuntimeException('No action provided');
    }
    
} catch (RuntimeException $e) {
    http_response_code(400);
    echo 'Error: ' . $e->getMessage();
}



?>