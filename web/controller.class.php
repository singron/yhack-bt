<?
	require('db.php');
	require('user.class.php');
	require('job.class.php');
	require('torrent.class.php');
	
	class Controller {
		public static function getUser($id){
			return User::getUser($id);
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
		
	}
	
	

?>