<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");
?>

<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-sm-2 sidenav">
            <p><a href="#">Link</a></p>
            <p><a href="#">Link</a></p>
            <p><a href="#">Link</a></p>
        </div>
        <div class="col-sm-8 text-left">
            <h1>Welcome</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <hr>
            <h3> Who are you? </h3>
            <div class="btn-group-vertical" role="group">
                
                <a href="homepagePrincipal.php" class="btn btn-large btn-block btn-info" role="button">Principal</a>
                <a href="homepageTeacher.php" class="btn btn-large btn-block btn-info" role="button">Teacher</a>
                <a href="homepageParent.php" class="btn btn-large btn-block btn-info" role="button">Parent</a>
                <a href="homepageAdmin.php" class="btn btn-large btn-block btn-info" role="button">Admin</a>
            </div>
        </div>
        <div class="col-sm-2 sidenav">
            <div class="well">
                <p>ADS</p>
            </div>
            <div class="well">
                <p>ADS</p>
            </div>
        </div>
    </div>
</div>

<?php
require_once("defaultFooter.php")
?>