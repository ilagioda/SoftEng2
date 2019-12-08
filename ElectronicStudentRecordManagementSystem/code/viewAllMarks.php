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
$(document).ready(function(){
	$("#comboClass").change(function() {
		

		var comboClass = $("option:selected", this).val();

		$.ajax({
			type:		"POST",
			dataType:	"text",
			url:		"selectSubjects.php",
			data:		"comboClass="+comboClass,
			cache:		false,
			success:	function(response){
							$('#comboSubject').html(response);
						},
			error: 		function(){
							alert("Error: subjects not loaded");
						}
		});
	});
	$("#comboClass").change(function() {
			document.getElementById('titleStudent').style.display= 'none' ;
			document.getElementById('studentTable').style.display= 'none' ;
	});
	
	$(function() {
		$('tr.parent td span.btn').on("click", function(){
			var idOfParent = $(this).parents('tr').attr('id');
			$('tr.child-'+idOfParent).toggle();	
			$(this).text($(this).text() == 'View marks' ? 'Hide marks' : 'View marks');		
		});
		$('tr[class^=child-]').hide().children('td');

	});
	
});

function modalDelete(obj) {
	
	var studentInfo = obj.getAttribute("data-student");
	document.getElementById("modalStudentDelete").value = studentInfo;
	
	var date = obj.getAttribute("data-date");
	document.getElementById("modalDateDelete").value = date;
	
	var mark = obj.getAttribute("data-mark");
	document.getElementById("modalMark").value = mark;
	
	var hour = obj.getAttribute("data-hour");
	document.getElementById("modalHourDelete").value = hour;
	
}

function modalEdit(obj) {
	
	var studentInfo = obj.getAttribute("data-student");
	document.getElementById("modalStudentEdit").value = studentInfo;
	
	var date = obj.getAttribute("data-date");
	document.getElementById("modalDateEdit").value = date;
	
	var mark = obj.getAttribute("data-mark");
	document.getElementById("modalSelectedGrade").innerHTML = mark;
	
	var hour = obj.getAttribute("data-hour");
	document.getElementById("modalHourEdit").value = hour;
	
}

</script>

<style> 
	#container {
		box-shadow: 0px 2px 25px rgba(0, 0, 0, .25);
		padding:0 15px 0 15px;
	}
</style>
<ul class="nav nav-tabs">
  <li role="presentation"><a href="submitMarks.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>


<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1> View all marks </h1>
		<div class="form-group">
		<form class="navbar-form navbar form-inline" role="class" method="POST" action="viewAllMarks.php">
		
			<table class="table">
						
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
				
				<tr><td><label>Subject </label></td><td>
					<select class="form-control" id="comboSubject" name="comboSubject" style="width:100%" required>
						<option value="" disabled selected>Select subject...</option>
					</select></td>
				</tr>	
				<tr><td><button type="submit" name="ok" class="btn btn-success">OK</button></td></tr>
			</table>
		</form>
	</div>

	<?php 
	if(!isset($_POST["ok"])) {
		if(!isset($_SESSION['ok'])) {
			$error = 1;
		}
	} else { 
		$_SESSION['ok']=$_POST['ok'];
		$_SESSION["comboClass"] = $_POST["comboClass"];
		$_SESSION["comboSubject"] = $_POST["comboSubject"];

	}
	
	if(isset($_SESSION['ok'])) {
		
		

		if(isset($_POST['yesDelete'])) {
			if(!isset($_POST["comboStudent"]) || !isset($_POST["lessontime"]) 
				|| !isset($_POST["comboHour"]) || !isset($_POST["comboSubject"])) {
		?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Oh no! Something went wrong...</p>
			</div>
		<?php
			} else {
				$ssn = explode(" ", $_POST['comboStudent']);
				$result = $db->deleteMark($ssn[2], $_POST['lessontime'], $_POST['comboHour'], $_POST['comboSubject']);
				?>

				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<p class="alert-link"> Mark successfully deleted!</p>
				</div>

			<?php	
			} 

		}
		
		if(isset($_POST['editButton'])) {
			if(!isset($_POST["comboStudent"]) || !isset($_POST["lessontime"]) 
				|| !isset($_POST["comboHour"]) || !isset($_POST["comboSubject"]) || !isset($_POST["comboGrade"])) {
		?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Oh no! Something went wrong...</p>
			</div>
		<?php
			} else {
				
				$ssn = explode(" ", $_POST['comboStudent']);
				$result = $db->updateMark($ssn[2], $_POST['comboSubject'], $_POST['lessontime'], $_POST['comboHour'], $_POST['comboGrade']);
				?>

				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<p class="alert-link"> Mark successfully updated!</p>
				</div>

			<?php	
			} 

		}
		
			$i = 0; // for a student student
			$j = 0; // for a mark and date of a specific student 
			// show students list
			echo "<form method='POST' action='' class='form-group'>";
			$selectedClass = $_SESSION["comboClass"];
			$selectedSubject = $_SESSION["comboSubject"];

	?>
			
			<h1 id="titleStudent"> Students list  <small>Class: <?php echo $selectedClass ?> - Subject: <?php echo $selectedSubject?></small></h1>

			<table class="table table-condensed" id="studentTable" style="border-collapse:collapse;">

				<tbody>
	<?php

			$students = $teacher->getStudents2($selectedClass);
			foreach($students as $student) {
				$args = explode(",",$student);

				// VISIBLE ROWS
	?> 			<tr class="parent active" id="<?php echo $args[2] ?>">

					<td>
					<?php 
						$studentInfo[$i] = "$args[0] $args[1] $args[2]";
						echo $studentInfo[$i];
					?> 
					</td>
					<td></td>
					<td><span class="btn btn-primary pull-right" id="viewMarks">View marks</span></td>
					
				</tr>
			
			<?php 
				$marks = $teacher->viewStudentMarks($args[2], $selectedSubject);
				if ($marks) {
			?>
				<tr class="child-<?php echo $args[2] ?>">
					<td class="text-center"> <strong> Date </strong>  </td>
					<td><strong> Specific marks </strong>  </td>
					<td></td>
				</tr>
				<?php
					foreach($marks as $mark){
						
						$argm = explode(",",$mark);
						$date[$j] = $argm[0];
						$specificMark[$j] = $argm[1];
						$hour[$j] = $argm[2];

				?>
				<tr class="child-<?php echo $args[2] ?>">
					<td class="text-center"> <?php echo $date[$j] ?> </td>
					<td><?php echo $specificMark[$j] ?> </td>
					<td>
						<?php 
							if($date[$j] >= date("Y-m-d", strtotime('monday this week')) && $date[$j] <= date("Y-m-d", strtotime('sunday this week'))) { 
						?>
						<button type="button" class="btn btn-default btn-xs" 
							data-toggle="modal" data-target="#modalEdit"
							<?php echo "data-student = '$studentInfo[$i]' data-date='$date[$j]' data-hour='$hour[$j]' data-mark='$specificMark[$j]'"; ?>
							onclick="modalEdit(this)">Edit</button>
							
						<button type="button" class="btn btn-danger btn-xs" 
							data-toggle="modal" data-target="#modalDelete"
							<?php echo "data-student = '$studentInfo[$i]' data-date='$date[$j]' data-hour='$hour[$j]' data-mark='$specificMark[$j]'"; ?>
							onclick="modalDelete(this)">Delete</button>
						<?php 
							}
						?>
					</td>
				</tr>
			<?php
						$j++;
					}
				} else { ?>
				<tr class="child-<?php echo $args[2] ?>">
						<td></td><td>No marks </td>
					</tr>
				<?php
				}
				$i++;
			}
			?>
			</tbody>
			</table>
			</form>
			<!-- Trigger the modal with a button -->
			
			
<!-- Modal -->
<div id="modalDelete" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body form-group text-center"></br>
	  
	    <button type="button" class="close" data-dismiss="modal">&times;</button>
		<form method="POST" action="">
			<table class="table text-center">
				<tr><td><label> Class </label></td>
					<td><input type="text" name="comboClass" value="<?php echo $selectedClass ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Subject </label></td>
					<td><input type="text" name="comboSubject" value="<?php echo $selectedSubject ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Student </label></td>
					<td><input id="modalStudentDelete" type="text" name="comboStudent" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input id="modalDateDelete" type="text" name="lessontime" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Hour </label></td>
					<td><input id="modalHourDelete" type="text" name="comboHour" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Grade </label></td>
					<td><input id="modalMark" type="text" name="comboGrade" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
			</table>
	
			<h3><strong>Do you really want to delete this record?</strong> </h3>
			<button name="yesDelete" type="submit" class="btn btn-default btn-lg" onclick="this.form.action='viewAllMarks.php'">Yes</button>
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
			<table class="table text-center">
				<tr><td><label> Class </label></td>
					<td><input type="text" name="comboClass" value="<?php echo $selectedClass ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Subject </label></td>
					<td><input type="text" name="comboSubject" value="<?php echo $selectedSubject ?>" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Student </label></td>
					<td><input id="modalStudentEdit" type="text" name="comboStudent" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input id="modalDateEdit" type="text" name="lessontime" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Hour </label></td>
					<td><input id="modalHourEdit" type="text" name="comboHour" readonly="readonly" style="border:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Grade </label></td>
					<td>
						<select class="form-control" name="comboGrade">
						
							<option id="modalSelectedGrade" selected></option> 
							<?php
								for($i=0; $i<=10; $i++) {
									$j=$i+1;
									echo "<option value=" . $i . ">" . $i . "</option>";
									if($i!=10){
										echo "<option value=" . $i . "+>" . $i . "+</option>";
										echo "<option value=" . $i . ".5>" . $i . ".5</option>";
										echo "<option value=" . $j . "->" . $j . "-</option>";
									} 
								}
							?>	
						</select>
					</td>
				</tr>
			</table>
			<button name="editButton" type="submit" class="btn btn-success" style="width:20%" onclick="this.form.action='viewAllMarks.php'">Edit</button>
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