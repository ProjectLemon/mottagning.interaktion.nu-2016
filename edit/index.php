<!DOCTYPE html>

<?php
$activites = json_decode(file_get_contents("content/activities.json"));
if ($activites == null) {
    $activites = array();
}
$params = array_keys($_GET);
if (count($params) != 1) {
    $param = 'new';
} else {
    $param = $params[0];
}
$selected = false;

?>

<html>
  <head>
    <title>Title</title>
    <meta charset="UTF-8">
  </head>
  <body>
    <div class="wrapper">
      <form action="save.php" method="POST" enctype="multipart/form-data">
        <select id="activity-select" name="activity">
          <option value="New" required>New</option>
          <?php
            foreach ($activites as $activity) {
              $title = $activity->title;
              if ($title == $param) {
                $selected = $activity;
                echo "<option selected value=\"$title\" required>$title</option>\n";
              } else {
                echo "<option value=\"$title\" required>$title</option>\n";
              }
            }
          ?>
        </select>
        <br>
        Titel:<input type="text" name="title" required <?php if ($selected) echo 'value="'.$activity->title.'"' ?>>
        <br>
        Bild:<input type="file" name="image" required>
        <input type="submit" value="Spara">
      </form>
    </div>
    
    <script>
      var select = document.getElementById('activity-select');
      select.addEventListener('change', function changeActivity() {
          var value = select.options[select.selectedIndex].value;
          if (value == 'New') {
            window.location.href = window.location.href.split('?')[0]
          } else {
            window.location.href = window.location.href.split('?')[0] + '?' + value;
          }
      });
    </script>
  </body>
</html>