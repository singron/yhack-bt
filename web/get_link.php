<?php

require_once('db.php');
require_once('controller.class.php');

$user = Controller::authenticate();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $db = Database::getDB();

    $download_id = $_GET['download_id'];
    $ip = $_SERVER['REMOTE_ADDR'];

    $db->updateRow('downloads', "ip=$ip", "downloadid=$download_id");
}
