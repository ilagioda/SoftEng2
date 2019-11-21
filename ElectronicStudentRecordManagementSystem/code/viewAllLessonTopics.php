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
    
?>

<style> 
	#container {
		box-shadow: 0px 2px 25px rgba(0, 0, 0, .25);
		padding:0 15px 0 15px;
	}
</style>
<ul class="nav nav-tabs">
  <li role="presentation"><a href="recordLesson.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>
<div class="panel panel-default" id="container">
	<div class="panel-body">

<h1> All lectures: </h1>

	<?php 
		$lectures = $teacher->getLectures();
		foreach((array)$lectures as $value) {
	?>	
		<div class="panel panel-default">
			<div class="panel-body">
				<form role="class" method="POST" action="">
					<table class="table">
					<?php
						$args = explode(",",$value);
					?>
						<tr><td> Class: </td><td><?php echo $args[0];?></td></tr>
						<input type="hidden" name="comboClass" value="<?php echo $args[0];?>">

						<tr><td> Subject: </td><td><?php echo $args[1];?></td></tr>
						<input type="hidden" name="comboSubject" value="<?php echo $args[1];?>">

						<tr><td> Date: </td><td><?php echo $args[2];?></td></tr>
						<input type="hidden" name="lessontime" value="<?php echo $args[2];?>">

						<tr><td> Hour: </td><td><?php echo $args[3];?></td></tr>
						<input type="hidden" name="comboHour" value="<?php echo $args[3];?>">

						<tr><td> Topics: </td><td><?php echo $args[4];?></td></tr>
						<input type="hidden" name="topics" value="<?php echo $args[4];?>">
					</table>
					<?php
						$dateLesson = $args[2];
						if($dateLesson >= date("Y-m-d", strtotime('monday this week')) && $dateLesson <= date("Y-m-d", strtotime('sunday this week'))) { 
					?>
							<button type="submit" class="btn btn-primary" onClick="this.form.action='editRecordLesson.php'">Edit</button>
							<button type="submit" class="btn btn-danger" onClick="this.form.action='deleteRecordLesson.php'">Delete</button>
					<?php	
						} else { ?>
							<div class="alert alert-warning" role="alert"> 
								<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								 This lecture is not editable/erasable as it relates to past weeks...
							</div> <?php
						}
					?>
				</form>
			</div>
		</div>
	<?php
		}
	?>
				</div>

			</div>

<?php 
    require_once("defaultFooter.php")
?>

