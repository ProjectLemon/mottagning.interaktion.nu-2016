<?php
header('Content-Type: text/html; charset=utf-8');
    
require '../saving_resources.php';

function save($activites_file_name, $target_dir, $parent_path) {
    $image_file_key = 'image';

    // Validate Form Data
    verifyForm('activity', 'title', 'description', 'date', 'time', 'place', 'lat', 'long');
    validateLength('activity', 100);
    validateLength('title', 100);
    validateLength('description', 200); // requirement
    validateLength('date', 25);
    validateLength('time', 5);
    validateLength('place', 100);
    validateLength('lat', 20);
    validateLength('long', 20);
    validateTime('time');
    validateNumber('lat');
    validateNumber('long');
    
    // All form data should be in order (not image)
    $formdata = array(
	    'title' => $_POST['title'],
	    'description' => $_POST['description'],
	    'startDate' => $_POST['date'],
	    'startTime' => $_POST['time'],
	    'place' => $_POST['place'],
	    'lat' => $_POST['lat'],
	    'long' => $_POST['long']
    );

    // Open json data file
    $activites = json_decode(file_get_contents($activites_file_name), true);

    $exists = false;
    $index = NULL;
    $activity = NULL;
    // linear search for value
    foreach ($activites as $i => $a) {
        if ($a['title'] == $_POST['activity']) {
            $exists = true;
            $index = $i;
            $activity = $a;
        }
    }

    // Update or set new image
    updateImage('activity', 'image', $target_dir, $parent_path, $activity, $formdata);

    // Add new data in sorted order (linear)
    if ($exists) {
        $inserted = false;
        if ($activity['startDate'] != $formdata['startDate'] or $activity['startTime'] != $formdata['startTime']) {
          
            foreach ($activites as $i => $a) {
                if ($a['title'] == $formdata['title']) {
                    array_splice($activites, $i, 1); // remove value
                }
            }
            foreach ($activites as $i => $a) {
                
                if (!$inserted and strtotime($a['startDate'].' '.$a['startTime']) > strtotime($formdata['startDate'].' '.$formdata['startTime'])) {
                    array_splice($activites, $i, 0, array($formdata)); // insert
                    $inserted = true;
                }
            }
            if (!$inserted) {
                $activites[] = $formdata; // insert in end
            }
            
        } else {
            $activites[$index] = $formdata;
        }
        
    } else {
        $inserted = false;
        foreach ($activites as $i => $a) {
            if (strtotime($a['startDate'] . $a['startTime']) > strtotime($formdata['startDate'] . $formdata['startTime'])) {
                array_splice($activites, $i, 0, array($formdata));
                $inserted = true;
                break;
            }
        }
        if (!$inserted) {
            $activites[] = $formdata; // insert in end
        }
    }

    // Convert back to json
    $jsondata = json_encode($activites, JSON_PRETTY_PRINT);
	   
    // Save to json data file
    if (file_put_contents($activites_file_name, $jsondata)) {
        echo 'Activity successfully saved';
	   
    } else {
        throw new RuntimeException('Could not save to file');
    }
}

function delete($activites_file_name) {
    
    verifyForm('activity');
    validateLength('activity', 100);

    // Open json data file
    $activities = json_decode(file_get_contents($activites_file_name), true);
    
    $index = NULL;
    foreach ($activities as $i => $activity) {
        if ($activity['title'] == $_POST['activity']) {
            $index = $i;
        }
    }
    
    if ($index == NULL) {
        throw new RuntimeException('No such activity to delete');
    }
    
    unset($activities[$index]); // Delete activity
    
    // Convert back to json
    $jsondata = json_encode($activities, JSON_PRETTY_PRINT);
       
    // Save to json data file
    if (file_put_contents($activites_file_name, $jsondata)) {
        echo 'Activity successfully deleted';
       
    } else {
        throw new RuntimeException('Could not save to file');
    }
}

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
    http_response_code(400);
    echo 'Error: ' . $e->getMessage();
}



?>