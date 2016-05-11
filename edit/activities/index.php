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
  <head>
    <title>Title</title>
    <meta charset="UTF-8">
    <link href="/resources/css/edit.css" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div class="wrapper">
      <?php include '../menu.php'; ?>
      
      <form id="edit-form" class="form-activity" action="save.php" method="POST" enctype="multipart/form-data">          
        <select id="edit-select" name="activity" class="button">
          <option id="select-new" value="Ny aktivitet" required>Ny aktivitet</option>
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
          
          
          <label class="form-place">Plats:<input name="place" type="text" required <?php 
            if ($selected && property_exists($selected, 'place')) echo 'value="'.$selected->place.'"';
          ?>></label>
          <br>
          
          <label>Latitude:<input name="lat" type="text" required <?php 
            if ($selected && property_exists($selected, 'lat')) echo 'value="'.$selected->lat.'"';
          ?>></label>
          <label class="form-long">Longitude:<input name="long" type="text" required <?php 
            if ($selected && property_exists($selected, 'long')) echo 'value="'.$selected->long.'"';
          ?>></label>
          <br>
          <span class="latlong-tip">Tips: <a href="http://www.latlong.net/" target="_blank">www.latlong.net</a></span>
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
    </script>
    <?php include '../js.php'; ?>
  </body>
</html>