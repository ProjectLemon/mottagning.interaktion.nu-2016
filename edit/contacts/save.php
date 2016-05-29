<?php
header('Content-Type: text/html; charset=utf-8');
    
require '../saving_resources.php';

function save($contacts_file_name, $target_dir, $parent_path) {

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
    updateImage('contact', 'image', $target_dir, $parent_path, $contact, $formdata);

    // Add new data
    if ($exists) {
        $contacts[$index] = $formdata;
    } else {
        $contacts[] = $formdata; // insert in end
    }
        

    // Convert back to json
    $jsondata = json_encode($contacts, JSON_PRETTY_PRINT);
	   
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
    
    unset($contacts[$index]); // Delete activity
    
    // Convert back to json
    $jsondata = json_encode($contacts, JSON_PRETTY_PRINT);
       
    // Save to json data file
    if (file_put_contents($contacts_file_name, $jsondata)) {
        echo 'Contact successfully deleted';
       
    } else {
        throw new RuntimeException('Could not save to file');
    }
}

$image_dir = '../content/images/';
$parent_path = '/edit';
$contacts_file_name = '../content/contacts.json';
if (!file_exists($contacts_file_name)) {
    // create file if not exist:
    $file = fopen($contacts_file_name, 'w');
    fwrite($file, '{}');
    fclose($file); 
}

try {
    if (isset($_POST['save'])) {
        
        save($contacts_file_name, $image_dir, $parent_path);
        
    } elseif (isset($_POST['delete'])) {
        
        delete($contacts_file_name);
        
    } else {
        throw new RuntimeException('No action provided');
    }
    
} catch (RuntimeException $e) {
    http_response_code(400);
    echo 'Error: ' . $e->getMessage();
}



?>