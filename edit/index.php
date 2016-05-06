<!DOCTYPE html>

<?php
$activites = json_decode(file_get_contents("content/activities.json"));
if ($activites == null) {
    $activites = array();
}

?>

<html>
  <head>
    <title>Title</title>
    <meta charset="UTF-8">
  </head>
  <body>
    <div class="wrapper">
      <form action="save.php" method="POST" enctype="multipart/form-data">
        <select name="activity" class="activity-selector">
          <?php
            foreach ($activites as $activity) {
              $title = $activity->title;
              echo "<option value=\"$title\">$title</option>\n";
            }
          ?>
        </select>
        <br>
        Titel:<input type="text" name="title"><br>
        Bild:<input type="file" name="image">
        <input type="submit" value="Spara">
      </form>
    </div>
  </body>
</html>