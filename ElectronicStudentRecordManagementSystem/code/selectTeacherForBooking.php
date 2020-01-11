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
require_once("db.php");
$parent = new dbParent();

?>


<h1 class="display-1 text-center">Select the teacher you want to book a meeting with</h1>

<br><br>

<form action="bookParentMeeting.php" method="POST">
<table class="table table-striped">

<?php

    $teachers=$parent->getTeachersByChild($_SESSION["child"]);


    foreach($teachers as $t){
        

        echo "<tr>";
        echo "<td> $t[surname] $t[name] </td>";
        echo "<td><button type=\"submit\" class=\"btn btn-default btn-sm\" name='teacher' value='$t[codFisc]'>Select</td>";
        echo "</tr>";
    }
    
?>

</table>
</form>

<?php
require_once("defaultFooter.php");
?>