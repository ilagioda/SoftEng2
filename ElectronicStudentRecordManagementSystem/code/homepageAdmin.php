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

?>

<div class="text-center">
    <?php
        echo "<h1>Welcome to your homepage " . $_SESSION["user"] . "!</h1><br>";
    ?>
    <div class="btn-group">
        <a href="enrollstudent.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-user pull-left" aria-hidden="true"></span>&emsp;Enroll student</a>
    </div><br>
    <div class="btn-group">
        <a href="mailInterface.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-envelope pull-left" aria-hidden="true"></span>&emsp;Enable access parents</a>
    </div><br>
    <div class="btn-group">
        <a href="classComposition.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-list-alt pull-left" aria-hidden="true"></span>&emsp;Class composition</a>
    </div><br>
    <?php
    if($_SESSION['sysAdmin']==1)
        echo <<<_SETUPACCOUNTSBUTTON
        <div class="btn-group">
            <a href="setupAccounts.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-cog pull-left" aria-hidden="true"></span>&emsp;Setup official accounts</a>
        </div><br>
_SETUPACCOUNTSBUTTON;
    ?>
    <div class="btn-group">
        <a href="publishCommunications.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-bullhorn pull-left" aria-hidden="true"></span>&emsp;Publish general communications</a>
    </div><br>
    <div class="btn-group">
            <a href="publishInternalCommunications.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-bullhorn pull-left" aria-hidden="true"></span>&emsp;Publish internal communications</a>
        </div><br>
    <div class="btn-group">
        <a href="publishTimetable.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-time pull-left" aria-hidden="true"></span>&emsp;Publish timetables</a>
    </div><br>
    <div class="btn-group">
        <a href="manageTeachers.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-cog pull-left" aria-hidden="true"></span>&emsp;Manage Teachers</a>
    </div><br>
    <div class="btn-group">
    <a href="changePassword.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-sunglasses pull-left" aria-hidden="true"></span>&emsp;Change Password</a>
    </div><br><br>
</div>

<?php
require_once "defaultFooter.php"
?>
