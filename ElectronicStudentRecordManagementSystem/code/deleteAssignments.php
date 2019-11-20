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
	$db = new dbTeacher();
    $error=0;
	
		if(isset($_POST['yes']) && $error == 0) {
			$db->deleteAssignments($_POST['assignmentstime'], $_POST['comboSubject'], $_POST['comboClass']);
			$_SESSION['yes']=$_POST['yes'];

	?>
			<div class="alert alert-success" role="alert">
				<p class="alert-link"> Assignments successfully deleted!</p>
			</div>
			<div>
			<form method="POST" action="">
				<button type="submit" class="btn btn-primary" onClick="this.form.action='homepageTeacher.php'">Homepage</button>
				<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllAssignments.php'">View all records</button>
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
				<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllAssignments.php'">View all records</button>
			</form>
			</div>
	<?php	

		} elseif(!isset($_POST['yes']) && !isset($_POST['no'])) {
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
		<li role="presentation"><a href="recordAssignemnts.php">New record</a></li>
		<li role="presentation"><a href="viewAllAssignments.php">View all records</a></li>
		<li role="presentation" class="active"><a href="#">Delete record</a></li>

	</ul>
	<div class="panel panel-default" align="center">
			<div class="panel-body">

				<form role="class" method="POST" action="deleteAssignments.php">

					<table class="table" name="lesson">
					
						<tr><td> Class: </td><td><?php echo $_SESSION['comboClass'];?>
						<input type="hidden" name="comboClass" value="<?php echo $_SESSION['comboClass'];?>">

						<tr><td> Subject: </td><td><?php echo $_SESSION['comboSubject'];?></td></tr>
						<input type="hidden" name="comboSubject" value="<?php echo $_SESSION['comboSubject'];?>">

						<tr><td> Date: </td><td><?php echo $_SESSION['assignmentstime'];?></td></tr>
						<input type="hidden" name="assignmentstime" value="<?php echo $_SESSION['assignmentstime'];?>">

						<tr><td> Topics: </td><td><?php echo $_SESSION['assignments'];?></td></tr>
						<input type="hidden" name="assignments" value="<?php echo $_SESSION['assignments'];?>">


					</table>
					<h1> Do you really want to delete these assignments? </h1>

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
