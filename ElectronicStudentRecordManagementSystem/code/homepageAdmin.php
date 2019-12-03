<?php
require_once "basicChecks.php";

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedAdminNavbar.php";
}

/* FIXME remove the next lines when login is implemented */

/* $_SESSION['user'] = 'GDILRI';
$_SESSION['role'] = 'admin'; */

/* End lines to be changed*/
?>

<div class="text-center">
    <?php
        echo "<h1>Welcome to your homepage " . $_SESSION["user"] . "!</h1><br>";
    ?>
    <div class="btn-group">
        <a href="enrollstudent.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-user pull-left" aria-hidden="true"></span>&emsp;Enroll new student</a>
    </div><br>
    <div class="btn-group">
        <a href="mailInterface.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-envelope pull-left" aria-hidden="true"></span>&emsp;Enable access parents</a>
    </div><br>
    <div class="btn-group">
        <a href="classComposition.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-list-alt pull-left" aria-hidden="true"></span>&emsp;Class composition</a>
    </div><br>
    <div class="btn-group">
        <a href="setupAccounts.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-cog pull-left" aria-hidden="true"></span>&emsp;Setup official accounts</a>
    </div><br>
    <div class="btn-group">
        <a href="publishCommunications.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-cog pull-left" aria-hidden="true"></span>&emsp;Publish communications</a>
    </div><br>
</div>

<?php
require_once "defaultFooter.php"
?>
