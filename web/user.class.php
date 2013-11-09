<?

include_once('db.php');

class User {
	public $userId;
	public $email;
	public $hash;
	public $salt;
	public $credit;
	public $jobs = array();

	public function initWithRecord($record){
		$this->userId = $record->userid;
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
		$db = Database::getDB();
		$r = $db->insertRow("Users", "email, hash, salt", "'$u->email', '$u->hash' , '$u->salt'" , 'userId');
		$u->userId = $db->lastInsertId();
		
		return $u;
	}
	
	public static function existsUserWithEmail($email){
		$db = Database::getDB();
		$u = $db->getRow("Users", "email = '$email'");
		$v = $db->numRows($u);
		return ($v == 0);

	}
	
	function __construct($record = NULL){
		if ($record){
			$this->initWithRecord($record);
		}
	}
	
	public static function getUser($id){
		$db = Database::getDB();
		$db->getRow( "Users", "userId = $id" );
		return new User($db->nextRecord());
	}
	
	

	public function getActiveJobs(){
		$db = Database::getDB();
		$jobs = $db->getRow("Jobs", "userId = $this->userId and completed IS NULL ORDER BY added ASC");
		while ($next = Database::$db->nextRecord()){
			$j = new Job($next);
			array_push($this->jobs, $j);
		}
		return $this->jobs;
	}

	public function addCredit($amount){
		$this->credit += $amount;
	}
	
	
	public function update(){
		if (!$this->userId) return -1;
		$db = Database::getDB();
		$db->updateRow("Users", "userId = '$this->userId', email = '$this->email', hash = '$this->hash', salt = '$this->salt', credit='$this->credit'", "userId = '$this->userId'");	
		foreach ($this->jobs as $job){
			$job->update();
		}
	}

}

?>