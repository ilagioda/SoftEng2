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
	require_once("functions.php");
	$teacher=new Teacher();
    $db = new dbTeacher();
	
	$days = getCurrentSemester();

	if($days) {
		$beginSemester = $days[0];
		$endSemester = $days[1];
	} // else --> summer holidays 
	
		if(isset($_POST['yesDelete'])) {
		if(!isset($_POST["assignments"]) || !isset($_POST["assignmentsDate"]) 
			 || !isset($_POST["comboSubject"]) || empty("assignments")) {
?>
		<div class="alert alert-danger alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong><span class="glyphicon glyphicon-send"></span> Oh no! Something went wrong...</strong>
		</div>
<?php
		} else {
			$db->deleteAssignments($_POST['assignmentsDate'], $_POST['comboSubject'], $selectedClass);
?>

		<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong><span class="glyphicon glyphicon-send"></span> Assignments successfully deleted!</strong>
		</div>

<?php	
		} 
	}
		
	if(isset($_POST['editButton'])) {
		if(!isset($_POST["assignments"]) || !isset($_POST["assignmentsDate"]) || !isset($_POST["comboSubject"])) {
?>
		<div class="alert alert-danger alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong><span class="glyphicon glyphicon-send"></span> Oh no! Something went wrong...</strong>
		</div>
<?php
		} else {		
			$db->updateAssignments($_POST['assignmentsDate'], $selectedClass, $_POST['comboSubject'], $_POST['assignments']);
?>

		<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong><span class="glyphicon glyphicon-send"></span> Assignments successfully updated!</strong>
		</div>

<?php	
		} 
	}
?>

<script>

function modalDelete(obj) {
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectDelete").value = subject;
	
	var assignment = obj.getAttribute("data-assignment");
	document.getElementById("modalAssignmentDelete").value = assignment;
	
	var date = obj.getAttribute("data-date");
	document.getElementById("modalDateDelete").value = date;
	
}

function modalEdit(obj) {
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectEdit").value = subject;
	
	var assignment = obj.getAttribute("data-assignment");
	document.getElementById("modalAssignmentEdit").value = assignment;
	
	var date = obj.getAttribute("data-date");
	document.getElementById("modalDateEdit").value = date;
	
}


$(document).ready(function(){
	
	$('a[data-toggle="tab"]').click(function(e) {
		var target = $(e.target).attr("href"); // activated tab
	});
	
	$('[id^= "filteredTable-"]').hide();
	$('[id^= "titleSelectDate-"]').hide();
	$('[id^= "divPreviousNext-"]').hide();

	$('[id^= "buttonCalendar-"]').click(function(){
		$(this).hide();
		var subject = $(this).attr("data-subject");
		$('#assignmentTitle-'+subject).hide();
		$('#boxInfoToday-'+subject).hide();
		$('#filteredTable-'+subject).show();
		$('#btnShowAll-'+subject).prop("type", "button");
		$('#assignmentsTable-'+subject).hide();
		$('#titleSelectDate-'+subject).show();
		$("#inputCalendar-"+subject).prop("type", "date");
		$("#divPreviousNext-"+subject).show();
	});
	
	$('[id^= "btnShowAll-"]').click(function(){
		var subject = $(this).attr("data-subject");
		$('#assignmentTitle-'+subject).show();
		$('#boxInfoToday-'+subject).show();
		$('#buttonCalendar-'+subject).show();
		$('#btnShowAll-'+subject).prop("type", "hidden");
		$("#inputCalendar-"+subject).prop("type", "hidden");
		$('#filteredTable-'+subject).hide();
		$('#assignmentsTable-'+subject).show();
		$('#titleSelectDate-'+subject).hide();
		$("#divPreviousNext-"+subject).hide();

	});
	
	$('[id^= "inputCalendar-"]').change(function() {

		var subject = $(this).attr("data-subject");
		var date = $(this).val();
		
		callAjaxLoadAssignments(date, subject);
	});
	
	$(".previous").click(function() {
		
		var subject = $(this).attr("data-subject");
		
		var actualDay = $('#inputCalendar-'+subject).val();
		actualDay = new Date(actualDay);
		
		var previousDay = actualDay.setDate(actualDay.getDate() - 1);
		previousDay = new Date(previousDay).toISOString().split('T')[0];
				
		callAjaxLoadAssignments(previousDay, subject);
	});
	
	$(".next").click(function() {
		
		var subject = $(this).attr("data-subject");
		
		var actualDay = $('#inputCalendar-'+subject).val();
		actualDay = new Date(actualDay);
		
		var nextDay = actualDay.setDate(actualDay.getDate() + 1);
		nextDay = new Date(nextDay).toISOString().split('T')[0];

		callAjaxLoadAssignments(nextDay, subject);
	});

});

var today = new Date().toISOString().split('T')[0];  

function callAjaxLoadAssignments(day, subject) {
	
	$('#inputCalendar-'+subject).val(day); // set the date in the input field 
		
	var flag = 1; // flag to incate if the assignment is editable/erasable: 0 if it is editable/erasable, 1 otherwise
	if(day >= today) {
		flag = 0;
	}
	
	$.ajax({
		type:		"POST",
		dataType:	"json",
		url:		"loadAssignments.php",
		data:		"date="+day,
		cache:		false,
		success:	function(response){ // RESPONSE = subject,assignment,pathfilename
						$('#filteredTable-'+subject).empty();
						if(response) {
							$('#filteredTable-'+subject).append(updateTableAssignments(response, flag, subject, day));
						} else {
							
							$('#filteredTable-'+subject).append("<div class='alert alert-warning'><h4 id='assignmentsTitle'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;No assignments for the selected date </h4></div>");
					
						}
					},
		error: 		function(){
						alert("Error: assignments not loaded");
					}
	});	
}


function updateTableAssignments(response, flag, subject, date) {

	var output = "<thead><tr class='active'><th class='text-center col-xs-6 col-md-4'>Date</th><th class='text-center col-xs-6 col-md-4'>Assignment</th><th class='text-center col-xs-6 col-md-4'></th></tr></thead><tbody>";
	var flag2 = 0; 
	
	for(var i=0; i<Object.keys(response).length; i++) { // foreach daily assignment
		var ass = "";
		res = response[i].split(",");
		for(var j=1; j<(res.length-1); j++) {
			ass += res[j]+",";
		}
		ass = ass.substr(0,ass.length-1);
		
		if(subject === res[0]) {

			output += "<tr class='text-center'><td>"+date+"</td><td>"+
						"<textarea readonly='readonly' style='border:none; background: none; outline: none;' rows='2'>"+
						ass+"</textarea>";
										
			if(res[(res.length-1)]) {
				// if pathfilename exists and it is not null or empty 
				output += "<div><span class='glyphicon glyphicon-paperclip' aria-hidden='true'>&emsp;<a href="+res[(res.length-1)]+">";
				var path = res[(res.length-1)].split('/');
				var end = path.slice(-1);
				output += end+"</a></span></div>";
			}
			
			output += "</td><td>";

			if(flag === 0) {
				// assignments editable/erasable
				output += "<button type='button' class='btn btn-default btn-xs' style='width:20%'";
				output += "data-toggle='modal' data-target='#modalEdit'";
				output += "data-subject='"+res[0]+"' data-date='"+date+"' data-assignment='"+ass+"' onclick='modalEdit(this)'>Edit</button>&emsp;";
				output += "<button type='button' class='btn btn-danger btn-xs' style='width:20%'";
				output += "data-toggle='modal' data-target='#modalDelete'";
				output += "data-subject='"+res[0]+"' data-date='"+date+"' data-assignment='"+ass+"' onclick='modalDelete(this)'>Delete</button>";
			} else {
				output += "<span class='glyphicon glyphicon-ban-circle' aria-hidden='true' title='Not editable/erasable as it relates to past weeks...'></span>";
			}

			output += "</td></tr>";
			
			flag2 = 1;
		}
	}
	if(flag2 != 1) {
		output = "<div class='alert alert-warning'><h4><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;No assignments for the selected date </h4></div>";
	} else {
		output += "</tbody>";
	}
	return output;
}

</script>

<ul class="nav nav-tabs">
  <li role="presentation"><a href="recordAssignments.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">

<?php 
	
	$subjects = $teacher->getSubjectByClassAndTeacher($_SESSION['comboClass']);
	if(count($subjects) > 0) { 
		navSubjects($subjects);			

		echo "<div id='myTabContent' class='tab-content'>";
		foreach($subjects as $subject) {
			if($subject == $subjects[0]) {
				echo "<div class='tab-pane fade active in' id='$subject'>";
			} else {
				echo "<div class='tab-pane fade' id='$subject'>";
			}
			$assignments = $db->getAssignmentsByClassAndSubject($_SESSION["user"], $selectedClass, $subject, $beginSemester, $endSemester); // date,textAssignment,pathFilename
			if(!empty($assignments)) {	
			
			$now = new DateTime('now');
			$dailyAssignments = $db->getAssignmentsByClassAndDate($_SESSION["user"], $selectedClass, $now->format("Y-m-d")); // subject, textAssignment, pathFilename
			$flag = 0; 

			if(!empty($dailyAssignments)) {

				foreach($dailyAssignments as $dailyAssignment) {
					$args_a = explode(",", $dailyAssignment);
					$text = "";
					$path = $args_a[(count($args_a)-1)];
					for($j=1; $j<(count($args_a)-1); $j++) {
						// the text can contain the character ,
						$text .= $args_a[$j].",";
					}
					$text = substr($text, 0, -1);
					if($subject == $args_a[0]) {
						echo "<div class='panel panel-info text-center' id='boxInfoToday-$subject'><div class='panel-heading'> <strong>For today... </strong></div>";
						// print textAssignment and file if it exists
						echo "<div class='panel-body'><textarea readonly='readonly' style='border:none; background: none; outline: none;' rows='2'>$text</textarea>";
						if(!empty($path)) {
							echo "<div><span class='glyphicon glyphicon-paperclip' aria-hidden='true'>&emsp;<a href='$path'>";
								$end = array_slice(explode('/', $path), -1)[0];
							echo $end . "</a></span></div>";
						}
						echo "</div></div>";
						$flag = 1;
					}
				}
				if($flag == 0) {
					echo "<div class='panel panel-warning text-center' id='boxInfoToday-$subject'>
					<div class='panel-heading '><strong> For today... </strong></div>";
					echo "<div class='panel-body'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;There are no assignments.</div></div>";
				}
			} else {
				echo "<div class='panel panel-warning text-center' id='boxInfoToday-$subject'>
				<div class='panel-heading '><strong> For today... </strong></div>";
				echo "<div class='panel-body'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;There are no assignments.</div></div>";
			}
			//echo "</div>";
?>		
	
		<h2 id="assignmentTitle-<?php echo $subject; ?>">All assignments 
			<button class="btn btn-default pull-right" id="buttonCalendar-<?php echo $subject; ?>" data-subject="<?php echo $subject; ?>"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Select a date</button>
		</h2>
		<p class='text-center' id="titleSelectDate-<?php echo $subject; ?>"><strong>Select a date</strong></p>

		<input class="form-control" name="inputCalendar" id="inputCalendar-<?php echo $subject; ?>" data-subject="<?php echo $subject; ?>"
				type="hidden" min="<?php echo $beginSemester; ?>" max="<?php echo $endSemester ?>">			
				
		<div id="divPreviousNext-<?php echo $subject; ?>">
			<ul class='pager'>
				<li class='previous' data-subject="<?php echo $subject; ?>"><a href='#'><span aria-hidden='true'>&larr;</span> Older</a></li>
				<li class='next' data-subject="<?php echo $subject; ?>"><a href='#'>Newer <span aria-hidden='true'>&rarr;</span></a></li>
			</ul>
		</div>
				<form method='POST' action='' class='form-group'>
					<table id="filteredTable-<?php echo $subject; ?>" class="table table-hover"></table>
					<table id="assignmentsTable-<?php echo $subject; ?>" class="table table-hover">
						<thead>
						<tr class="active">
							<th class="text-center col-xs-6 col-md-4">Date </th>
							<th class="text-center col-xs-6 col-md-4">Assignments</th>
							<th class="col-xs-6 col-md-4"></th>
						</tr>
						</thead>
					
						<tbody>

<?php 
						foreach((array)$assignments as $value) {

							$args = explode(",",$value);
							
							$date = $args[0];
							$textAssignment = "";
							for($j=1; $j<(count($args)-1); $j++) {
								// the text of the assignment can contain the character ,
								$textAssignment .= $args[$j].",";
							}
							$textAssignment = substr($textAssignment, 0, -1); // remove the last ,

							// the pathFilename is the last value of the array
							$pathFilename = $args[(count($args)-1)];
?>
				
							<tr class="text-center">
								<td><?php echo $date;?> </td>
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
										if($date >= date("Y-m-d")) {  // if the date is in the future, then the assignments can be editable/erasable
									?>
									<button type="button" class="btn btn-default btn-xs" style='width:20%'
										data-toggle="modal" data-target="#modalEdit"
										<?php echo "data-subject='$subject' data-assignment='$textAssignment' data-date='$date'";?>
										onclick="modalEdit(this)">
										Edit
									</button>
										
									<button type="button" class="btn btn-danger btn-xs" style='width:20%'
										data-toggle="modal" data-target="#modalDelete"
										<?php echo "data-subject='$subject' data-assignment='$textAssignment' data-date='$date'"; ?>
										onclick="modalDelete(this)">
										Delete
									</button>
									<?php 
										} else {
											echo "<span class='glyphicon glyphicon-ban-circle' aria-hidden='true' title='Not editable/erasable as it relates to past weeks...'></span>";
										}
									?>
								</td>
							</tr>
					<?php
						}
					?>
						</tbody>
					</table>
					<div class="text-center"><input type="hidden" class="btn btn-primary" value="Show all assignments" id="btnShowAll-<?php echo $subject; ?>" data-subject="<?php echo $subject; ?>"></div>
				</form>
<?php
			} else {
				echo "<div class='alert alert-warning'>
						<h4 id='assignmentsTitle'>
						<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;
						No assignments for this subject </h4></div>";
			}
			
			echo "</div>";
		}
		echo "</div>";
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
					<td><input type="text" name="assignmentsDate" id="modalDateDelete" readonly="readonly" style="border:none; outline: none;"></td>
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
					<td><input type="text" name="assignmentsDate" id="modalDateEdit" readonly="readonly" style="border:none; outline: none;"></td>
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