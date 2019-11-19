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
    require_once "loggedNavbar.php";
}
    
	/* FIXME remove the next lines when login is implemented */
    
    //$_SESSION['user'] = 'GNV';
    //$_SESSION['role'] = 'teacher';
    
    /* End lines to be changed*/    
?>

<div class="text-center">
	<h1> TEACHER HOMEPAGE </h1>
        <?php
            echo "<h2>Welcome to your homepage TEACHER ".$_SESSION["user"]."!</h2><br>";
        ?>
	<form method="POST" action="dailyLessonTopics.php">
		<div class="btn-group">
		<button type="button" class="btn btn-primary dropdown-toggle btn-lg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&emsp;
			Daily lesson topics <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
		<li><a href="recordLesson.php">New record</a></li>
		<li><a href="viewAllLessonTopics.php">View all records</a></li>
		</ul>
		</div>
	</form>
		<br>
		<a href="selectClassForMarks.php" class="btn btn-primary btn-lg" role="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>&emsp;Add student mark</a>
</div>

<?php 
    require_once("defaultFooter.php")
?>
