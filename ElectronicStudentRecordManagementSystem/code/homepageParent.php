<?php

require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "parent") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
if(!isset($_SESSION['childName'])){
    header("Location: chooseChild.php");
    exit;
}
    require_once "loggedParentNavbar.php";
}

//checkIfLogged();

echo "<div class=text-center>";
echo "<h1>Welcome to your homepage " . $_SESSION["user"] . "!</h1><br>";

echo <<< _OPLIST
<div class="text-center">
    <div class="btn-group">
        <a href="viewMarks.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-education pull-left" aria-hidden="true"></span>&emsp;Marks</a>
    </div><br>
    <div class="btn-group">
        <a href="studentAttendance.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-time pull-left" aria-hidden="true"></span>&emsp;Attendance</a>
    </div><br>
    <div class="btn-group">
        <a href="viewChildAssignment.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-book pull-left" aria-hidden="true"></span>&emsp;Assignments</a>
    </div><br>
    <div class="btn-group">
        <a href="viewSupportMaterial.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-book pull-left" aria-hidden="true"></span>&emsp;Materials</a>
    </div><br>
</div>
_OPLIST;

echo "</div>";
require_once("defaultFooter.php");
?>