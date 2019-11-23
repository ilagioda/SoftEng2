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
	
/* 	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher"; */
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
    $db = new dbTeacher();
	$error = 0;
  ?>
  
 
  <?php
    if(!isset($_REQUEST["comboStudent"]) || !isset($_REQUEST["comboSubject"]) ||
        !isset($_REQUEST["lessontime"]) || !isset($_REQUEST["comboHour"]) || !isset($_REQUEST['comboGrade'])){
    
        if(!isset($_SESSION['comboStudent']) || !isset($_SESSION['comboSubject']) ||
            !isset($_SESSION['lessontime']) || !isset($_SESSION['comboHour']) || !isset($_SESSION['comboGrade'])) {

            $error = 1;
    }
	} else {
		$_SESSION['comboStudent'] = $_POST['comboStudent'];
		$_SESSION['comboSubject'] = $_POST['comboSubject'];
		$_SESSION['lessontime'] = $_POST['lessontime'];
        $_SESSION['comboHour'] = $_POST['comboHour'];
        $_SESSION['comboGrade'] = $_POST['comboGrade'];


		$db->updateMark($_SESSION['comboStudent'], $_SESSION['comboSubject'], $_SESSION['lessontime'], $_SESSION['comboHour'], $_SESSION['comboGrade']);
	}
	

    if($error != 0){ ?>
		<div class='alert alert-danger' role='alert'>
			<p class="alert-link"> Oh no! Something went wrong... </p>
		</div>
    <?php
	} else {
    ?> 
	
		<div class="alert alert-success" role="alert">
			<p class="alert-link"> Mark successfully updated!</p>
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
