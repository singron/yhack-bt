<?php 
require('controller.class.php'); 
$user = Controller::authenticate();
$user->getActiveJobs();
date_default_timezone_set('America/New_York'); 


?>
<!DOCTYPE html>
<html>
  <head>
    <title>yhack-bt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	
  </head>
  <body>
       <?php
       $activepage = 'torrents';
       include 'navbar.php'; 
       ?>
        <div class="torrents">
          <table class="table" id='torrents'>
            <thead>
              <tr>
                <th></th>
                <th>Bid</th>
                <th>Name</th>
                <th>Size</th>
                <th>Progress</th>
                <th>Down Speed</th>
                <th>ETA</th>
                <th>Deletion In</th>
              </tr>
            </thead>
            <tbody>

                    <?php foreach($user->jobs as $job): ?>
                    <?php if($job->completed): ?>
                        <tr class="success">
                            <td><button type="button" class="btn btn-success">Download</button></td>
                            <td></td>               
                    <?php else: ?>
                        <tr class="active">
                            <td><button type="button" class="btn btn-danger">Cancel</button></td>
                            <td><?php echo $job->bid ?></td>
                    <?php endif; ?>
                    <td><?php echo $job->torrent->name ?></td>
                    <td><?php echo $job->size ?></td>
                    <td>
                        <div class="progress text-center">
                            <div class="progress-bar" style="width: <?php echo round(100*($job->downloaded/$job->size),1) ?>%;">
                                <span><?php echo round(100*($job->downloaded/$job->size),1) ?>%</span>
                            </div> 
                        </div>
                    </td>
                    <?php if($job->completed): ?>
                        <td></td>
                        <td></td>
                        <td></td>
                    <?php else: ?>
                        <td><?php echo $job->speed ?></td>
                        <td><?php echo $job->eta ?></td>
                        <td></td>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tr>
            </tbody>
          </table>
        </div> 

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
	
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="assets/js/bootstrap.min.js"></script>
	<script>
		$(document).ready(function () {
		    var interval = 3000;  //number of mili seconds between each call
		    var refresh = function() {
				$.ajax({
					type: "POST",
					data: { 'userId' : '<? echo $user->userId; ?>', 'mode' : 'getAvailableJobsForUser' },
		            url: "controller.php",
		            cache: false,
					dataType: "json",
		            success: function(jobs) {
						var torrents = $("#torrents tr");
						for (var job in jobs){
							$("#torrents tr .progress")[job].style.width = 1.00 * jobs[job]["downloaded"] / jobs[job]["size"];
							$("#torrents tr .progress span")[job].innerHTML = Math.round(100*(jobs[job]["downloaded"] / jobs[job]["size"]),1) + "%";
						}
		                setTimeout(function() {
		                    refresh();
		                }, interval);

		            }
		        });
		    };
		    refresh();
		});		
	</script>

  </body>
</html>
