<?php
require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "parent") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
    exit;
} else {

    if (!isset($_SESSION['childName'])) {
        header("Location: chooseChild.php");
        exit;
    }
    require_once "loggedParentNavbar.php";
}
require_once("db.php");

$childName = $_SESSION['childName'];
$childSurname = $_SESSION['childSurname'];
$childClass = $_SESSION['class'];

$db = new dbParent();

echo "<div class=text-center style='margin-bottom: 30px;'>";
echo "<h1>$childName $childSurname (class $childClass) TIMETABLE</h1><br>";

$timetableToShow = $db->retrieveChildTimetable($childClass);        

if(empty($timetableToShow)){
    echo <<<_ALERTMSG
    <div class="alert alert-warning alert-dismissible" role="alert">
        We're sorry. <strong>No timetable</strong> has been uploaded yet for class $childClass.
    </div> 
_ALERTMSG;
} else {
    echo <<<_OPENTABLE
    <div class="table-responsive">
    <table class="table table-striped table-bordered text-center">
    <tr style="font-size: 20px;"><td></td><td><b>Monday</b></td><td><b>Tuesday</b></td><td><b>Wednesday</b></td><td><b>Thursday</b></td><td><b>Friday</b></td></tr>
_OPENTABLE;

    // Prepare arrays which will be useful when filling the HTML table 
    $hours = array("8:00","9:00", "10:00", "11:00", "12:00", "13:00");
    $cont = 0;

    for($i=1; $i<=6; $i++){
        $hour = $hours[$i-1];
        $mon = $timetableToShow[$i]["mon"];
        $tue = $timetableToShow[$i]["tue"];
        $wed = $timetableToShow[$i]["wed"];
        $thu = $timetableToShow[$i]["thu"];
        $fri = $timetableToShow[$i]["fri"];

        echo <<<_ROW
        <tr>
        <td style="vertical-align: middle;"><b>$hour<b></td>
        <td style="vertical-align: middle;" id="mon_$i">$mon</td>
        <td style="vertical-align: middle;" id="tue_$i">$tue</td>
        <td style="vertical-align: middle;" id="wed_$i">$wed</td>
        <td style="vertical-align: middle;" id="thu_$i">$thu</td>
        <td style="vertical-align: middle;" id="fri_$i">$fri</td>
        </tr>
_ROW;
    }

    echo <<<_CLOSETABLE
        </table>
        </div>
_CLOSETABLE;

}

echo "</div>";

require_once("defaultFooter.php");
?>