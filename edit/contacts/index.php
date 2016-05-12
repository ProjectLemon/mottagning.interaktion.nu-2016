<!DOCTYPE html>

<?php
$contacts_file_name = '../content/contacts.json';
if (file_exists($contacts_file_name)) {
    $content = file_get_contents($contacts_file_name); 
} else {
    $content = null;
}
if (!$content) {
    $contacts = '{}';
} else {
    $contacts = json_decode($content);
}

if ($contacts == null) {
    $contacts = array();
}
if (isset($_GET['select'])) {
    $param = $_GET['select'];
} else {
    $param = 'Ny kontakt';
}
$selected = false;

?>

<html lang="sv">
  <head>
    <title>Title</title>
    <meta charset="UTF-8">
    <link href="/resources/css/edit.css" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:400,700' rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div class="wrapper">
      <?php include '../menu.php' ?>
      
      <form id="edit-form" class="form-contact" action="save.php" method="POST" enctype="multipart/form-data">          
        <select id="edit-select"  name="contact" class="button">
          <option id="select-new" value="Ny kontakt" required>Ny kontakt</option>
          <?php
            foreach ($contacts as $name => $contact) {
                $selected_attr = '';
                if ($name == $param) {
                    $selected = $contact;
                    $selected_attr = 'selected';
                    
                }
                echo "<option $selected_attr value=\"$name\" required>$name</option>\n";
            }
          ?>
        </select>
        <input type="submit" name="delete" value="Radera" 
            <?php 
              if (!$selected) { echo 'disabled'; } else { echo 'class="button"'; }
            ?>>
        
        <div><label>Namn:<input type="text" name="name" required 
            <?php
              if ($selected && property_exists($selected, 'name')) echo 'value="'.$selected->name.'"'
            ?>>
        </label></div>
        
        <div class="form-content">
        
          <?php
            if ($selected && property_exists($selected, 'image')) {
              echo '<img id="image-upload-show" src="'.$selected->image.'">Ersätt';
            }
          ?>
          <label>Bild:<input id="image-upload" type="file" name="image" 
              <?php
                if (!$selected || ($selected && !property_exists($selected, 'image'))) {
                  echo 'required';
                }
              ?>>
          </label>
          <br>
          
          <label>Mail:<input name="mail" type="text" required <?php 
            if ($selected && property_exists($selected, 'mail')) echo 'value="'.$selected->mail.'"';
          ?>></label>
          <br>
          
          <label class="form-phone">Telefon:<input name="phone" type="text" required <?php 
            if ($selected && property_exists($selected, 'phone')) echo 'value="'.$selected->phone.'"';
          ?>></label>
          <br>
          <div id="form-groups">
              <h3 class="group-title">Grupp:</h3>
              <?php 
                  if ($selected && property_exists($selected, 'group')) {
                      $selected->group;
                  }
                  $group_colors = array('blue' => 'Blå', 'red' => 'Röd', 'yellow' => 'Gul', 'green' => 'General');
                  foreach ($group_colors as $color => $color_name) {
                      $checked = '';
                      if (   ($selected
                              && property_exists($selected, 'group') 
                              && $selected->group == $color)
                                || 
                             (!$selected && $color == 'blue') // check first element if no privious selected
                         ) {
                          $checked = 'checked';
                      }
                      echo "<label>$color_name<input type=\"radio\" name=\"group\" value=\"$color\" $checked></label>";
                  }
              ?>
          </div>
          <br>
          
          <input type="submit" name="save" value="Spara" class="button">
          <span id="form-error">Var snäll och fyll i hela formuläret</span>
          
        </div>
      </form>
      <a href="/" class="back-to-main-page">← Tillbaka till huvudsidan</a>
    </div>
    
    <script>
      var form = document.getElementById('edit-form');
        
      /* Change background based on group */
      groupColors = {red: '#C62828', blue: '#3374BA', yellow: '#d8bd2f', green: '#1E5E2F'}
      
      // Change background color immediately
      form.style.backgroundColor = groupColors[document.querySelector('input[name="group"]:checked').value];
      
      var groupButtons = document.querySelectorAll('[name="group"]');
      for (i = groupButtons.length-1; i >= 0; i--) {
          groupButtons[i].addEventListener('change', function(event) {
              form.style.backgroundColor = groupColors[event.target.defaultValue];
          });
      }
    </script>
    <?php include '../js.php' ?>
  </body>
</html>