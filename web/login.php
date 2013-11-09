<?php require('controller.class.php'); ?>
<!DOCTYPE html>
<html>
  <head>
    <title>yhack-bt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="assets/css/login.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php
    $activepage ='login';
    include 'navbar.php';
    ?>    

    <div class="container">
        <form class="form-login">
            <h2 class="form-login-heading">Log In</h2>
            <input type="text" class="form-control" placeholder="Username" name="username" required autofocus>
            <input type="password" class="form-control" placeholder="Password" name="password" required>
            <label class="checkbox">
                <input type="checkbox" value="remember me" name="remember"> Remember Me
            </label>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Log In</button>
        </form>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>
