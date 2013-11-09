<?

require_once('db.php');

class Torrent {
	public $torrentId;
	public $torrent;
    public $magnet_link;
	public $name;

	public static function getTorrent($id){
		$db = Database::getDB();
		$r = $db->getRow( "Torrents", "torrentId = $id" );
		$r = $db->nextRecord();
		$t = new Torrent();
		$t->torrentId = $r->torrentid;
		$t->torrent = $r->torrent;
		$t->name = $r->name;
		
		return $t;
	}
	
	public function createTorrentFromFile($name, $torrentPath){
		$db = Database::getDB();
 		$data = file_get_contents($torrentPath);
		$this->name = $name;
	    $db->insertRow("Torrents", 'torrentid,torrent,name' , "DEFAULT,'$data','$name'", 'torrentId', false);

        $res = $db->getRow("Torrents", "torrentid", "torrent=$data");
        $row = pg_fetch_row($res);
		$t->torrentId = $row[0];
	}

	public function createTorrentFromMagnet($magnetLink){
		$db = Database::getDB();
		$this->manget_link = $magnetLink;
		$db->insertRow("Torrents", 'torrentid,magnet_link', "DEFAULT,'$magnetLink'", 'torrentId', false);

        $res = $db->getRow("Torrents", "magnet_link='$magnetLink'");
        $row = pg_fetch_row($res);
		$this->torrentId = $row[0];
    }

    public function createJob($user) {
        $db = Database::GetDB();
        $db->insertRow("Jobs", "torrentid, added, billed, completed, userid, downloadid",
            $this->torrentId . ", NOW(), FALSE, NULL, $user->userId, NULL", 'jobId', false);
        return $db->lastInsertId();
    }
}

?>
