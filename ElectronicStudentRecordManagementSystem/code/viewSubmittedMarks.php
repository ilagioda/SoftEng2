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
	
	$error = 0;	
	
    if(!isset($_POST["comboSubject"]) || !isset($_POST["lessontime"]) || 
			!isset($_POST["comboHour"]) || !isset($_POST["comboGrade"]) 
			|| !isset($_POST["ssn"])){			
			
		if(!isset($_SESSION['comboSubject']) || !isset($_SESSION['lessontime']) || 
			!isset($_SESSION['comboHour']) || !isset($_SESSION['comboGrade']) || 
			!isset($_SESSION["ssn"])) {
			
			$error = 1;
		}
		
	} else {
		
		$_SESSION['comboSubject'] = $_POST['comboSubject'];
		$_SESSION['lessontime'] = $_POST['lessontime'];
		$_SESSION['comboHour'] = $_POST['comboHour'];
        $_SESSION['comboGrade'] = $_POST['comboGrade'];
		$_SESSION['ssn'] = $_POST['ssn'];
        
		foreach($_SESSION['ssn'] as $key => $value) {
			if($_SESSION['comboGrade'][$key] > 0) {
				
				$result = $db->insertGrade($_SESSION['lessontime'], $_SESSION['comboHour'], $value, $_SESSION['comboSubject'], $_SESSION['comboGrade'][$key]);
				if($result == -1) {
					$error = 1;
				}		
		}

	}
	if($error != 0){ ?>
		<div class='alert alert-danger' role='alert'>
			<p class="alert-link"> Oh no! Something went wrong... </p>
		</div>
<?php
	} else {
?> 
		<div class="alert alert-success" role="alert">
			<p class="alert-link"> Mark successfully recorded!</p>
		</div>
<?php 
	}	
?>
	<div>
	<form method="POST" action="">
		<button type="submit" class="btn btn-primary" onClick="this.form.action='homepageTeacher.php'">Homepage</button>
		<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllMarks.php'">View all marks</button>
	</form>
	</div>

<?php 
    require_once("defaultFooter.php")
?>
