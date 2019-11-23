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
			$db->deleteMark($_POST['comboStudent'], $_POST['lessontime'], $_POST['comboHour']);
			$_SESSION['yes']=$_POST['yes'];

	?>
			<div class="alert alert-success" role="alert">
				<p class="alert-link"> Mark successfully deleted!</p>
			</div>
			<div>
			<form method="POST" action="">
				<button type="submit" class="btn btn-primary" onClick="this.form.action='homepageTeacher.php'">Homepage</button>
				<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllMarks.php'">View all marks</button>
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
				<button type="submit" class="btn btn-primary" onClick="this.form.action='viewAllMarks.php'">View all marks</button>
			</form>
			</div>
	<?php	

		} elseif(!isset($_POST['yes']) && !isset($_POST['no'])) {

			 if(!isset($_POST["comboStudent"]) || !isset($_POST["lessontime"]) || !isset($_POST["comboHour"]) || !isset($_POST["comboClass"]) || !isset($_POST["comboSubject"]) || !isset($_POST["comboGrade"])) {
					if(!isset($_SESSION['comboStudent']) || !isset($_SESSION['lessontime']) || !isset($_SESSION['comboHour']) || !isset($_SESSION['comboClass']) || !isset($_SESSION['comboSubject']) || !isset($_SESSION['comboGrade'])) {
						$error = 1;
					}
			} else {
                $_SESSION['comboClass'] = $_POST['comboClass'];
				$_SESSION['comboSubject'] = $_POST['comboSubject'];
				$_SESSION['comboStudent'] = $_POST['comboStudent'];
				$_SESSION['lessontime'] = $_POST['lessontime'];
                $_SESSION['comboHour'] = $_POST['comboHour'];
                $_SESSION['comboGrade'] = $_POST['comboGrade'];
               
			 }
						
	?>
<style>
    .form-control:focus {
        border-color: #ff80ff;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(255, 100, 255, 0.5);
    }
	#container {
		box-shadow: 0px 2px 25px rgba(0, 0, 0, .25);
		padding:0 15px 0 15px;
	}
</style>
	
	<ul class="nav nav-tabs">
		<li role="presentation"><a href="submitMarks.php">New mark</a></li>
		<li role="presentation"><a href="viewAllMarks.php">View all marks</a></li>
		<li role="presentation" class="active"><a href="#">Delete mark</a></li>

	</ul>
	<div class="panel panel-default" align="center" id="container">
			<div class="panel-body">

				<form role="class" method="POST" action="deleteMark.php">

					<table class="table" name="lesson">
					
						<tr><td> Class: </td><td><?php echo $_SESSION['comboClass'];?>

                        <tr><td> Subject: </td><td><?php echo $_SESSION['comboSubject'];?>

                        <tr><td> Student: </td><td><?php echo $teacher->getStudentByCod($_SESSION['comboStudent']) . " (" .  $_SESSION['comboStudent'] . ")";?>
						<input type="hidden" name="comboStudent" value="<?php echo $_SESSION['comboStudent'];?>">

						<tr><td> Date: </td><td><?php echo $_SESSION['lessontime'];?></td></tr>
						<input type="hidden" name="lessontime" value="<?php echo $_SESSION['lessontime'];?>">

						<tr><td> Hour: </td><td><?php echo $_SESSION['comboHour'];?></td></tr>
						<input type="hidden" name="comboHour" value="<?php echo $_SESSION['comboHour'];?>">

                        <tr><td> Grade: </td><td><?php echo $_SESSION['comboGrade'];?>

					</table>
					<h1> Do you really want to delete this mark? </h1>

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
