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

	}
    
?>

<ul class="nav nav-tabs">

  <li role="presentation"><a href="recordLesson.php">New record</a></li>
  <li role="presentation"><a href="viewAllLessonTopics.php">View all records</a></li>
  <li role="presentation" class="active"><a href="#">Edit record</a></li>

</ul>

<div class="panel panel-default">
	<div class="panel-body">
	<h1> Update daily lesson topics </h1>
		<form class="navbar-form navbar-left" role="class" method="POST" action="updateRecordedLesson.php">
			<table class="table">
				<tr><td><label>Class </label></td><td>
				<input class="form-control" disabled style="width:100%" value="
				<?php 
					$selectedClass = $_SESSION["comboClass"];
					echo $selectedClass;
				?>"> 
				
				<input hidden name="comboClass" value="<?php echo $selectedClass ?>"/>


				</td></tr>
				<tr><td><label>Subject </label></td><td>
				<select class="form-control" id="comboSubject" name="comboSubject" style="width:100%">	
				<?php 
					$selectedSubject = $_SESSION["comboSubject"];
				?>				
				<option value="<?php echo $selectedSubject ?>" selected> 
				<?php echo $selectedSubject ?>
				</option>
				<?php 
					$subjects=$teacher->getSubjectByClassAndTeacher($selectedClass);
					foreach($subjects as $value) {
						if($value != $selectedSubject)
							echo "<option value=".$value.">".$value."</option>";
					}		
				?>
				</select></td></tr>
				<tr><td><label>Date</label></td><td>  
				<input class="form-control" disabled type="date" value="<?php echo $_SESSION["lessontime"]; ?>"
						style="width:100%" required>
				<input hidden name="lessontime" value="<?php echo $_SESSION["lessontime"]; ?>"/>
						</td></tr>
				<tr><td><label>Hour</label></td><td>
				<input disabled name="comboHour" style="width:100%" value="<?php 
					$selectedHour = $_SESSION["comboHour"];
					echo $selectedHour;
				?>">
				<input hidden name="comboHour" value="<?php echo $selectedHour ?>"/>	
				</td></tr>	
				<tr><td><label>Topic(s)</label></td><td>
				<textarea class="form-control" name="topics" rows="4" cols="50" style="width:100%" required>
				<?php echo $_SESSION["topics"]; ?>
				</textarea></td></tr>
	
				<tr><td></td><td><button type="submit" class="btn btn-success">Update</button></td></tr>
			</table>
		</form>
	</div>
</div>



<?php
	require_once("defaultFooter.php")
?>