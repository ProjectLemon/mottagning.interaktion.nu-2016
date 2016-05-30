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
if (isset($_GET['select'])) {
    $param = $_GET['select'];
} else {
    $param = 'Ny aktivitet';
}
$selected = false;

?>

<html lang="sv">
  <?php include '../head.php'; ?>
  <body>
    <div class="wrapper">
      <?php include '../menu.php'; ?>
      
      <form id="form" action="save.php" method="POST" enctype="multipart/form-data">
        
        <ul id="select-list">
          <?php
            $selected_attr = ''; 
            if ($param == 'Ny aktivitet') {
              $selected_attr = 'checked'; 
            }
          ?>
          <li><label>Ny aktivitet<input id="select-new" type="radio" name="activity" value="Ny aktivitet" required <?php echo $selected_attr; ?>></label></li>
          <?php 
            $lastActivityDate = NULL;
            foreach ($activites as $activity) {
                $selected_attr = '';
                if ($activity->title == $param) {
                    $selected = $activity;
                    $selected_attr = 'checked';
                }
                if ($activity->startDate != $lastActivityDate or $lastActivityDate === NULL) {
                    echo '<li><h3 class="select-seperator">'.$activity->startDate.'</h3><hr></li>';
                }
                $lastActivityDate = $activity->startDate;
                
                echo '<li><label>'.htmlspecialchars($activity->title).'<input type="radio" name="activity" value="'.htmlspecialchars($activity->title).'" data-datetime="'.$activity->startDate.' '.$activity->startTime.'" required '.$selected_attr.'></label></li>';
            }
          ?>
        </ul>
        
        <div id="edit-form" class="form-activity">
          <button id="form-delete" type="button" name="delete"
              <?php 
                if (!$selected) { echo 'disabled'; }
              ?>>Radera aktivitet</button>
          
          <label>Titel:<input id="selector-input" type="text" name="title" maxlength="100" required 
              <?php
                if ($selected && property_exists($selected, 'title')) echo 'value="'.htmlspecialchars($selected->title).'"'
              ?>>
          </label>
          
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
            
            <label>Datum:<input name="date" type="text" id="form-date" class="readonly" maxlength="25" required <?php 
              if ($selected && property_exists($selected, 'startDate')) echo 'value="'.htmlspecialchars($selected->startDate).'"';
            ?>></label>
            <br>
          
            <label>Tid:<input id="form-time" type="time" name="time" maxlength="5" required
                <?php 
                  if ($selected && property_exists($selected, 'startTime')) echo 'value="'.htmlspecialchars($selected->startTime).'"'   
                ?>>
            </label>
            <div id="form-time-error" class="input-error-message">Normalt skriver man tid så här: 12:34</div>
            
            <label class="form-description">Beskrivning:<textarea id="form-description" name="description" rows="5" cols="30" maxlength="200" required
            ><?php if ($selected && property_exists($selected, 'description')) echo htmlspecialchars($selected->description)
                ?></textarea>
            </label>
            <div id="form-description-error" class="input-error-message">170/200 tecken</div>
            <br>          
            
            <label class="form-place">Plats:<input name="place" type="text" maxlength="100" required <?php 
              if ($selected && property_exists($selected, 'place')) echo 'value="'.htmlspecialchars($selected->place).'"';
            ?>></label>
            <br>
            
            <label>Latitude:<input name="lat" type="text" maxlength="20" required <?php 
              if ($selected && property_exists($selected, 'lat')) echo 'value="'.htmlspecialchars($selected->lat).'"';
            ?>></label>
            <label class="form-long">Longitude:<input name="long" type="text" maxlength="20" required <?php 
              if ($selected && property_exists($selected, 'long')) echo 'value="'.htmlspecialchars($selected->long).'"';
            ?>></label>
            <br>
            <span class="latlong-tip">Tips: <a href="http://www.latlong.net/" target="_blank">www.latlong.net</a></span>
            <br>
            
            <input type="submit" name="save" value="Spara" class="button">
            <span id="form-error">Var snäll och fyll i hela formuläret</span>
            
          </div>
          <div id="response"></div>
        </div>
      </form>
      <a href="/" class="back-to-main-page">← Tillbaka till huvudsidan</a>
    </div>
    
    <script src="/src/js/lib/pikaday.min.js"></script>
    <script>
      /* Add datepicker */
      var picker = new Pikaday({
        field: document.getElementById('form-date'),
        minDate: new Date(),
        firstDay: 1,
        theme: 'purple-theme', // defined in css
        i18n: {
            previousMonth : 'Föregående månad',
            nextMonth     : 'Nästa månad',
            months        : ['Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December'],
            weekdays      : ['Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag'],
            weekdaysShort : ['Sön','Mån','Tis','Ons','Tors','Fre','Lör']
        }
      });
      
      /* Validate time */
      var time = document.getElementById('form-time');
      var timeError = document.getElementById('form-time-error');
      var regexpTime = new RegExp('^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$');
      time.addEventListener('change', function(event) {
          var timeLabel = event.target.parentNode;
          if (regexpTime.test(event.target.value) == false) {
              timeLabel.classList.add('input-error');
              timeError.style.display = 'block';
          } else {
              timeLabel.classList.remove('input-error');
              timeError.style.display = 'none';
          }
      });
      
      /* Validate description lenght */
      var description = document.getElementById('form-description');
      var descriptionError = document.getElementById('form-description-error');
      description.addEventListener('keyup', function(event) {
          var len = event.target.value.length;
          if (len >= 170) {
              descriptionError.innerHTML = len+'/200 tecken';
              descriptionError.style.display = 'block';
          } else {
              descriptionError.style.display = '';
          }
      });
      
      document.getElementById('select-list').querySelector('input[checked]').checked = true;
    </script>
    <script src="/src/js/edit.js" async></script>
  </body>
</html>