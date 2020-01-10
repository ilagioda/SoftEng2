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
    require_once "loggedTeacherNavbar.php";
}	
	require_once("classTeacher.php");
	$teacher=new Teacher();
	$db = new dbTeacher();
	
	$selectedClass = $_SESSION["comboClass"];

		if(isset($_POST['yesDelete'])) {
		if(!isset($_POST["comboStudent"]) || !isset($_POST["lessontime"]) 
			|| !isset($_POST["comboHour"]) || !isset($_POST["comboSubject"])) {
?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong><span class="glyphicon glyphicon-send"></span> Oh no! Something went wrong...</strong>
			</div>
		<?php
			} else {
				$ssn = explode(" ", $_POST['comboStudent']);
				$result = $db->deleteMark($ssn[2], $_POST['lessontime'], $_POST['comboHour'], $_POST['comboSubject']);
				?>

				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<strong><span class="glyphicon glyphicon-send"></span> Mark successfully deleted!</strong>
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
				<strong><span class="glyphicon glyphicon-send"></span> Oh no! Something went wrong...</strong>
			</div>
		<?php
			} else {
				
				$ssn = explode(" ", $_POST['comboStudent']);
				$result = $db->updateMark($ssn[2], $_POST['comboSubject'], $_POST['lessontime'], $_POST['comboHour'], $_POST['comboGrade']);
				?>

				<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<strong><span class="glyphicon glyphicon-send"></span> Mark successfully updated!</strong>
				</div>

			<?php	
			} 

		}
				
	
?>

<script>
$(document).ready(function(){	
	$(function() {
		$('tr.parent td span.btn').on("click", function(){
			var idOfParent = $(this).parents('tr').attr('id');
			$('tr.child-'+idOfParent).toggle();	
			$(this).text($(this).text() == 'View marks' ? 'Hide marks' : 'View marks');		
		});
		$('tr[class^=child-]').hide().children('td');

	});
	
$('a[data-toggle="tab"]').click(function(e) {
	var target = $(e.target).attr("href"); // activated tab
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
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectDelete").value = subject;
	
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
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectEdit").value = subject;	
}

</script>

<ul class="nav nav-tabs">
  <li role="presentation"><a href="submitMarks.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>


<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1> View all marks </h1>
	
<?php
	$subjects = $teacher->getSubjectByClassAndTeacher($_SESSION['comboClass']);
	if(count($subjects) > 0) { 
		echo "<ul id='myTab' class='nav nav-pills' style='justify-content: center; display: flex;'>";
		echo "<li class='text-center active' style='width:20%;'><a href='#$subjects[0]' data-toggle='tab'>$subjects[0]</a></li>";
		foreach($subjects as $subject) {
			if($subject != $subjects[0])
				echo "<li class='text-center' style='width:20%;'><a href='#$subject' data-toggle='tab'>$subject</a></li>";
		}
		echo "</ul>";				
		
		echo "<div id='myTabContent' class='tab-content'>";
	
		foreach($subjects as $subject) {
			if($subject == $subjects[0]) {
				echo "<div class='tab-pane fade active in' id='$subject'>";
			} else {
				echo "<div class='tab-pane fade' id='$subject'>";
			}

?>
			<form method='POST' action='' class='form-group'>
			<h2> Students list </h2>
		
			<table class="table table-condensed" id="studentTable" style="border-collapse:collapse;">
				<tbody>
	<?php
			$i = 0; // for a student student
			$j = 0; // for a mark and date of a specific student 
			$students = $teacher->getStudents2($selectedClass);
			foreach($students as $student) {
				$args = explode(",",$student);

				// VISIBLE ROWS
	?> 			<tr class="parent active" id="<?php echo $args[2]."-".$subject; ?>">

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
				$marks = $teacher->viewStudentMarks($args[2], $subject);
				if ($marks) {
			?>
				<tr class="child-<?php echo $args[2]."-".$subject; ?>">
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
				<tr class="child-<?php echo $args[2]."-".$subject; ?>">
					<td class="text-center"> <?php echo $date[$j] ?> </td>
					<td><?php echo $specificMark[$j] ?> </td>
					<td>
						<?php 
							if($date[$j] >= date("Y-m-d", strtotime('monday this week')) && $date[$j] <= date("Y-m-d", strtotime('sunday this week'))) { 
						?>
						<button type="button" class="btn btn-default btn-xs" 
							data-toggle="modal" data-target="#modalEdit"
							<?php echo "data-student = '$studentInfo[$i]' data-date='$date[$j]' data-hour='$hour[$j]' data-mark='$specificMark[$j]' data-subject='$subject'"; ?>
							onclick="modalEdit(this)">Edit</button>
							
						<button type="button" class="btn btn-danger btn-xs" 
							data-toggle="modal" data-target="#modalDelete"
							<?php echo "data-student = '$studentInfo[$i]' data-date='$date[$j]' data-hour='$hour[$j]' data-mark='$specificMark[$j]' data-subject='$subject'"; ?>
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
				<tr class="child-<?php echo $args[2]."-".$subject; ?>">
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
		<?php 
			echo "</div>";
		}
		echo "</div>";
	}
?>

</div></div>
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
					<td><input type="text" name="comboSubject" id="modalSubjectDelete" readonly="readonly" style="border:none; outline: none;"></td>
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
					<td><input type="text" name="comboSubject" id="modalSubjectEdit" readonly="readonly" style="border:none; outline: none;"></td>
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
										echo "<option value=" . $i . ".25>" . $i . "+</option>";
										echo "<option value=" . $i . ".5>" . $i . ".5</option>";
										echo "<option value=" . ($j-1) . ".75>" . $j . "-</option>";
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

	</div>
</div>

<?php 
    require_once("defaultFooter.php")
?>