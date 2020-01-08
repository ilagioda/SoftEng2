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

require_once("classTeacher.php");

// Create a Teacher object
$teacher = new Teacher();
$classes = $teacher->getClassesByTeacher();

echo "<div class=text-center>";
echo "<h1>TIMETABLES</h1><br>";

if (isset($_REQUEST['class'])) {

    $chosenClass = $_REQUEST['class'];

    $db = new dbTeacher();
    $teacherSSN = $_SESSION['user'];

    echo "<div class=text-center style='margin-bottom: 30px;'>";
    echo "<h2>Class $chosenClass</h2><br>";

    $timetableToShow = $db->retrieveTimetableTeacher($chosenClass, $teacherSSN); 

    if(empty($timetableToShow)){
        echo <<<_ALERTMSG
        <div class="alert alert-warning alert-dismissible" role="alert">
            We're sorry. <strong>No timetable</strong> has been uploaded yet for class $chosenClass.
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

} else {
    // The class hasn't been chosen yet, so the list of classes must be shown
    echo <<<_LIST
        <ul class="list-group">
_LIST;

    foreach ($classes as $class) {
        echo <<<_ROW
            <a href="seeTimetableTeacher.php?class=$class" class="list-group-item">$class</a>     
_ROW;
    }

    echo <<<_ENDLIST
        </ul>
_ENDLIST;

}

echo "</div>";

require_once("defaultFooter.php");
?>
