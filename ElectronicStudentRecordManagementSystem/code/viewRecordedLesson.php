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

	
/* 	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher"; */
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
    	$db = new dbTeacher();
	$error = 0;

    	if(!isset($_REQUEST["comboClass"]) || !isset($_REQUEST["comboSubject"]) ||
		!isset($_REQUEST["lessontime"]) || !isset($_REQUEST["comboHour"]) || !isset($_REQUEST["topics"]) 
		|| empty($_REQUEST["topics"])){
		if(!isset($_SESSION['comboClass']) || !isset($_SESSION['comboSubject']) ||
			!isset($_SESSION['lessontime']) || !isset($_SESSION['comboHour']) || !isset($_SESSION['topics'])) {

			$error = 1;
		}
	} else {
		$_SESSION['comboClass'] = $_POST['comboClass'];
		$_SESSION['comboSubject'] = $_POST['comboSubject'];
		$_SESSION['lessontime'] = $_POST['lessontime'];
		$_SESSION['comboHour'] = $_POST['comboHour'];
		$_SESSION['topics'] = $_POST['topics'];
		
		$result = $db->insertDailyLesson($_SESSION['lessontime'], $_SESSION['comboHour'], $_SESSION['comboClass'], $_SESSION['user'], $_SESSION['comboSubject'], $_SESSION['topics']);
		if($result == -1) {
			?>
			<div class='alert alert-danger' role='alert'>
				<p class="alert-link"> Lecture already inserted! </p>
				<p> Try to edit/delete the lecture in the section "<a href="viewAllLessonTopics.php">View all records</a>"</p>
			</div>
			<?php
		} else {

			if($error != 0){ ?>
				<div class='alert alert-danger' role='alert'>
					<p class="alert-link"> Oh no! Something went wrong... </p>
				</div>
			<?php
			} else {
			?> 
				<div class="alert alert-success" role="alert">
					<p class="alert-link"> Daily lesson successfully recorded!</p>
				</div>
			<?php 
			}
		}
	}
			?>
	<div>
	<form method="POST" action="">
		<button type="submit" class="btn btn-primary" onClick="this.form.action='homepageTeacher.php'">Homepage</button>
		<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllLessonTopics.php'">View all records</button>
	</form>
	</div>

<?php 
    require_once("defaultFooter.php")
?>
