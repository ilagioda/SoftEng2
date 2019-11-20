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
	$teacher=new Teacher();
	
    if(!isset($_REQUEST["comboClass"]) || !isset($_REQUEST["comboSubject"]) ||
		!isset($_REQUEST["assignmentstime"]) || !isset($_REQUEST["assignments"]) 
		|| empty($_REQUEST["assignments"])){
		if(!isset($_SESSION['comboClass']) || !isset($_SESSION['comboSubject']) ||
			!isset($_SESSION['assignmentstime']) || !isset($_SESSION['assignments'])) {

			$error = 1;
		}
	} else {
		$_SESSION['comboClass'] = $_POST['comboClass'];
		$_SESSION['comboSubject'] = $_POST['comboSubject'];
		$_SESSION['assignmentstime'] = $_POST['assignmentstime'];
		$_SESSION['assignments'] = $_POST['assignments'];
	}
    
?>

<ul class="nav nav-tabs">

  <li role="presentation"><a href="recordAssignments.php">New record</a></li>
  <li role="presentation"><a href="viewAllAssignments.php">View all records</a></li>
  <li role="presentation" class="active"><a href="#">Edit record</a></li>

</ul>

<div class="panel panel-default">
	<div class="panel-body">
	<h1> Update assignments </h1>
		<form class="navbar-form navbar-left" role="class" method="POST" action="updateAssignments.php">
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
				<input class="form-control" disabled style="width:100%" value="	
				<?php 
					$selectedSubject = $_SESSION["comboSubject"];
					echo $selectedSubject;
				?>">
				
				<input hidden name="comboSubject" value="<?php echo $selectedSubject ?>"/>

				</td></tr>
				<tr><td><label>Date</label></td><td>  
				<input class="form-control" disabled type="date" value="<?php echo $_SESSION["assignmentstime"]; ?>"
						style="width:100%" required>
				<input hidden name="assignmentstime" value="<?php echo $_SESSION["assignmentstime"]; ?>"/>
						</td></tr>
				<tr><td><label>Topic(s)</label></td><td>
				<textarea class="form-control" name="assignments" rows="4" cols="50" style="width:100%" required>
				<?php echo $_SESSION["assignments"]; ?>
				</textarea></td></tr>
	
				<tr><td></td><td><button type="submit" class="btn btn-success">Update</button></td></tr>
			</table>
		</form>
	</div>
</div>



<?php
	require_once("defaultFooter.php")
?>