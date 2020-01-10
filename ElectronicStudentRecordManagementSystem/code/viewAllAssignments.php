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
	
	if (!isset($_SESSION['comboClass'])) {
        header("Location: chooseClass.php");
        exit;
    }
	$selectedClass = $_SESSION['comboClass'];
    require_once "loggedTeacherNavbar.php";
}
	require_once("classTeacher.php");
	$teacher=new Teacher();
    $db = new dbTeacher();
	
		// get the current semester:
	$now = new DateTime('now');
	// $month = $now->format('m');
	$year = $now->format('Y');
	$one_year = new DateInterval('P1Y');
	$next_year = (new DateTime())->add(new DateInterval('P1Y'));

	if(strtotime($now->format("Y-m-d")) >= strtotime($now->format('Y-09-01')) 
		&& strtotime($now->format("Y-m-d")) <= strtotime($next_year->format('Y-01-31'))) {
			// TODAY IS WITHIN THE FIRST SEMESTER
		$beginSemester = ($now->format("Y-m-d"));
		$endSemester = ($next_year->format('Y-01-31'));
	} elseif(strtotime($now->format("Y-m-d")) >= strtotime($now->format('Y-02-01')) 
		&& strtotime($now->format("Y-m-d")) <= strtotime($now->format('Y-06-30'))) {
			// TODAY IS WITHIN THE SECOND SEMESTER
		$beginSemester = ($now->format("Y-m-d"));
		$endSemester = ($now->format('Y-06-30'));
	} else {
		// summer holidays
	}
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
	
	$("#assignmentsDate").change(function() {
		var date = $("#assignmentsDate").val();

		var today = new Date().toISOString().split('T')[0];  
		
		var flag = 1; // flag to incate if the assignment is editable/erasable: 0 if it is editable/erasable, 1 otherwise
		if(date >= today) {
			flag = 0;
		}
		
		$.ajax({
			type:		"POST",
			dataType:	"json",
			url:		"loadAssignments.php",
			data:		"date="+date,
			cache:		false,
			success:	function(response){ // RESPONSE = subject,assignment,pathfilename
							$('#assignmentsTable').empty();
							if(response) {
								$('#assignmentsTable').append(updateTableAssignments(response, flag));
							} else {
								
								$('#assignmentsTable').append("<div class='alert alert-warning'><h4 id='assignmentsTitle'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;No assignments for this day </h4></div>");
						
							}
						},
			error: 		function(){
							alert("Error: assignments not loaded");
						}
		});
	});
	
	$(".previous").click(function() {
		
		var actualDay = document.getElementById("assignmentsDate").value;
		actualDay = new Date(actualDay);
		var previousDay = actualDay.setDate(actualDay.getDate() - 1);
		previousDay = new Date(previousDay);
		$('#assignmentsDate').val(previousDay.toISOString().split('T')[0]);
		
		var date = $("#assignmentsDate").val();

		var today = new Date().toISOString().split('T')[0];  
		
		var flag = 1; // flag to incate if the assignment is editable/erasable: 0 if it is editable/erasable, 1 otherwise
		if(date >= today) {
			flag = 0;
		}
		
		$.ajax({
			type:		"POST",
			dataType:	"json",
			url:		"loadAssignments.php",
			data:		"date="+date,
			cache:		false,
			success:	function(response){ // RESPONSE = subject,assignment,pathfilename
							$('#assignmentsTable').empty();
							if(response) {
								$('#assignmentsTable').append(updateTableAssignments(response, flag));
							} else {
								
								$('#assignmentsTable').append("<div class='alert alert-warning'><h4 id='assignmentsTitle'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;No assignments for this day </h4></div>");
						
							}
						},
			error: 		function(){
							alert("Error: assignments not loaded");
						}
		});
	});
	
		$(".next").click(function() {
		
		var actualDay = document.getElementById("assignmentsDate").value;
		actualDay = new Date(actualDay);
		var nextDay = actualDay.setDate(actualDay.getDate() +1);
		nextDay = new Date(nextDay);
		$('#assignmentsDate').val(nextDay.toISOString().split('T')[0]);
		
		var date = $("#assignmentsDate").val();

		var today = new Date().toISOString().split('T')[0];  
		
		var flag = 1; // flag to incate if the assignment is editable/erasable: 0 if it is editable/erasable, 1 otherwise
		if(date >= today) {
			flag = 0;
		}
		
		$.ajax({
			type:		"POST",
			dataType:	"json",
			url:		"loadAssignments.php",
			data:		"date="+date,
			cache:		false,
			success:	function(response){ // RESPONSE = subject,assignment,pathfilename
							$('#assignmentsTable').empty();
							if(response) {
								$('#assignmentsTable').append(updateTableAssignments(response, flag));
							} else {
								
								$('#assignmentsTable').append("<div class='alert alert-warning'><h4 id='assignmentsTitle'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;No assignments for this day </h4></div>");
						
							}
						},
			error: 		function(){
							alert("Error: assignments not loaded");
						}
		});
	});

	
});

function updateTableAssignments(response, flag) {

	var output = "<thead><tr class='active'><th class='text-center'>Subject</th><th class='text-center'>Assignment</th><th class='text-center'>Edit/Delete</th></tr></thead><tbody>";
	
	for(var i=0; i<Object.keys(response).length; i++) {
		res = response[i].split(",");
	
		output += "<tr class='text-center'><td>"+res[0]+"</td><td>"+
					"<textarea readonly='readonly' style='border:none; background: none; outline: none;' rows='2'>"+
					res[1]+"</textarea>";
									
		if(res[2]) {
			// if pathfilename exists and it is not null or empty 
			output += "<div><span class='glyphicon glyphicon-paperclip' aria-hidden='true'>&emsp;<a href="+res[2]+">";
			var path = res[2].split('/');
			var end = path.slice(-1);
			output += end+"</a></span></div>";
		}
		
		output += "</td><td>";

		if(flag === 0) {
			// assignments editable/erasable
			output += "<button type='button' class='btn btn-default btn-xs' style='width:20%'";
			output += "data-toggle='modal' data-target='#modalEdit'";
			output += "data-subject='"+res[0]+"' data-assignment='"+res[1]+"' onclick='modalEdit(this)'>Edit</button>&emsp;";
			output += "<button type='button' class='btn btn-danger btn-xs' style='width:20%'";
			output += "data-toggle='modal' data-target='#modalDelete'";
			output += "data-subject='"+res[0]+"' data-assignment='"+res[1]+"' onclick='modalDelete(this)'>Delete</button>";
		} else {
			output += "<span class='glyphicon glyphicon-ban-circle' aria-hidden='true' title='Not editable/erasable as it relates to past weeks...'></span>";
		}

		output += "</td></tr>";
	}

	output += "</tbody>";
	return output;
}

</script>

<ul class="nav nav-tabs">
  <li role="presentation"><a href="recordAssignments.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">

	<h1>All assignments </h1>

	<?php 

		if(isset($_POST['yesDelete'])) {
			if(!isset($_POST["assignments"]) || !isset($_POST["assignmentsDate"]) 
			 || !isset($_POST["comboSubject"])) {
		?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Oh no! Something went wrong...</p>
			</div>
		<?php
			} else {
				$db->deleteAssignments($_POST['assignmentsDate'], $_POST['comboSubject'], $selectedClass);

				?>

				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<p class="alert-link"> Assignments successfully deleted!</p>
				</div>

			<?php	
			} 

		}
		
		if(isset($_POST['editButton'])) {
			if(!isset($_POST["assignments"]) || !isset($_POST["assignmentsDate"]) || !isset($_POST["comboSubject"])) {
		?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Oh no! Something went wrong...</p>
			</div>
		<?php
			} else {
				
				$db->updateAssignments($_POST['assignmentsDate'], $selectedClass, $_POST['comboSubject'], $_POST['assignments']);

				?>

				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<p class="alert-link"> Assignments successfully updated!</p>
				</div>

			<?php	
			} 

		}
		
			if(!isset($_SESSION["assignmentsDate"])) {
				$_SESSION["assignmentsDate"] = $now->format("Y-m-d");
			}
			$selectedDate = $_SESSION["assignmentsDate"];
			
			/*$subjects = $teacher->getSubjectByClassAndTeacher($_SESSION['comboClass']);
			if(count($subjects) > 1) { 
				echo "<ul class='nav nav-pills' style='justify-content: center; display: flex;'>";
				
				foreach($subjects as $subject) {
					echo "<li><a href='#$subject' data-toggle='tab'>$subject</a></li>";
				}
				echo "</ul>";
						
			}*/
			
		?>
		

		
		<input class="form-control assignmentsDate" name="assignmentsDate" id="assignmentsDate" type="date" min="<?php echo $beginSemester; ?>" max="<?php echo $endSemester ?>" value="<?php echo $selectedDate; ?>">

		<ul class="pager">
			<li class="previous"><a href="#"><span aria-hidden="true">&larr;</span> Older</a></li>
			<li class="next"><a href="#">Newer <span aria-hidden="true">&rarr;</span></a></li>
		</ul>
	
		<?php
		$assignments = $teacher->getAssignmentsByClassAndDate($selectedClass, $selectedDate); // assignments subject,assignment,pathFilename
		if(!empty($assignments)) {

	?>
	<form method='POST' action='' class='form-group'>
		<table id="assignmentsTable" class="table table-hover">
			<thead>
			<tr class="active">
				<th class="text-center">Subject</th>
				<th class="text-center">Assignment</th>
				<th class="text-center">Edit/Delete</th>
			</tr>
			</thead>
			<tbody>

	<?php 
		foreach((array)$assignments as $value) {
	?>		

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
							if($selectedDate >= date("Y-m-d")) {  // if the date is in the future, then the assignments can be editable/erasable
						?>
						<button type="button" class="btn btn-default btn-xs" style='width:20%'
							data-toggle="modal" data-target="#modalEdit"
							<?php echo "data-subject='$args[0]' data-assignment='$args[1]'"; ?>
							onclick="modalEdit(this)">Edit</button>
							&emsp;
						<button type="button" class="btn btn-danger btn-xs" style='width:20%'
							data-toggle="modal" data-target="#modalDelete"
							<?php echo "data-subject='$args[0]' data-assignment='$args[1]'"; ?>
							onclick="modalDelete(this)">Delete</button>
						<?php 
							} else {
								echo "<span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span>";
							}
						?>
					</td>
				</tr>
	<?php
		}
	?>
		</tbody>
		</table>
	</form>
	
	<?php
		} else {
			echo "<div class='alert alert-warning'>
					<h4 id='assignmentsTitle'>
					<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;
					No assignments for this day </h4></div>";
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
					<td><input type="text" name="comboClass" value="<?php echo $selectedClass; ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input type="text" name="assignmentsDate" value="<?php echo $selectedDate; ?>" readonly="readonly" style="border:none; outline: none;"></td>
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
					<td><input type="text" name="comboClass" value="<?php echo $selectedClass; ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input type="text" name="assignmentsDate" value="<?php echo $selectedDate; ?>" readonly="readonly" style="border:none; outline: none;"></td>
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
	//}
	?>
</div>
</div>

<?php 
    require_once("defaultFooter.php")
?>