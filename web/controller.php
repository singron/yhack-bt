<?
	require('controller.class.php');
	if (!$_POST['mode']) exit(0);
	switch($_POST['mode']){
		case "createUser":
			return json_encode(Controller::createUser($_POST['email'], $_POST['password']));
		break;
		
		case "startJob":
			$rv = Controller::startJob($_POST['jobId']);
			if ($rv == -1){
				// Error
			}
			// Remove funds From User Account
			
		break;
		case "pauseJob":
			Controller::pauseJob($_POST['jobId']);
		break;
		
		case "addCreditToUser":
			Controller::addFundsToUser($_POST['funds'], $_POST['userId']);
		break;
		
		case "getAvailableJobsForUser":
			return json_encode(Controller::getAvailableJobsForUser($_POST['userId']));
		break;
		
		case "createJobAndTorrent":
			$tid = Controller::createTorrent($_FILE['torrent']['name'], $_FILE['torrent']['tmp_name']);
			return json_encode(Controller::createJobForUserWithTorrent($_POST['userId'], $tid));
		break;
	}

?>