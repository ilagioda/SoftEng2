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
/* 	
	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher"; */
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
	$db = new dbTeacher();
    $error=0;
	
		if(isset($_POST['yes']) && $error == 0) {
			$db->deleteDailyLesson($_POST['lessontime'], $_POST['comboHour'], $_POST['comboClass']);
			$_SESSION['yes']=$_POST['yes'];

	?>
			<div class="alert alert-success" role="alert">
				<p class="alert-link"> Daily lesson successfully deleted!</p>
			</div>
			<div>
			<form method="POST" action="">
				<button type="submit" class="btn btn-primary" onClick="this.form.action='homepageTeacher.php'">Homepage</button>
				<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllLessonTopics.php'">View all records</button>
			</form>
			</div>
	<?php
		} elseif(isset($_POST['no']) && $error == 0) {
			$_SESSION['no']=$_POST['no'];
	?>
			<div class="alert alert-warning" role="alert">
				<p class="alert-link"> Operation aborted! </p>
			</div>
			<div>
			<form method="POST" action="">
				<button type="submit" class="btn btn-primary" onClick="this.form.action='homepageTeacher.php'">Homepage</button>
				<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllLessonTopics.php'">View all records</button>
			</form>
			</div>
	<?php	

		} elseif(!isset($_POST['yes']) && !isset($_POST['no'])) {
			 if(!isset($_POST["comboClass"]) || !isset($_POST["comboSubject"]) ||
				!isset($_POST["lessontime"]) || !isset($_POST["comboHour"]) || !isset($_POST["topics"]) 
				|| empty($_POST["topics"])){
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
			 }
						
	?>
	<ul class="nav nav-tabs">
		<li role="presentation"><a href="recordLesson.php">New record</a></li>
		<li role="presentation"><a href="viewAllLessonTopics.php">View all records</a></li>
		<li role="presentation" class="active"><a href="#">Delete record</a></li>

	</ul>
	<div class="panel panel-default" align="center">
			<div class="panel-body">

				<form role="class" method="POST" action="deleteRecordLesson.php">

					<table class="table" name="lesson">
					
						<tr><td> Class: </td><td><?php echo $_SESSION['comboClass'];?>
						<input type="hidden" name="comboClass" value="<?php echo $_SESSION['comboClass'];?>">

						<tr><td> Subject: </td><td><?php echo $_SESSION['comboSubject'];?></td></tr>
						<input type="hidden" name="comboSubject" value="<?php echo $_SESSION['comboSubject'];?>">

						<tr><td> Date: </td><td><?php echo $_SESSION['lessontime'];?></td></tr>
						<input type="hidden" name="lessontime" value="<?php echo $_SESSION['lessontime'];?>">

						<tr><td> Hour: </td><td><?php echo $_SESSION['comboHour'];?></td></tr>
						<input type="hidden" name="comboHour" value="<?php echo $_SESSION['comboHour'];?>">

						<tr><td> Topics: </td><td><?php echo $_SESSION['topics'];?></td></tr>
						<input type="hidden" name="topics" value="<?php echo $_SESSION['topics'];?>">


					</table>
					<h1> Do you really want to delete this lesson? </h1>

					<button name="yes" type="submit" class="btn btn-primary btn-lg">Yes</button>
					<button name="no" type="submit" class="btn btn-default btn-lg">No</button>
				</form>

			</div>
		</div>

		<?php
		}
	?>


<?php 
    require_once("defaultFooter.php")
?>
