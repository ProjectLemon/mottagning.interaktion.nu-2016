<!DOCTYPE html>

<?php
$activites_file_name = '../content/activities.json';
if (file_exists($activites_file_name)) {
    $content = file_get_contents($activites_file_name); 
} else {
    $content = null;
}
if (!$content) {
    $activites = '{}';
} else {
    $activites = json_decode($content);
}

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
    <link href="/resources/css/edit.css" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div class="wrapper">
      <form id="edit-activity-form" action="save.php" method="POST" enctype="multipart/form-data">          
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
          
          <label>Tid och Datum:<input name="datetime" type="text" id="datepicker" readonly required <?php 
            if ($selected && property_exists($selected, 'startDateTime')) echo 'value="'.$selected->startDateTime.'"';
          ?>></label>
          <br>
        
          <!--
          <label>Tid:<input id="form-time" type="time" name="time" required
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
          -->
          
          <label class="form-description">Beskrivning:<textarea name="description" rows="5" cols="30" required
          ><?php if ($selected && property_exists($selected, 'description')) echo $selected->description
              ?></textarea>
          </label>
          <br>
          
          
          <label>Plats:<input name="place" type="text" required <?php 
            if ($selected && property_exists($selected, 'place')) echo 'value="'.$selected->place.'"';
          ?>></label>
          <br>
          
          <label>Latitude:<input name="lat" type="text" required <?php 
            if ($selected && property_exists($selected, 'lat')) echo 'value="'.$selected->lat.'"';
          ?>></label>
          <label>Longitude:<input name="long" type="text" required <?php 
            if ($selected && property_exists($selected, 'long')) echo 'value="'.$selected->long.'"';
          ?>></label>
          <br>
          <span>Tips: <a href="http://www.latlong.net/">www.latlong.net</a></span>
          <br>
          
          <input type="submit" name="save" value="Spara" class="button">
          <span id="form-error">Var snäll och fyll i hela formuläret</span>
          
        </div>
      </form>
      <a href="/" class="back-to-main-page">← Tillbaka till huvudsidan</a>
    </div>
    
    <script src="/src/js/lib/pikaday.min.js"></script>
    <script>
      var picker = new Pikaday({
          field: document.getElementById('datepicker'),
          showTime: true,
          use24hour: true,
          minDate: new Date(),
        i18n: {
            previousMonth : 'Föregående månad',
            nextMonth     : 'Nästa månad',
            months        : ['Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December'],
            weekdays      : ['Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag'],
            weekdaysShort : ['Sön','Mån','Tis','Ons','Tors','Fre','Lör']
        }
      });
      
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