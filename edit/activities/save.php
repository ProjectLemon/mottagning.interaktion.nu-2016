<!DOCTYPE html>
<html>
  <head>
    <title>Edit: Response</title>
    <meta charset="UTF-8">
  </head>
  <body>
<?php
    
require '../saving_resources.php';

function save($activites_file_name, $target_dir, $parent_path) {
    $image_file_key = 'image';

    // Validate Form Data
    verifyForm('activity', 'title', 'description', 'datetime', 'place', 'lat', 'long');
    
    // All form data should be in order (not image)
    $formdata = array(
	    'title' => $_POST['title'],
	    'description' => $_POST['description'],
	    'startDateTime' => $_POST['datetime'],
	    'place' => $_POST['place'],
	    'lat' => $_POST['lat'],
	    'long' => $_POST['long']
    );

    // Open json data file
    $activites = json_decode(file_get_contents($activites_file_name), true);


    // Verify image if new
    updateImage('activity', 'image', $target_dir, $parent_path, $activites, $formdata);

    // Add new data
    if ($_POST['activity'] != $_POST['title']) { // Title has been changed
        unset($activites[$_POST['activity']]);
        $activites[$_POST['title']] = $formdata;    
        
    } else {
        $activites[$_POST['title']] = $formdata;   
    }

    // Convert back to json
    $jsondata = json_encode($activites, JSON_PRETTY_PRINT);
	   
    // Save to json data file
    if (file_put_contents($activites_file_name, $jsondata)) {
        echo '<p>Activity successfully saved</p>';
	   
    } else {
        throw new RuntimeException('Could not save to file');
    }
}

function delete($activites_file_name) {
    
    if (!isset($_POST['activity']) || $_POST['activity'] == '') {
        throw new RuntimeException('No activity was selected');
    }
    // All form data should be in order (not image)
    $formdata = array(
        'activity' => $_POST['activity'],
    );
    
    // Open json data file
    $activites = json_decode(file_get_contents($activites_file_name), true);
    
    if (!isset($activites[$_POST['activity']])) {
        throw new RuntimeException('No such activity to delete');
    }
    
    unset($activites[$_POST['activity']]); // Delete activity
    
    // Convert back to json
    $jsondata = json_encode($activites, JSON_PRETTY_PRINT);
       
    // Save to json data file
    if (file_put_contents($activites_file_name, $jsondata)) {
        echo '<p>Activity successfully deleted</p>';
       
    } else {
        throw new RuntimeException('Could not save to file');
    }
}


// define ('SITE_ROOT', realpath(dirname(__FILE__))); // may need on server

$image_dir = '../content/images/';
$parent_path = '/edit';
$activites_file_name = '../content/activities.json';
if (!file_exists($activites_file_name)) {
  $file = fopen($activites_file_name, 'w');
  fwrite($file, '{}');
  fclose($file); // create file if not exist
}

try {
    if (isset($_POST['save'])) {
        
        save($activites_file_name, $image_dir, $parent_path);
        
    } elseif (isset($_POST['delete'])) {
        
        delete($activites_file_name);
        
    } else {
        throw new RuntimeException('No action provided');
    }
    
} catch (RuntimeException $e) {
    echo '<h1>Error: ' . $e->getMessage() . '</h1>';
}



?>

    <a href="./?title=<?php echo rawurlencode($_POST['title']) ?>">Back</a>
  </body>
</html>