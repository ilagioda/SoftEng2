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

	require_once("classTeacher.php");
	$teacher=new Teacher();
    	$db = new dbTeacher();
	$error = 0;

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
		
		$result = $db->insertNewAssignments($_SESSION['assignmentstime'], $_SESSION['comboClass'], $_SESSION['user'], $_SESSION['comboSubject'], $_SESSION['assignments']);
		if($result == -1) {
			?>
			<div class='alert alert-danger' role='alert'>
				<p class="alert-link"> Assignments already inserted for that subject! </p>
				<p> Try to edit the assignments in the section "<a href="viewAllAssignments.php">View all records</a>"</p>
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
					<p class="alert-link"> Assigments successfully recorded!</p>
				</div>
			<?php 
			}
		}
	}
			?>
	<div>
	<form method="POST" action="">
		<button type="submit" class="btn btn-primary" onClick="this.form.action='homepageTeacher.php'">Homepage</button>
		<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllAssignments.php'">View all records</button>
	</form>
	</div>

<?php 
    require_once("defaultFooter.php")
?>
