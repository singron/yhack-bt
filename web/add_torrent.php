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

header('location: torrents.php');
?>
