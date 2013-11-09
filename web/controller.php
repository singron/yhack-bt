<?
	include('controller.class.php');
	$_POST = $_GET;
	if (!array_key_exists("mode", $_POST)) exit(0);
	switch($_POST['mode']){
		case "createUser":
			if (User::existsUserWithEmail($_POST['email']) == 1){
				echo "ERROR";
				exit(-1);
			}
			$u = Controller::createUser($_POST['email'], $_POST['password']);
			if ($u == -1){
				//error
			}
			else {
				echo json_encode($u);
			}
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
			echo json_encode(Controller::getAvailableJobsForUser($_POST['userId']));
		break; 
		
		case "createJobAndTorrent":
			$tid = Controller::createTorrent($_FILE['torrent']['name'], $_FILE['torrent']['tmp_name']);
			return json_encode(Controller::createJobForUserWithTorrent($_POST['userId'], $tid));
		break;
	}

?>