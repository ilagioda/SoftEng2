<?php
require_once "basicChecks.php";

$loggedin = false;
if (isset($_SESSION['user'])) {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    require_once "defaultNavbar.php";
} else {
    require_once "loggedNavbar.php";
}
/**
 *  To be removed => here until logout is implemented
 * */
//$_SESSION = array();
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
            <img id="central_image" src="images/school_logo.png">
        </div>
        <div class="col-sm-2 sidenav">
            <div class="well">
                <p><img class="ads_logos" src="images/fb_logo.png"></p> <!--Logo facebook -->
            </div>
            <div class="well">
                <p><img class="ads_logos" src="images/instagram_logo.png"></p> <!--Logo instagram -->
            </div>
            <div class="well">
                <p><img class="ads_logos" src="images/whatsapp_logo.png"></p> <!--Logo instagram -->
            </div>
            <div class="well">
                <p><img class="ads_logos" src="images/twitter_logo.png"></p> <!--Logo instagram -->
            </div>
        </div>
    </div>
</div>

<?php
require_once "defaultFooter.php"
?>