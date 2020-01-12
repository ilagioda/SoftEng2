<?php
require_once "basicChecks.php";

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    header("Location: login.php");
	exit;
} 

require_once "db.php";
$CLASSINDEX='classIndex';

if (isset($_REQUEST[$CLASSINDEX])) {

    if(!ctype_digit($_REQUEST[$CLASSINDEX])){
        // trying to hack the page => redirect to homepage
        header('HTTP/1.1 307 Temporary Redirect');
        header('Location: index.php');
        exit;
    }

    /* coming from the same page, choosing the class */
    $index = $_REQUEST[$CLASSINDEX];
    $_SESSION['comboClass'] = $_SESSION['classes'][$index];
    $_SESSION[$CLASSINDEX] = $index;
    header('HTTP/1.1 307 Temporary Redirect');
    header('Location: homepageTeacher.php');
    exit;
}

if (isset($_SESSION['user'])) {

    $db = new dbTeacher();
    $classes = $db->getClassesByTeacher2($_SESSION['user']);

    switch (count($classes)) {

        case 0:
            // no classes for that teacher => display error
            require_once "loggedTeacherNavbar.php";
            echo <<<_ERROR
            <div class="text-center">
            <h1> No classes assigned. Please contact the administrator. </h1>
            </div>
_ERROR;
            exit;

        case 1:
            // only one class
            $_SESSION['comboClass'] = $classes[0];
            $_SESSION['numClasses'] = 1;
            $_SESSION['classes'] = $classes;
            $_SESSION[$CLASSINDEX] = 0;
            header('HTTP/1.1 307 Temporary Redirect');
            header('Location: homepageTeacher.php');
            break;

        default:
            // more than one class, display classes to be chosen
            $_SESSION['numClasses'] = count($classes);
            $_SESSION['classes'] = $classes;
            break;
    }

} else {
    header('HTTP/1.1 307 Temporary Redirect');
    header('Location: index.php');
    exit;
}

require_once "loggedTeacherNavbar.php";

?>

<div>
    <h2 class="text-center"> Choose a class</h2>
    <form class="form-horizontal" action="chooseClass.php" method="POST">
		<div class="form-group text-center">
			<table class="table table-hover">


<?php
$i = 0;
// print all the classes, one per row
foreach ($_SESSION['classes'] as $class) {
    echo <<<_CLASSROW

                <tr>
                    <td><button type="submit" class="btn btn-default" name=$CLASSINDEX value=$i style="border:none; background: none; outline: none !important; width:100%">$class</td>
                </tr>
_CLASSROW;
    $i++;
}
?>
			</table>
		</div>
    </form>
	<div class="text-center">

		<h3> Or... </h3><br>
		<div class="btn-group">
			<a href="provideParentMeetingSlots.php" class="btn btn-primary main btn-lg" role="button"><span class=" glyphicon glyphicon-comment pull-left" aria-hidden="true"></span>&emsp;Provide parent meetings</a>
		</div>
		<br>
		<div class="btn-group">
			<a href="seeTimetableTeacher.php" class="btn btn-primary main btn-lg" role="button"><span class="glyphicon glyphicon-time pull-left" aria-hidden="true"></span>&emsp;See timetables</a>
		</div>
		<br>
	</div>
</div>

<?php
require_once "defaultFooter.php"
?>