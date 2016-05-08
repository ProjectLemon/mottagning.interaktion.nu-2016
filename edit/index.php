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

<html lang="sv">
  <head>
    <title>Title</title>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div class="wrapper">
      <form id="edit-activity-form" action="edit.php" method="POST" enctype="multipart/form-data">          
        <select id="activity-select" name="activity" class="button">
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
              if (!$selected) { echo 'disabled'; } else { echo 'class="button"'; }
            ?>>
        
        <label>Titel:<input type="text" name="title" required 
            <?php
              if ($selected && property_exists($selected, 'title')) echo 'value="'.$selected->title.'"'
            ?>>
        </label>
        
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
        
          <label>Tid:<input type="text" name="time" required
              <?php 
                if ($selected && property_exists($selected, 'time')) echo 'value="'.$selected->time.'"'   
              ?>>
          </label>
          
          <label class="form-date">Datum:<input type="text" name="date" required
              <?php 
                if ($selected && property_exists($selected, 'date')) echo 'value="'.$selected->date.'"'   
              ?>>
          </label>
          <br>
          
          <label class="form-description">Beskrivning:<textarea name="description" rows="5" cols="30" required
          ><?php if ($selected && property_exists($selected, 'description')) echo $selected->description
              ?></textarea>
          </label>
          <br>
          <input type="submit" name="save" value="Spara" class="button">
          <span id="form-error">Var snäll och fyll i hela formuläret</span>
        </div>
      </form>
      <a href="/" class="back-to-main-page">← Tillbaka till huvudsidan</a>
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
      
      /* Preview image before uploading */
      var imageUpload = document.getElementById('image-upload');
      imageUpload.addEventListener('change', function changeActivity(event) {
          var imageUploadShow = document.getElementById('image-upload-show');
          var image = URL.createObjectURL(event.target.files[0])
          if (imageUploadShow) {
              imageUploadShow.src = image;
          } else {
              imageUpload.parentNode.insertAdjacentHTML('beforebegin', '<img id="image-upload-show" src="'+image+'">Ersätt ');
          }
      });
      
      /* Add cross-browser support for required */
      var form = document.getElementById('edit-activity-form');
      form.noValidate = true;
      errorTime = 4000 // 4 seconds
      form.addEventListener('submit', function(event) {
          if (!event.target.checkValidity()) {
              event.preventDefault();
              formError = document.getElementById('form-error');
              formError.style.display = 'inline';
              window.setTimeout(function(){ formError.style.display = 'none'; }, errorTime);
              // Mark all invalid inputs labels
              for (i=0; i<event.target.length; i++) {
                  var input = event.target[i];
                  if (!input.validity.valid) {
                      input.parentNode.classList.add('input-error');
                      // Use closure to capture correct input
                      window.setTimeout((function(inputParent) {
                          return function() {
                              inputParent.classList.remove('input-error');
                          };
                      })(input.parentNode), errorTime);
                  }
              }
          }
      }, false);
    </script>
  </body>
</html>