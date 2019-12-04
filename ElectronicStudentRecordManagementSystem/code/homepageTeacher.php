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

/* FIXME remove the next lines when login is implemented */

//$_SESSION['user'] = 'GNV';
//$_SESSION['role'] = 'teacher';

/* End lines to be changed*/
?>

<div class="text-center">
	<?php
	echo "<h1>Welcome to your homepage " . $_SESSION["user"] . "!</h1><br>";
	?>
	<div class="btn-group">
		<button type="button" class="btn btn-primary main dropdown-toggle btn-lg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="glyphicon glyphicon-list pull-left" aria-hidden="true"></span>&emsp;
			Daily lesson topics <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li><a href="recordLesson.php">New record</a></li>
			<li><a href="viewAllLessonTopics.php">View all records</a></li>
		</ul>
	</div>
	<br>
	<div class="btn-group">
		<button type="button" class="btn btn-primary main dropdown-toggle btn-lg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="glyphicon glyphicon-education pull-left" aria-hidden="true"></span>&emsp;
			Insert mark <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li><a href="submitMarks.php">New record</a></li>
			<li><a href="viewAllMarks.php">View all records</a></li>
		</ul>
	</div>
	<br>

	<div class="btn-group">
		<button type="button" class="btn btn-primary main dropdown-toggle btn-lg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="glyphicon glyphicon-book pull-left" aria-hidden="true"></span>&emsp;
			Assignments <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li><a href="recordAssignments.php">New record</a></li>
			<li><a href="viewAllAssignments.php">View all records</a></li>
		</ul>
	</div>
	<br>
	<div class="btn-group">
		<a href="attendance.php" class="btn btn-primary main btn-lg" role="button"><span class=" glyphicon glyphicon-time pull-left" aria-hidden="true"></span>&emsp;Attendance</a>
	</div>
	<br>
	<div class="btn-group">
		<a href="publishSupportMaterial.php" class="btn btn-primary main btn-lg" role="button"><span class=" glyphicon glyphicon-time pull-left" aria-hidden="true"></span>&emsp;Publish material</a>
	</div>
</div>
<?php
require_once("defaultFooter.php")
?>