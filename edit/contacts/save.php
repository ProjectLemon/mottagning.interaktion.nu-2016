<!DOCTYPE html>
<html>
  <head>
    <title>Edit: Response</title>
    <meta charset="UTF-8">
  </head>
  <body>
<?php
    
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


    // Verify image if new
    updateImage('contact', 'image', $target_dir, $parent_path, $contacts, $formdata);

    // Add new data
    if ($_POST['contact'] != $_POST['name']) { // Name has been changed
        unset($contacts[$_POST['contact']]);
        $contacts[$_POST['name']] = $formdata;    
        
    } else {
        $contacts[$_POST['name']] = $formdata;   
    }

    // Convert back to json
    $jsondata = json_encode($contacts, JSON_PRETTY_PRINT);
	   
    // Save to json data file
    if (file_put_contents($contacts_file_name, $jsondata)) {
        echo '<p>Contact successfully saved</p>';
	   
    } else {
        throw new RuntimeException('Could not save to file');
    }
}

function delete($contacts_file_name) {
    
    if (!isset($_POST['contact']) || $_POST['contact'] == '') {
        throw new RuntimeException('No contact was selected');
    }
    // All form data should be in order (not image)
    $formdata = array(
        'contact' => $_POST['contact'],
    );
    
    // Open json data file
    $contacts = json_decode(file_get_contents($contacts_file_name), true);
    
    if (!isset($contacts[$_POST['contact']])) {
        throw new RuntimeException('No such contact to delete');
    }
    
    unset($contacts[$_POST['contact']]); // Delete activity
    
    // Convert back to json
    $jsondata = json_encode($contacts, JSON_PRETTY_PRINT);
       
    // Save to json data file
    if (file_put_contents($contacts_file_name, $jsondata)) {
        echo '<p>Contact successfully deleted</p>';
       
    } else {
        throw new RuntimeException('Could not save to file');
    }
}


// define ('SITE_ROOT', realpath(dirname(__FILE__))); // may need on server

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
    echo '<h1>Error: ' . $e->getMessage() . '</h1>';
}



?>

    <a href="./?select=<?php echo rawurlencode($_POST['name']) ?>">Back</a>
  </body>
</html>