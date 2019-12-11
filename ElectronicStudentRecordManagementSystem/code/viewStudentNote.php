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
$parentDB = new dbParent();

//checkIfLogged();

echo "<div class=text-center>";

?>
<p> This page has the aim of letting you see the diplinar notes of the selected child.</p>
<?php
        //More than one child
        if (isset($_SESSION['childName']) && isset($_SESSION['childSurname'])) {

            echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'
                . $_SESSION["childName"] . ' ' . $_SESSION["childSurname"] .'</a>';
        }


        $ssnStudent = $_SESSION['child'];

                $disciplinarNotes = $parentDB->retrieveStudentNotes($ssnStudent);
echo "</div>";
require_once("defaultFooter.php");
