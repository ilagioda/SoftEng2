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

    if(!isset($_REQUEST["comboClass"]) || !isset($_REQUEST["comboSubject"]) ||
		!isset($_REQUEST["absencetime"]) || !isset($_REQUEST["comboHour"]) || !isset($_REQUEST["comboStudent"]) 
		|| !isset($_REQUEST["absencenote"]) || empty($_REQUEST["topics"])){
			
		if(!isset($_SESSION['comboClass']) || !isset($_SESSION['comboSubject']) || !isset($_SESSION["comboStudent"])
			!isset($_SESSION['absencetime']) || !isset($_SESSION['comboHour']) || !isset($_SESSION['absencenote'])) {

			$error = 1;
		}
	} else {
		$_SESSION['comboClass'] = $_POST['comboClass'];
		$_SESSION['comboSubject'] = $_POST['comboSubject'];
		$_SESSION['comboStudent'] = $_POST['comboStudent'];
		$_SESSION['absencetime'] = $_POST['absencetime'];
		$_SESSION['comboHour'] = $_POST['comboHour'];
		$_SESSION['absencenote'] = $_POST['absencenote'];
		
		$result = $db->insertAbsenceNote($_SESSION['absencetime'], $_SESSION['comboHour'], CODFISC, $_SESSION['comboClass'], $_SESSION['user'], $_SESSION['comboSubject'], $_SESSION['absencenote']);
		if($result == -1) {
			?>
			<div class='alert alert-danger' role='alert'>
				<p class="alert-link"> Absence note already inserted! </p>
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
					<p class="alert-link"> Absence note successfully recorded!</p>
				</div>
			<?php 
			}
		}
	}
			?>
	<div>
	<form method="POST" action="">
		<button type="submit" class="btn btn-primary" onClick="this.form.action='homepageTeacher.php'">Homepage</button>
		<button type="submit" class="btn btn-primary" onClick="this.form.action='absenceNote.php'">New absence note</button>
	</form>
	</div>

<?php 
    require_once("defaultFooter.php")
?>
