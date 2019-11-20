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
  <li role="presentation"><a href="recordAssignments.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">

	<h1> All assignments: </h1>
	<form class="form-inline active-cyan-3 active-cyan-4">
		<input class="form-control form-control-sm ml-3 w-75" style="width:100%" type="text" placeholder="Search" aria-label="Search">
	</form>
	
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
			</div>
		</div>
<?php 
    require_once("defaultFooter.php")
?>

