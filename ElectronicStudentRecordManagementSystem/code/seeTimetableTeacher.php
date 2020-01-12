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

// Print the teacher's timetable 
echo "<div class='text-center'>";
echo "<h2>Your TIMETABLE</h2><br>";

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

// Print the timetable of the teacher's classes
$classes = $teacher->getClassesByTeacher();
if(!empty($classes)){
    echo <<< _INTRO
        <div class=text-center>
            <br><h3>Below you can see the timetables of your classes...</h3><br>
        </div>
_INTRO;
}
foreach ($classes as $chosenClass) {

    echo "<div class=text-center>";
    echo "<h2>Class $chosenClass TIMETABLE</h2><br>";

    $timetableToShow = $db->retrieveTimetableOfAClass($chosenClass, $teacherSSN); 

    if(empty($timetableToShow)){
        echo <<<_ALERTMSG
        <div class="alert alert-warning alert-dismissible" role="alert">
            <strong>No timetable</strong> has been uploaded yet for class $chosenClass.
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
        </div>
    _CLOSETABLE;
    
    }

    echo "</div>";

}

echo "</div>";

require_once("defaultFooter.php");

?>
