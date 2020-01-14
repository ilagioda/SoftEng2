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

//checkIfLogged();

echo "<div class=text-center>";
echo "<h1>Welcome to your homepage " . $_SESSION["user"] . "!</h1><br>";

echo <<< _OPLIST
<div class="text-center">
    <div class="btn-group">
        <a href="viewMarks.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-education pull-left" aria-hidden="true"></span>&emsp;Marks</a>
    </div><br>
    <div class="btn-group">
        <a href="studentAttendance.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-calendar pull-left" aria-hidden="true"></span>&emsp;Attendance</a>
    </div><br>
    <div class="btn-group">
        <a href="viewChildAssignment.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-file pull-left" aria-hidden="true"></span>&emsp;Assignments</a>
    </div><br>
    <div class="btn-group">
        <a href="viewChildLessonTopics.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-list pull-left" aria-hidden="true"></span>&emsp;Lectures</a>
    </div><br>
    <div class="btn-group">
        <a href="viewSupportMaterial.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-book pull-left" aria-hidden="true"></span>&emsp;Materials</a>
    </div><br>
    <div class="btn-group">
        <a href="seeTimetable.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-time pull-left" aria-hidden="true"></span>&emsp;Timetable</a>
    </div><br>
	<div class="btn-group">
        <a href="viewFinalGrades.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-education pull-left" aria-hidden="true"></span>&emsp;Final grades</a>
    </div><br>
    <div class="btn-group">
        <a href="selectTeacherForBooking.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-comment pull-left" aria-hidden="true"></span>&emsp;Parent meetings</a>
    </div><br>
	<div class="btn-group">
    <a href="viewStudentNote.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-bookmark pull-left" aria-hidden="true"></span>&emsp;Disciplinar Note</a>
    </div>
	<br>
    <div class="btn-group">
    <a href="changePassword.php" class="btn btn-primary btn-lg main" role="button"><span class="glyphicon glyphicon-sunglasses pull-left" aria-hidden="true"></span>&emsp;Change Password</a>
    </div><br>
    <br>
</div>
_OPLIST;

echo "</div>";
?>

<br>
<br>
<div class="overflow-auto">
                <?php
                $res = $parent->getInternalAnnouncements($parent->getChildClass($_SESSION['child']));
                if(!$res) echo "<p>Some problem occurred. We're sorry.</p>";
                else{
                    if($res->num_rows == 0) 
                        echo <<<_NOANNOUNCEMENT
                        <div class="card-index">
                        <div class="card-header-index text-left">
                            NONE
                        </div>
                        <div class="card-body-index">
                            <h5 class="card-title-index"><strong> NONE </strong></h5>
                            <p>No announcement to be shown.</p>
                        </div>
                    </div>
_NOANNOUNCEMENT;
                    else{
                        foreach($res as $tuple){
                            $timestamp = $tuple['timestamp'];
                            $text = $tuple['text'];
                            $title = $tuple['title'];

                            echo <<<_ANNOUNCEMENT
                            <div class="card-index">
                                <div class="card-header-index text-left">
                                    $timestamp
                                </div>
                                <div class="card-body-index">
                                    <h5 class="card-title-index"><strong> $title </strong></h5>
                                    <p>$text</p>
                                </div>
                            </div>
_ANNOUNCEMENT;
                        }
                    }
                }


                ?>
            </div>

<?php
require_once("defaultFooter.php");
?>