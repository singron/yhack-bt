<?
	include_once('db.php');
	include_once('user.class.php');
	include_once('job.class.php');
	include_once('torrent.class.php');
	
	class Controller {
		public static function getUser($id){
			return User::getUser($id);
		}
		
		public static function loginUser($user, $password=-1){
			if ($password == -1) $passwrd = $_COOKIE['hash'];
			session_start();
			if ($password == $user->hash){
				if ($user->sessionId == ""){
					session_start();
					$user->setSessionId(session_id());
				 	setcookie('email', $user->email, $past); 
				 	setcookie('hash', $user->hash, $past); 
					echo $_COOKIE['email'];
					$user->loggedIn = true;
				}
				else {
					session_id($user->sessionId);
					if ($user->sessionId != session_id()){
						$user->loggedIn = false;
						$past = time() - 100; 
					 	setcookie("email", 'gone', $past); 
					 	setcookie("hash", 'gone', $past); 
						header("Location: login.php");
					}
					else {
						session_id($user->sessionId);
						$user->loggedIn = true;
						echo session_id();
					}
				}
			}
			else {
				header("Location: login.php");
			}
		}
		
		public static function getUserAndAvailableJobs($id){
			$user = User::getUser($id);
			$user->getActiveJobs();
			return $user;
		}
		
		public static function getJob($id){
			return $job = Job::getJob($id);
		}
		
		public static function getTorrent($id){
			$torrent = Job::getJob($id);
			$torrent->torrent = "";
			return $torrent;
		}
		
		public static function getAvailableJobsForUser($id){
			$user = User::getUser($id);
			$user->getActiveJobs();
			return $user->jobs;
		}
		
		public static function createUser($email, $password){
			$user = User::existsUserWithEmail($email);
			if ($user == 1) return -1;
			$user = User::createUser($email, $password);
			return json_encode($user);
		}
		
		public static function addFundsToUser($funds, $user){
			$user = User::getUser($user);
			$user->addFunds($funds);
			$user->update();
		}
		
		public static function createTorrent($name, $path){
			$t = Torrent::createTorrent($name, $path);
			return $t;
			
		}
		
		public static function createJobForUserWithTorrent($userid, $torrentId, $bid){
			$user = User::getUser($userid);
			$t = $torrentId;
			if (gettype($torrentId) == "object"){
				$t = $torrentId->torrentId;
			}
			
			$j = Job::createJob($t, $userid, $bid);
			return $j;
			
			
		}
		
		public static function pauseJob($jobId){
			$job = Job::getJob($_POST['jobId']);
			$rv = $job->pause();
		}
		
		public static function startJob($jobId){
			$job = Job::getJob($_POST['jobId']);
			return $job->start();
				// Error
		}
		
		public static function authenticate($u = NULL, $password = NULL){
			if (isset($_COOKIE['email'])){
				if (!$u) $u = User::getUserByEmail($_COOKIE['email']);
				Controller::loginUser($u, $_COOKIE['hash']);
				return $u;
			}
			else {
				if ($u && $password){
					if ($u->checkLogin($password)){
					 	setcookie('hash', $user->hash, $past); 
						Controller::loginUser($u);
					}
					else {
						header("Location: login.php");
					}
				}
				else header("Location: login.php");
			}
		}
	}

?>