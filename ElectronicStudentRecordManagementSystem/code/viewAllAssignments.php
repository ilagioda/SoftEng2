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
    
?>
<ul class="nav nav-tabs">
  <li role="presentation"><a href="recordAssignments.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>

	<?php 
		$assignments = $teacher->getAssignments();
		foreach((array)$assignments as $value) {
	?>	
		<div class="panel panel-default">
			<div class="panel-body">
				<form role="class" method="POST" action="">
					<table class="table" name="lesson">
					<?php
						$args = explode(",",$value);
					?>
						<tr><td> Class: </td><td><?php echo $args[0];?>
						<input type="hidden" name="comboClass" value="<?php echo $args[0];?>">

						<tr><td> Subject: </td><td><?php echo $args[1];?></td></tr>
						<input type="hidden" name="comboSubject" value="<?php echo $args[1];?>">

						<tr><td> Date: </td><td><?php echo $args[2];?></td></tr>
						<input type="hidden" name="assignmentstime" value="<?php echo $args[2];?>">

						<tr><td> Assignments: </td><td><?php echo $args[3];?></td></tr>
						<input type="hidden" name="assignments" value="<?php echo $args[3];?>">
					</table>
					<?php
						$dateAssignments = $args[2];
						if($dateAssignments >= date("Y-m-d")) { 
					?>
					<button type="submit" class="btn btn-primary" onClick="this.form.action='editAssignments.php'">Edit</button>
					<button type="submit" class="btn btn-danger" onClick="this.form.action='deleteAssignments.php'">Delete</button>
					<?php	
						} else { ?>
							<div class="alert alert-warning" role="alert"> 
								<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								 These assignments are not editable/erasable as they relate to past weeks...
							</div> <?php
						}
					?>
				</form>
			</div>
		</div>
	<?php
		}
	?>
<?php 
    require_once("defaultFooter.php")
?>

