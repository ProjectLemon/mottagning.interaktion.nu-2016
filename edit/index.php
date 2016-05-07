<!DOCTYPE html>

<?php
$activites = json_decode(file_get_contents("content/activities.json"));
if ($activites == null) {
    $activites = array();
}
if (isset($_GET['title'])) {
    $param = $_GET['title'];
} else {
    $param = 'Ny aktivitet';
}
$selected = false;

?>

<html>
  <head>
    <title>Title</title>
    <meta charset="UTF-8">
    <style>
      #image-upload-show {
          width: 23em;
          display: block;
      }
      #form-error {
          display: none;
      }
    </style>
  </head>
  <body>
    <div class="wrapper">
      <form id="edit-activity-form" action="edit.php" method="POST" enctype="multipart/form-data">
          
        <select id="activity-select" name="activity">
          <option value="Ny aktivitet" required>Ny aktivitet</option>
          <?php
            foreach ($activites as $title => $activity) {
                if ($title == $param) {
                    $selected = $activity;
                    echo "<option selected value=\"$title\" required>$title</option>\n";
                } else {
                    echo "<option value=\"$title\" required>$title</option>\n";
                }
            }
          ?>
        </select>
        <input type="submit" name="delete" value="Radera" 
            <?php 
              if (!$selected) { echo 'disabled';}
            ?>>
        <br>
        
        Titel:<input type="text" name="title" required 
            <?php
              if ($selected && property_exists($selected, 'title')) echo 'value="'.$selected->title.'"'
            ?>>
        <br>
        
        <?php
          if ($selected && property_exists($selected, 'image')) {
            echo '<img id="image-upload-show" src="'.$selected->image.'">Ersätt';
          }
        ?>
        Bild:<input id="image-upload" type="file" name="image" 
            <?php
              if (!$selected || ($selected && !property_exists($selected, 'image'))) {
                echo 'required';
              }
            ?>>
        <br>
        
        Beskrivning:<textarea name="description" rows="5" cols="30" required
        ><?php if ($selected && property_exists($selected, 'description')) echo $selected->description
            ?></textarea>
        <br>
        
        Tid:<input type="text" name="time" required
            <?php 
              if ($selected && property_exists($selected, 'time')) echo 'value="'.$selected->time.'"'   
            ?>>
        <br>
        
        Datum:<input type="text" name="date" required
            <?php 
              if ($selected && property_exists($selected, 'date')) echo 'value="'.$selected->date.'"'   
            ?>>
            
        <br>
        <input type="submit" name="save" value="Spara">
        <span id="form-error">Var snäll och fyll i hela formuläret</span>
      </form>
    </div>
    
    <script>
      /* Reaload page with filled content when changing activity */
      var select = document.getElementById('activity-select');
      select.addEventListener('change', function() {
          var value = select.options[select.selectedIndex].value;
          if (value == 'Ny aktivitet') {
              window.location.href = window.location.href.split('?')[0]
          } else {
              window.location.href = window.location.href.split('?')[0] + '?title=' + encodeURIComponent(value);
          }
      });
      
      /* Preivew image before uploading */
      var imageUpload = document.getElementById('image-upload');
      imageUpload.addEventListener('change', function changeActivity(event) {
          var imageUploadShow = document.getElementById('image-upload-show');
          if (imageUploadShow) {
              imageUploadShow.src = URL.createObjectURL(event.target.files[0]);
          }
      });
      
      /* Add cross-browser support for required */
      var form = document.getElementById('edit-activity-form');
      form.noValidate = true;
      form.addEventListener('submit', function(event) {
          if (!event.target.checkValidity()) {
              event.preventDefault();
              formError = document.getElementById('form-error');
              formError.style.display = 'inline';
              window.setTimeout(function(){ formError.style.display = 'none'; }, 4000); // 4 seconds
          }
      }, false);
    </script>
  </body>
</html>