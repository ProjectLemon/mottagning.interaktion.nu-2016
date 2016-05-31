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
  <?php include '../head.php'; ?>
  <body>
    <div class="wrapper">
      <?php include '../menu.php' ?>
      
      <form id="form" action="save.php" method="POST" enctype="multipart/form-data">
      
        <ul id="select-list">
          <?php
            $radio_list = '';
            
            foreach ($contacts as $contact) {
                $selected_attr = '';
                if ($contact->name == $param) {
                    $selected = $contact;
                    $selected_attr = 'checked';
                }
                
                $radio_list .= '<li><label>'.htmlspecialchars($contact->name).'<input type="radio" name="contact" value="'.htmlspecialchars($contact->name).'" required '.$selected_attr.'></label></li>';
            }

            $selected_attr = '';
            if (!$selected) {
                $selected_attr = 'checked';
            }
            echo '<li><label>Ny kontakt<input id="select-new" type="radio" name="contact" value="Ny kontakt" required '.$selected_attr.'></label></li>
          <li><hr></li>';
            echo $radio_list;
          ?>
        </ul>
      
        <div id="edit-form" class="form-contact">
          <?php 
            if ($selected) { echo '<button id="form-delete" type="button" name="delete" >Radera kontakt</button>'; }
          ?>
          
          <div><label>Namn:<input id="selector-input" type="text" name="name" maxlength="100" required 
              <?php
                if ($selected && property_exists($selected, 'name')) echo 'value="'.htmlspecialchars($selected->name).'"'
              ?>>
          </label></div>
          
          <div class="form-content">
          
            <?php
              if ($selected && property_exists($selected, 'image')) {
                echo '<img id="image-upload-show" src="'.htmlspecialchars($selected->image).'">Ersätt';
              }
            ?>
            <label>Bild:<input id="image-upload" type="file" name="image" accept="image/png,image/jpeg, .jpg,.jpeg,.png"
                <?php
                  if (!$selected || ($selected && !property_exists($selected, 'image'))) {
                    echo 'required';
                  }
                ?>>
            </label>
            <div id="form-image-error" class="input-error-message">Bilden får inte vara mer än 5mb</div>
            <br>
            
            <label>Mail:<input name="mail" type="text" maxlength="100" required <?php 
              if ($selected && property_exists($selected, 'mail')) echo 'value="'.htmlspecialchars($selected->mail).'"';
            ?>></label>
            <br>
            
            <label class="form-phone">Telefon:<input name="phone" type="text" maxlength="20" required <?php 
              if ($selected && property_exists($selected, 'phone')) echo 'value="'.htmlspecialchars($selected->phone).'"';
            ?>></label>
            <br>
            <div id="form-groups">
                <h3 class="group-title">Grupp:</h3>
                <?php 
                    if ($selected && property_exists($selected, 'group')) {
                        $selected->group;
                    }
                    $group_colors = array('blue' => 'Blå', 'red' => 'Röd', 'yellow' => 'Gul', 'general' => 'General');
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
                        echo "<label>$color_name<input type=\"radio\" name=\"group\" value=\"$color\" required $checked></label>";
                    }
                ?>
            </div>
            <br>
            
            <input type="submit" name="save" value="Spara" class="button">
            <span id="form-error">Var snäll och fyll i hela formuläret</span>
            
          </div>
          <div id="response"></div>
        </div>
      </form>
      <div class="extra">
        <a href="/" class="back-to-main-page">← Tillbaka till huvudsidan</a>
        <button id="form-delete-all" type="button" name="delete-all" >Radera <strong>alla</strong> kontakter</button>
      </div>
    </div>
    
    <script>
      var form = document.getElementById('form');
      var editForm = document.getElementById('edit-form');
        
      /* Change background based on group */
      var groupColors = {red: '#C62828', blue: '#3374BA', yellow: '#d8bd2f', general: '#1E5E2F'}
      
      // Change background color immediately
      editForm.style.backgroundColor = groupColors[editForm.querySelector('input[name="group"]:checked').value];
      
      var groupButtons = editForm.querySelectorAll('[name="group"]');
      for (i = groupButtons.length-1; i >= 0; i--) {
          groupButtons[i].addEventListener('change', function(event) {
              editForm.style.backgroundColor = groupColors[event.target.defaultValue];
          });
      }
      
      /* Link clicking on image to upload image */
      var imageShow = document.getElementById('image-upload-show');
      var image = document.getElementById('image-upload');
      if (imageShow) {
          imageShow.addEventListener('click', function() {
              image.click();
          });
      }
    </script>
    <script src="/src/js/edit.js" async></script>
  </body>
</html>