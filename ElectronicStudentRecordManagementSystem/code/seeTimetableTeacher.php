<?php

require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    header("Location: login.php");
} else {
    require_once "loggedTeacherNavbar.php";
}

require_once("classTeacher.php");

// Create a Teacher object
$teacher = new Teacher();
$teacherSSN = $_SESSION['user'];
$db = new dbTeacher();

echo "<div class='text-center' style='margin-bottom: 30px;'>";
echo "<h1>$teacherSSN TIMETABLE</h1><br>";

$timetableToShow = $db->retrieveTimetableTeacher($teacherSSN); 

if(empty($timetableToShow)){
    echo <<<_ALERTMSG
    <div class="alert alert-danger alert-dismissible" role="alert">
        Oh no, something went wrong during the retrieval of the timetable!
    </div> 
_ALERTMSG;
} else {
    echo <<<_OPENTABLE
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
            <tr style="font-size: 20px;"><td></td><td><b>Monday</b></td><td><b>Tuesday</b></td><td><b>Wednesday</b></td><td><b>Thursday</b></td><td><b>Friday</b></td></tr>
_OPENTABLE;

    // Call the function that prints the timetable
    printTimetable($timetableToShow);

    echo <<<_CLOSETABLE
        </table>
    </div>
_CLOSETABLE;

}

echo "</div>";

require_once("defaultFooter.php");

?>
