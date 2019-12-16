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
?>

<script>

function modalDelete(obj) {
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectDelete").value = subject;
	
	var assignment = obj.getAttribute("data-assignment");
	document.getElementById("modalAssignmentDelete").value = assignment;
	
}

function modalEdit(obj) {
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectEdit").value = subject;
	
	var assignment = obj.getAttribute("data-assignment");
	document.getElementById("modalAssignmentEdit").value = assignment;
	
}
$(document).ready(function(){

	$("#comboClass").change(function() {
		document.getElementById('assignmentsTitle').style.display= 'none' ;
		document.getElementById('assignmentsTable').style.display= 'none' ;
	});
	
	$("#assignmentstime").change(function() {
		document.getElementById('assignmentsTitle').style.display= 'none' ;
		document.getElementById('assignmentsTable').style.display= 'none' ;
	});
	
});

</script>

<ul class="nav nav-tabs">
  <li role="presentation"><a href="recordAssignments.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">

	<h1 class="text-center"> All assignments </h1>

	<div class="form-group">
		<form class="navbar-form navbar form-inline" method="POST" action="viewAllAssignments.php">
		
			<table class="table table-hover">
						
				<tr><td><label> Class </label></td><td>
					<select class="form-control" id="comboClass" name="comboClass" style="width:100%" required> 
						<option value="" disabled selected>Select class...</option>

						<?php 
							$classes=$teacher->getClassesByTeacher();
							foreach($classes as $value) {
								echo "<option value=".$value.">".$value."</option>";
							}
						?>
					</select></td>
				</tr>
				
				<tr><td><label> Date </label></td><td>
						<input class="form-control" type="date" name="assignmentstime" id="assignmentstime"
							min="<?php echo $beginSemester;  ?>" max="<?php echo $endSemester; ?>"
							style="width:100%" required> </td>
				</tr>
			</table>
			<button type="submit" name="okAssignemnent" class="btn btn-success">OK</button>
		</form>
	</div>

	<?php 
	if(!isset($_POST["okAssignemnent"])) {
		if(!isset($_SESSION['okAssignemnent'])) {
			$error = 1;
		}
	} else { 
		$_SESSION['okAssignemnent']=$_POST['okAssignemnent'];
		$_SESSION["comboClass"] = $_POST["comboClass"];
		$_SESSION["assignmentstime"] = $_POST["assignmentstime"];

	}
	
	if(isset($_SESSION['okAssignemnent'])) {
		

		if(isset($_POST['yesDelete'])) {
			if(!isset($_POST["assignments"]) || !isset($_POST["assignmentstime"]) 
				|| !isset($_POST["comboClass"]) || !isset($_POST["comboSubject"])) {
		?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Oh no! Something went wrong...</p>
			</div>
		<?php
			} else {
				$db->deleteAssignments($_POST['assignmentstime'], $_POST['comboSubject'], $_POST['comboClass']);

				?>

				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<p class="alert-link"> Assignments successfully deleted!</p>
				</div>

			<?php	
			} 

		}
		
		if(isset($_POST['editButton'])) {
			if(!isset($_POST["assignments"]) || !isset($_POST["assignmentstime"]) 
				|| !isset($_POST["comboClass"]) || !isset($_POST["comboSubject"])) {
		?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Oh no! Something went wrong...</p>
			</div>
		<?php
			} else {
				
				$db->updateAssignments($_POST['assignmentstime'], $_POST['comboClass'], $_POST['comboSubject'], $_POST['assignments']);

				?>

				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<p class="alert-link"> Assignments successfully updated!</p>
				</div>

			<?php	
			} 

		}
		
		$selectedClass = $_SESSION["comboClass"];
		$selectedDate = $_SESSION["assignmentstime"];
		
		$assignments = $teacher->getAssignmentsByClassAndDate($selectedClass, $selectedDate); // assignments couple subject,assignment,pathFilename
		if(!empty($assignments)) {

	?>
	<br><h1 id="assignmentsTitle" class="text-center"> Assignments list <small>Class: <?php echo $selectedClass ?> - Date: <?php echo $selectedDate?></small></h1>
	<form method='POST' action='' class='form-group'>
		<table id="assignmentsTable" class="table table-hover">
			<thead>
			<tr class="active">
				<th class="text-center">Subject</th>
				<th class="text-center">Assignment</th>
				<th class="text-center">Edit/Delete</th>
			</tr>
			</thead>
	<?php 
		foreach((array)$assignments as $value) {
	?>		

			<tbody>
			<?php
				$args = explode(",",$value);
				$subject = $args[0];
				$textAssignment = $args[1];
				$pathFilename = $args[2];
			?>
				<tr class="text-center">
					<td><?php echo $subject;?></td>
					<td>
						<textarea readonly="readonly" style="border:none; background: none; outline: none;"
							rows="2"><?php echo $textAssignment;?>
						</textarea>
						<?php
							if(!empty($pathFilename)) {
								echo "<div><span class='glyphicon glyphicon-paperclip' aria-hidden='true'>&emsp;<a href='$pathFilename'>";
								$end = array_slice(explode('/', $pathFilename), -1)[0];
								echo $end . "</a></span></div>";
							}
						?>
					</td>
					<td>
						<?php 
							//if($selectedDate >= date("Y-m-d", strtotime('monday this week')) && $selectedDate <= date("Y-m-d", strtotime('sunday this week'))) { 
						?>
						<button type="button" class="btn btn-default btn-xs" style='width:20%'
							data-toggle="modal" data-target="#modalEdit"
							<?php echo "data-subject='$args[0]' data-assignment='$args[1]'"; ?>
							onclick="modalEdit(this)">Edit</button>
							
						<button type="button" class="btn btn-danger btn-xs" style='width:20%'
							data-toggle="modal" data-target="#modalDelete"
							<?php echo "data-subject='$args[0]' data-assignment='$args[1]'"; ?>
							onclick="modalDelete(this)">Delete</button>
						<?php 
							//}
						?>
					</td>
				</tr>
			</tbody>
	<?php
		}
	?>
		</table>
	</form>
	
	<?php
		} else {
			echo "<div class='alert alert-warning'>
					<h4 id='assignmentsTitle'>
					<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
					No assignments for this day <small> &emsp;Class: $selectedClass - Date: $selectedDate </small></h4></div>";
		}
	?>
	
<!-- Modal -->
<div id="modalDelete" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body form-group text-center"></br>
	  
	    <button type="button" class="close" data-dismiss="modal">&times;</button>
		<form method="POST" action="">
			<table class="table table-hover text-center">
				<tr><td><label> Class </label></td>
					<td><input type="text" name="comboClass" value="<?php echo $selectedClass ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input type="text" name="assignmentstime" value="<?php echo $selectedDate ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Subject </label></td>
					<td><input id="modalSubjectDelete" type="text" name="comboSubject" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Assignments </label></td>
					<td>
						<textarea id="modalAssignmentDelete" name="assignments" readonly="readonly" style="border:none; outline: none;"
							rows="4"></textarea>
					</td>
				</tr>
			</table>
	
			<h3><strong>Do you really want to delete this record?</strong> </h3>
			<button name="yesDelete" type="submit" class="btn btn-default btn-lg" onclick="this.form.action='viewAllAssignments.php'">Yes</button>
			<button type="submit" class="btn btn-default btn-lg" data-dismiss="modal">No</button>
		</form>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="modalEdit" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body form-group text-center"></br>
	  
	    <button type="button" class="close" data-dismiss="modal">&times;</button>
		<form method="POST" action="">
			<table class="table table-hover text-center">
					<tr><td><label> Class </label></td>
					<td><input type="text" name="comboClass" value="<?php echo $selectedClass ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input type="text" name="assignmentstime" value="<?php echo $selectedDate ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Subject </label></td>
					<td><input id="modalSubjectEdit" type="text" name="comboSubject" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Assignments </label></td>
					<td>
						<textarea id="modalAssignmentEdit" class="form-control" name="assignments" rows="4" style="width:60%"></textarea>
						<span id="helpBlock" class="help-block"><small>You can edit only the assignment description</small></span>
					</td>
				</tr>
			</table>
			<button name="editButton" type="submit" class="btn btn-success" style="width:20%" onclick="this.form.action='viewAllAssignments.php'">Edit</button>
			<button type="submit" class="btn btn-default" style="width:20%" data-dismiss="modal">Cancel</button>
		</form>
      </div>
    </div>

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