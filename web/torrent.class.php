<?

include_once('db.php');

class Torrent {
	public $torrentId;
	public $torrentPath;
	public $torrent;
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

	
	public static function createTorrent($name, $torrentPath){
		$db = Database::getDB();
 		$data = bin2hex(file_get_contents($torrentPath));
		$t = new Torrent;
		$t->name = $name;
		$db->insertRow("Torrents", 'torrent,name' , "'$data','$name'", 'torrentId', false);
		$t->torrentId = $db->lastInsertId();
		$t->torrentPath = $torrentPath;
		return $t;
	}
}

?>