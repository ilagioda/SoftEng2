<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");

/**
 *  To be removed => here until logout is implemented
 * */
$_SESSION = array();
?>

        <div class="col-sm-8 text-left">
            <h1>Welcome</h1>
            <hr>
            <h2> Who are you? </h2>
            <div class="btn-group-vertical" role="group">
                
                <a href="homepagePrincipal.php" class="btn btn-large btn-block btn-info" role="button">Principal</a>
                <a href="homepageTeacher.php" class="btn btn-large btn-block btn-info" role="button">Teacher</a>
                <a href="pseudoLogParent.php" class="btn btn-large btn-block btn-info" role="button">Parent</a>
                <a href="homepageAdmin.php" class="btn btn-large btn-block btn-info" role="button">Admin</a>
            </div>
        </div>
 

<?php
require_once("defaultFooter.php")
?>