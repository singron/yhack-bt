<?php 

require_once('torrent.class.php');
require_once('controller.class.php');

$user = Controller::authenticate();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $torrent = new Torrent();

    if ($_POST['type'] === "file") {
        $torrent->createTorrentFromFile($_FILES["file"]["name"], $_FILES["file"]["tmp_name"]); 
        unlink($_FILES["file"]["tmp_name"]);
    } else {
        $torrent->createTorrentFromMagnet($_POST['magnet_link']);
    }

    $torrent->createJob($user);
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>yhack-bt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
       <?php
       $activepage = 'torrents';
       include 'navbar.php'; 
       ?>

        

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>
