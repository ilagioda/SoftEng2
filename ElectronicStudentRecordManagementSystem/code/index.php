<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");
/**
 *  To be removed => here until logout is implemented
 * */
$_SESSION = array();
?>

<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-sm-2 sidenav">
            <p><a href="#">Rules</a></p>
            <p><a href="#">History</a></p>
            <p><a href="#">Calendar</a></p>
            <p><a href="#">Language</a></p>
        </div>
        <div class="col-sm-8 text-center">
            <h1>Welcome</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <hr>
            <h3> Who are you? </h3>
            <div class="btn-group-vertical" role="group">
                
                <a href="homepagePrincipal.php" class="btn btn-large btn-block btn-primary" role="button">Principal</a>
                <a href="homepageTeacher.php" class="btn btn-large btn-block btn-primary" role="button">Teacher</a>
                <a href="pseudoLogParent.php" class="btn btn-large btn-block btn-primary" role="button">Parent</a>
                <a href="homepageAdmin.php" class="btn btn-large btn-block btn-primary" role="button">Admin</a>
            </div>
        </div>
        <div class="col-sm-2 sidenav">
            <div class="well">
                <p>ADS</p> <!--Logo facebook -->
            </div>
            <div class="well">
                <p>ADS</p> <!--Logo istagram -->
            </div>
        </div>
    </div>
</div>

<?php
require_once("defaultFooter.php")
?>