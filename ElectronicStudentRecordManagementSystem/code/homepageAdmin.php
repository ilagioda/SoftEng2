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
<h1 align="center"> ADMIN HOMEPAGE </h1>
<div class="text-center">
	<p>
        <?php
echo "<h2>Welcome to your homepage ADMIN " . $_SESSION["user"] . "!</h2><br>";
?>

	</p>
    <a href="enrollstudent.php" class="btn btn-primary btn-lg" role="button"><span class="glyphicon glyphicon-user pull-left" aria-hidden="true"></span>&emsp;Enroll new student</a><br><br>
    <a href="mailInterface.php" class="btn btn-primary btn-lg" role="button"><span class="glyphicon glyphicon-envelope pull-left" aria-hidden="true"></span>&emsp;Enable access parents</a><br><br>
    <a href="classComposition.php" class="btn btn-primary btn-lg" role="button"><span class="glyphicon glyphicon-list-alt pull-left" aria-hidden="true"></span>&emsp;Class composition</a><br><br>
    <a href="setupAccounts.php" class="btn btn-primary btn-lg" role="button"><span class="glyphicon glyphicon-book pull-left" aria-hidden="true"></span>&emsp;Setup official accounts</a><br><br>
</div>

<?php
require_once "defaultFooter.php"
?>
