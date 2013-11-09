<?
require('db.php');

class Controller {
	static public $db = NULL;
	public static function getDB(){
		if (!$db) {
			Controller::$db = new Database();
			Controller::$db->connect;
		}
		return Controller::$db;
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
		
		$db = Controller::getDB();
		$db->query("INSERT into Users (email, hash, salt) VALUES ('$u->email', '$u->hash', '$u->salt')");
		return $u;
	}
	
	function __construct($record = NULL){
		if ($record){
			$this->initWithRecord($record);
		}
	}
	
	public static function getUserWithId($id){
		$db = Controller::getDB();
		$db->query( "SELECT * from Users where userId = $id" );
		return new User($db->singleRecord());
	}
	

	public function getActiveJobs(){
		$db = Controller::getDB();
		$jobs = $db->query("SELECT * from Jobs WHERE userId = $this->userId and completed IS NULL ORDER BY added ASC");
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
		$db->query("UPDATE Users set userId = '$this->userId', email = '$this->userId', hash = '$this->userId', salt = '$this->userId', credit='$this->credit' WHERE userId = '$this->userId'");		
		foreach ($this->jobs as $job){
			$job->update();
		}
	}

}

class Job {
	public $torrentId;
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
		$db->query( "SELECT * from Jobs where torrentId = $id" );
		return new Job($db->singleRecord());
	}
	
	public static function createJob( $torrentId, $userId, $size, $bid ){
		$j = new Job();
		$j->torrentId = $torrentId;
		$j->userId = $userId;
		$j->size = $size;
		$j->bid = $bid;
		
		$db = Controller::getDB();
		$db->query("INSERT into Jobs (torrentId, userId, size, bid) VALUES ('$j->torrentId', '$j->userId', '$j->size', '$j->bid')");
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
		$db->query("UPDATE Job set torrentId = '$this->torrentId', added = '$this->added', bid = '$this->bid', downloaded = '$this->downloaded', size='$this->size', eta='$this->eta', completed='$this->completed',  WHERE torrentId = '$this->torrentId'");
		if ($this->torrent) 
			$torrent->update();
	}
	
	public function updateProgress($downloaded, $eta){
		$this->downloaded = $downloaded;
		$this->eta = $eta;
		$db = Controller::getDB();
		$db->query("UPDATE jobs SET downloaded = $downloaded, eta= $eta WHERE torrentId = $this->torrentId");
	}
	
	
 
}

class Torrent {
	public $torrentId;
	public $torrent;
	public $magnetLink;
}


	?>
