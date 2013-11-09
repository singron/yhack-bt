<?php 
require('controller.class.php');

$regerror = false;
$nouser = false;
$noemail = false;
$nopassword = false;
$nopasswordconfirm = false;
$passwordmismatch = false;
if(isset($_POST['submitbutton'])){
    if(!isset($_POST['username'])){
        $regerror = true;
        $nouser = true;
    }
    if(!isset($_POST['email'])){
        $regerror = true;
        $noemail = true;
    }
    if(!isset($_POST['password'])){
        $regerror = true;
        $nopassword = true;
    }
    if(!isset($_POST['password_confirm'])){
        $regerror = true;
        $nopasswordconfirm = true;
    }
    if(!($nopassword || $nopasswordconfirm) && strcmp($_POST['password'], $_POST['password_confirm'])!=0){
        $regerror = true;
        $passwordmismatch = true;
    }
    
    if (User::existsUserWithEmail($_POST['email']) == 1){
			 $regerror = true;
	 }
	 

    
    if(!$regerror){
		$u = Controller::createUser($_POST['email'], $_POST['password']);
        header('Location: index.php?&registered=true');
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>yhack-bt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="assets/css/register.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php
    $activepage = 'register';
    include 'navbar.php';
    ?>    
   
    

    <div class="container">
        <?php if($regerror): ?>
        <div class="bs-callout bs-callout-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">x</button>
            You suck
        </div>
        <?php endif; ?>
        <form class="form-register" action="register.php" method="post">
            <h2 class="form-register-heading">Register</h2>
            <input type="text" class="form-control" placeholder="Username" name="username" required autofocus>
            <input type ="text" class="form-control" placeholder="Email Address" name="email" required>
            <input type="password" class="form-control" placeholder="Password" name="password" requried>
            <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirm" required>
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="submitbutton" >Register</button>
        </form>
    </div>
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>
