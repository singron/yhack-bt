<?php 
require_once('controller.class.php');

$user = Controller::authenticate();


if(isset($_REQUEST['id'])){
    $deleteid = $_REQUEST['id'];
    $db = Database::getDB();
    $db->deleteRow("Jobs", "jobId = $deleteid AND userId = $user->userId");
    
}
header('location: torrents.php');

?>
