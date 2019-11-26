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
}
    require_once "loggedParentNavbar.php";
}

//checkIfLogged();

echo "<div class=text-center>";
echo "<h2>Welcome to your homepage PARENT " . $_SESSION["user"] . "!</h2>";
echo "<h3>You can:</h3>";

echo <<< _OPLIST
<div class="btn-group-vertical" role="group">
    <a href="viewMarks.php" class="btn btn-primary main btn-lg" role="button">View $_SESSION[childName]'s marks</a>
    <a href="" class="btn btn-primary main btn-lg" role="button">To be implemented...</a>
</div>
_OPLIST;

echo "</div>";
require_once("defaultFooter.php");
?>