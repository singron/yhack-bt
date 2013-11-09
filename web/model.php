<?


require('db.php');


class Controller {
	static public $db = NULL;
	public static function getDB(){
		if (!self::$db) {
			self::$db = new Database();
			self::$db->connect();
		}
		return self::$db;
	}
}

class User {
	public $userId;
	public $email;
	public $hash;
	public $salt;
	public $credit;
	public $jobs;

	public function initWithRecord($record){
		$this->userid = $record->userId;
		$this->email = $record->email;
		$this->hash = $record->hash;
		$this->salt = $record->salt;
		$this->credit = $record->credit;
	}
	
	public static function createUser($email, $password, $credit = 0){
		$u = new User();
		$u->salt = uniqid(mt_rand(), true);
		$u->hash = crypt($u->salt . $password);
		$u->credit = $credit;
		$u->email = $email;
		$db = Controller::getDB();
		$db->insertRow("Users", "email, hash, salt", "'$u->email', '$u->hash' , '$u->salt'" , 'userId');
		$u->userId = $db->lastInsertId();
		
		return $u;
	}
	
	function __construct($record = NULL){
		if ($record){
			$this->initWithRecord($record);
		}
	}
	
	public static function getUserWithId($id){
		$db = Controller::getDB();
		$db->getRow( "Users", "userId = $id" );
		return new User($db->nextRecord());
	}
	

	public function getActiveJobs(){
		$db = Controller::getDB();
		$jobs = $db->getRow("Jobs", "userId = $this->userId and completed IS NULL ORDER BY added ASC");
		while ($next = Controller::$db->nextRecord()){
			$j = new Job($next);
			array_push($this->jobs, $j);
		}
	}

	public function addCredit($amount){
		$this->credit += $amount;
	}
	
	
	public function update(){
		if (!$this->userId) return -1;
		$db = Controller::getDB();
		$db->updateRow("Users", "userId = '$this->userId', email = '$this->email', hash = '$this->hash', salt = '$this->salt', credit='$this->credit'", "userId = '$this->userId'");	
		foreach ($this->jobs as $job){
			$job->update();
		}
	}

}

class Job {
	public $jobId;
	public $torrent;
	public $added;
	public $bid;
	public $downloaded;
	public $size;
	public $eta;
	public $completed;
	public $userId;

	public static function getJobWithId($id){
		$db = Controller::getDB();
		$db->getRow( "Jobs", "where jobId = $id" );
		return new Job($db->nextRecord());
	}
	
	public static function createJob( $torrentId, $userId, $bid ){
		$j = new Job();
		$j->torrentId = $torrentId;
		$j->userId = $userId;
		$j->bid = $bid;
		$db = Controller::getDB();
		$db->insertRow('Jobs', "torrentId, userId, bid", "$j->torrentId, $j->userId,$j->bid", 'jobId');
		$j->jobId = $db->lastInsertId();
		return $j;
	}

	function __construct($record = NULL){
		if ($record){
			$this->initWithRecord($record);
		}
	}

	public function initWithRecord($record){
		$this->torrentId = $record->torrentId;
		$this->added = $record->added;
		$this->bid = $record->bid;
		$this->downloaded = $record->downloaded;
		$this->size = $record->size;
		$this->eta = $record->eta;
		$this->completed = $record->completed;
		$this->userId = $record->userId;
	}
	

	public function progress() { 
		return $this->downloaded / (float)$this->size;
	}
	
	public function update(){
		if (!$this->torrentId) return -1;
		$db = Controller::getDB();
		$db->updateRow("Job", "torrentId = '$this->torrentId', added = '$this->added', bid = '$this->bid', downloaded = '$this->downloaded', size='$this->size', eta='$this->eta', completed='$this->completed'", "jobId = '$this->jobId'");
		if ($this->torrent) 
			$torrent->update();
	}
		
	public static function updateProgress($jid, $downloaded, $eta){
		$j = new Job();
		$j->torrentId = $jid;
		$j->updateProgress($downloaded, $eta);
	}
}

class Torrent {
	public $torrentId;
	public $torrentPath;

	
	public static function createTorrent($torrentPath){
		$db = Controller::getDB();
 		$data = bin2hex(file_get_contents($torrentPath));
		$t = new Torrent;
		$db->insertRow("Torrents", 'torrent' , $data, 'torrentId', false);
		$t->torrentId = $db->lastInsertId();
		$t->torrentPath = $torrentPath;

		return $t;
	}
}
echo "SDF";
$t = Torrent::createTorrent("/var/www/mikey/db.php");
$u = User::createUser("micmoo@me.com", "luigi193", 100);
$j = Job::createJob($t->torrentId, $u->userId, 10);

	?>