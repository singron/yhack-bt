<?


class Job {
	public $jobId;
	public $torrentId;
	public $added;
	public $bid;
	public $downloaded;
	public $size;
	public $speed;
	public $eta;
	public $completed;
	public $userId;
	public $billed;

	public static function getJob($id){
		$db = Database::getDB();
		$db->getRow( "Jobs", "jobId = $id" );
		return new Job($db->nextRecord());
	}
	
	public static function createJob( $torrentId, $userId, $bid ){
		$j = new Job();
		$j->torrentId = $torrentId;
		$j->userId = $userId;
		$j->bid = $bid;
		$j->billed = false;
		$j->completed = false;
		$j->added = date(DATE_RFC2822);
		$db = Database::getDB();
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
		$this->torrentId = $record->torrentid;
		$this->added = $record->added;
		$this->bid = $record->bid;
		$this->downloaded = $record->downloaded;
		$this->size = $record->size;
		$this->billed = $record->billed;
		$this->eta = $record->eta;
		$this->speed = $record->speed;
		$this->completed = $record->completed;
		$this->jobId = $record->jobid;
		$this->userId = $record->userid;
	}
	
	public function pause(){
		$this->update();
	}
	
	public function start(){
		if ($this->size == 0) return -1;
		$this->update();
		
		return 0;
	}
	

	public function progress() { 
		if ($this->size == 0){
			return 0;
		}
		return $this->downloaded / (float)$this->size;
	}
	
	public function setSize($size, $speed = 0, $eta = 0){
		$this->size = $size;
		if ($speed > 0) $this->speed = $speed;
		if ($eta > 0) $this->eta = $eta;
	}
	
	public function sync(){
		$db = Database::getDB();
		$db->getRow( "Jobs", "jobId = $id" );
		$this->initWithRecord($db->nextRecord());
	}

	
	public function torrentName(){

	}
	
	public function update(){
		if (!$this->torrentId) return -1;
		$db = Database::getDB();
		$str = "";
		if ($this->eta)
			$str .= "eta='$this->eta',";
		if($this->completed) 
			$str .= "completed='$this->completed,";
		$db->updateRow("Jobs", "torrentId = '$this->torrentId', added = '$this->added', bid = '$this->bid', downloaded = '$this->downloaded', size='$this->size'," . $str . " billed='$this->billed'", "jobId = '$this->jobId'");
	}
		
	public function updateProgress($downloaded, $eta, $speed){
		$this->downloaded = $downloaded;
		$this->eta = $eta;
		$this->speed = $speed;
		$this->update();
	}
}

?>
