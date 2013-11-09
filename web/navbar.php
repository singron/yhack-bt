    <div class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button> 
                <a class="navbar-brand" href="index.php">yhack-bt</a> 
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li <?php if(strcmp($activepage,'index')==0): ?> class="active" <?php endif; ?>><a href="index.php">Home</a></li>
                    <li<?php if(strcmp($activepage,'torrents')==0): ?> class="active" <?php endif; ?>><a href="torrents.php">Torrents</a></li> 
                </ul> 
                <ul class="nav navbar-nav navbar-right">
                   <li<?php if(strcmp($activepage,'login')==0): ?> class="active" <?php endif; ?>><a href="login.php">Log In</a></li>
                   <li<?php if(strcmp($activepage,'register')==0): ?> class="active" <?php endif; ?>><a href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </div>
