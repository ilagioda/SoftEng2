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
			$(this).text($(this).text() == 'Add grades' ? 'Hide grades' : 'Add grades');		
		});
		$('tr[class^=child-]').hide().children('td');

	});
	
	
	$('.confirmButton').click(function () {
		
        var id = $(this).attr("id");
		
		var student = $(this).attr("data-student");
		var subject = $(this).attr("data-subject");
		var idButton = $(this).attr("data-select");
		var optionSelected = $('#'+idButton+' option:selected').text()
		var finalTerm = $(this).attr("data-date");
		
		// ajax call for inserting the final grade record inside the DB
		$.ajax({
			type:		"POST",
			dataType:	"text",
			url:		"insertFinalGrade.php",
			data:		"student="+student+", subject="+subject+", mark="+optionSelected+", date="+finalTerm,
			cache:		false,
			success:	function(response){
							// the button "confirm" becomes "edit" to feedback the teacher that the grade has been inserted
							if(response == "ok") {
								var button = $('#confirmButton'+idButton);
								button.attr('disabled',true);
								button.html("Done");
								setTimeout(function() {
									button.attr('disabled',false);
									button.html("Edit").removeClass("btn-success").addClass("btn-default");;
								},2000);
							} else {
								alert("Error: final grade not inserted");
							}
						},
			error: 		function(){
							alert("Error: final grade not inserted");
						}
		});
		
	});
	
	
	$(".comboGrade").change(function () {	
		//var id = $('option:selected', this).attr('value');
		// alert(id);
		var id = $(this).attr('id')

		$('#confirmButton'+id).attr('disabled',false);

	});

	
	
});



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
		<div class="form-group">
			<form class="navbar-form navbar form-inline" role="class" method="POST" action="publishFinalGrade.php">
			
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
					<tr><td><label> Final term </label></td><td>
						<input class="form-control" type="date" name="finalTerm" id="finalTerm"
							min="<?php echo date("Y-m-d");  ?>" max="<?php echo date("Y-m-d", strtotime('2020-06-10')); ?>"
							style="width:100%" required> </td>
					</tr>
					<tr><td><button type="submit" name="okPublish" class="btn btn-success">OK</button></td></tr>
				</table>
			</form>
		</div>
	
	<?php 
	if(!isset($_POST["okPublish"])) {
		if(!isset($_SESSION['okPublish'])) {
			$error = 1;
		}
	} else { 
		$_SESSION['okPublish']=$_POST['okPublish'];
		$_SESSION["comboClass"] = $_POST["comboClass"];
		$_SESSION["finalTerm"] = $_POST["finalTerm"];
	}
	
	if(isset($_SESSION['okPublish'])) {
	
		
			$i = 0; // for a student student
			$idButton = 0;	
			
			echo "<form method='POST' action='publishFinalGrade.php' class='form-group'>";
			$selectedClass = $_SESSION["comboClass"];
			$finalTerm = $_SESSION["finalTerm"];
	?>
			
			<h1 id="titleStudent"> Publish final grades  <small>Class: <?php echo $selectedClass ?></small></h1>
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
					<td><span class="btn btn-primary pull-right" id="addGrades">Add grades</span></td>
					
				</tr>
			
			<?php 
				$subjects = array();
				$subjects = $teacher->getSubjectByClassAndTeacher($selectedClass);
				
				if ($subjects) {
			?>			
					<tr class="child-<?php echo $args[2] ?>">
						<td class="text-center"> <strong> Subject </strong>  </td>
						<td class="text-center"><strong> Final grade </strong>  </td>
						<td></td>
					</tr>
				<?php
					foreach($subjects as $subject) {
						
						$rows = $teacher->viewStudentMarks($args[2], $subject);
						$marks = array();
						$j=0;
						if ($rows) {
							foreach($rows as $row) {
								$mark = explode(",", $row);
								$marks[$j] = $mark[1];
								$j++;
							}
							
							$average = array_sum($marks) / count($marks);
						?>
							
							<tr class="child-<?php echo $args[2] ?>">
								<td class="text-center"> <?php echo $subject ?> </td>
								<?php
									if(is_int($average)) {
										// if the average is an integer, then the value can be printed in the field and the teacher can already confirm or modify the grade
										echo "<td>
												<select class='form-control comboGrade' name='comboGrade' id='$idButton' style='width:60%' required>
													<option value='' selected>$average</option>";
												for($k=0; $k<=10; $k++) {
													echo "<option value='$k'>$k</option>"; 
												}
											echo "</select></td>";									
										echo "<td><button type='button' class='btn btn-success btn-xs confirmButton' style='width:25%'
												data-student=$args[2] data-subject=$subject data-select=$idButton
												id='confirmButton$idButton'>Confirm</button></td>";

									} else {
										// if the average is a float, then the teacher must change it into an integer and the "confirm" button is disabled until she changes the grade
										$option = round($average, 2);
										echo "<td>
												<select class='form-control comboGrade' name='comboGrade' id='$idButton' style='width:60%' required>
													<option value='' selected disabled>$option</option>";
												for($k=0; $k<=10; $k++) {
													echo "<option value='$k'>$k</option>"; 
												}
											echo "</select></td>";	
										echo "<td><button type='button' class='btn btn-success btn-xs confirmButton' style='width:25%'
												data-student=$args[2] data-subject=$subject data-select=$idButton data-date=$finalTerm
												id='confirmButton$idButton' disabled>Confirm</button></td>";
									}
									$idButton++;

								?>
							</tr>
					<?php	
						} else { 
					?>
					
							<tr class="child-<?php echo $args[2] ?>">
								<td class="text-center"> <?php echo $subject ?> </td>
								<td>
									<select class="form-control comboGrade" name="comboGrade" id="<?php echo $idButton ?>" style="width:60%" required>
										<option type="text" value="" selected>N.C.</option> 
										<?php
											for($k=0; $k<=10; $k++) {
													echo "<option value='$k'>$k</option>"; 
											} 

										?>	
									</select>
								</td>
								<td>
									<button type="button" class="btn btn-success btn-xs confirmButton" style='width:25%'
												data-student=<?php echo $args[2] ?> data-subject=<?php echo $subject ?> data-select=<?php echo $idButton ?>
												id='confirmButton<?php echo $idButton ?>'>Confirm</button>
								</td>
							</tr>
					<?php
						$idButton++;
						}
					}
				}
				$i++;
			}
	}
					?>	

				</tbody>

			</table>
		</form>
    </div>
</div>


<?php 
    require_once("defaultFooter.php")
?>