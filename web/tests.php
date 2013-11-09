<?
	echo bin2hex($_POST['t']);
	echo "<form action='tests.php' method='post' enctype='multipart/form-data'><textarea id='t' name='t'></textarea><input type='submit' /></form>";
	exit(0);
	
	require("controller.class.php");
	$e = mt_rand(65,95) . mt_rand(65,95) . mt_rand(65,95) . mt_rand(65,95) .mt_rand(65,95);
	$user1 = User::createUser($e, "luigi193");
	$torrent = Torrent::createTorrent( "This Torrent", "./db.php" );
	$job1 = Job::createJob( $torrent->torrentId, $user1->userId, 20 );
	
	assert( $user1->email == $e);
	assert( $job1->userId == $user1->userId);
	assert( $job1->torrentId == $torrent->torrentId);
	assert( $job1->bid == 20);
	
	$user = User::getUser($user1->userId);
	
	assert( $user1->email == $user->email);
	$job = Job::getJob($job1->jobId);
	$job_u = $user1->getActiveJobs();
	$job_u = $job_u[0];
	
	assert( $job->userId == $job_u->userId);
	assert( $job->userId == $job1->userId);
	assert( $job->torrentId == $job_u->torrentId);
	
	$job_u = $user->getActiveJobs();
	$job_u = $job_u[0];
	
	assert( $job->userId == $job_u->userId);
	assert( $job->userId == $job1->userId);
	assert( $job->torrentId == $job_u->torrentId);

	
	


?>
