<?php

require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedTeacherNavbar.php";
}

//checkIfLogged();

echo "<div class=text-center>";
echo "<h2>Attendance</h2>";






echo "</div>";
require_once("defaultFooter.php");
?>