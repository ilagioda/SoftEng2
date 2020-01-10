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

	require_once("functions.php");
	
	$semester = getCurrentSemester();
	$now = new DateTime('now');
	$beginSemester = ($now->format('Y-m-d'));
	$endSemester = $semester[1];
	
?>

<script>

function checkIfHoliday(day) {
	
	var holidays = ["2019-12-23","2019-12-24", "2019-12-25", "2019-12-26",
					"2019-12-27", "2019-12-28", "2019-12-29", "2019-12-30",
					"2019-12-31", "2020-01-01", "2020-01-02", "2020-01-03",
					"2020-01-04", "2020-02-22", "2020-02-23", "2020-02-24",
					"2020-02-25", "2020-02-26", "2020-04-09", "2020-04-10",
					"2020-04-11", "2020-04-12", "2020-04-13", "2020-04-14",
					"2020-05-02", "2020-05-02"];
						
						
	for(var i=0; i<holidays.length; i++) {
		var temp = new Date(holidays[i]);
		if(day.getTime() == temp.getTime()) {
			return true;
		}
	}
	
	return false;
}


$(document).ready(function(){
	
	$(function() {
		// this function allows to open/hide the window that shows the rows subject-grade
		
		$('tr.parent td span.btn').on("click", function(){
			var idOfParent = $(this).parents('tr').attr('id');
			$('tr.child-'+idOfParent).toggle();	
			$(this).text($(this).text() == 'Add grades' ? 'Hide grades' : 'Add grades');		
		});
		$('tr[class^=child-]').hide().children('td');

	});
	
	
	var warning = $('<p class="text-danger">').text('Error: you must select a working day!')
	$('#finalTerm').change(function(e) {

		var d = new Date(e.target.value)
		if(d.getDay() === 6 || d.getDay() === 0 || checkIfHoliday(d)) {
			$('#okPublish').attr('disabled',true);
			$('#finalTerm').after(warning);
		} else {
			warning.remove()
			$('#okPublish').attr('disabled',false);
		}
	});
	
	$('.confirmButton').click(function () {
		
       //  var id = $(this).attr("id");
		
		var student = $(this).attr("data-student");
		var subject = $(this).attr("data-subject");
		var idButton = $(this).attr("data-select");
		var finalGrade = $('#'+idButton+' option:selected').text();
		var finalTerm = $(this).attr("data-finalTerm");
		
		// ajax call for inserting the final grade record inside the DB
		$.ajax({
			type:		"POST",
			dataType:	"text",
			url:		"insertFinalGrade.php",
			data:		"student="+student+"&subject="+subject+"&finalGrade="+finalGrade+"&finalTerm="+finalTerm,
			cache:		false,
			success:	function(response){
							// the button "confirm" becomes "done" to feedback the teacher that the grade has been inserted
							if(response == "ok") {
								var button = $('#confirmButton'+idButton);
								button.attr('disabled',true);
								button.html("Done");
								$('#'+idButton).replaceWith("<p class='form-control' style='width:60%'>"+finalGrade+" </p>");
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

<div class="panel panel-default" id="container">
	<div class="panel-body">
	
	<?php 
	if(!isset($_POST["okPublish"])) {
		if(!isset($_SESSION['okPublish'])) {
			?>
					<form class="form-horizontal" method="POST" action="publishFinalGrade.php" enctype="multipart/form-data">
			<h1 class="text-center">Publish final grades</h1><br>
			<div class="form-group text-center">
			<label> Select the date of the term </label>
			<input class="form-control" type="date" name="finalTerm" id="finalTerm"
					min="<?php echo $beginSemester;  ?>" max="<?php echo $endSemester; ?>" required>
			<button type="submit" name="okPublish" id="okPublish" class="btn btn-success">OK</button>
			</div>

		</form>
			<?php
		}
	} else { 
		$_SESSION['okPublish']=htmlspecialchars($_POST['okPublish']);
		$_SESSION["finalTerm"] = htmlspecialchars($_POST["finalTerm"]);
		
	}
	
	if(isset($_SESSION['okPublish'])) {
		
		$finalTerm = $_SESSION["finalTerm"];
		
		$coordinator = $db->isCoordinator($_SESSION['user'], $selectedClass);
		
		if($coordinator) {		
		
			$i = 0; // for a student student
			$idButton = 0;	
			
			echo "<form method='POST' action='publishFinalGrade.php' class='form-group'>";

	?>
			
			<h1 id="titleStudent"> Publish final grades  <small> Final term:  <?php echo $finalTerm ?></small></h1>
			<table class="table table-condensed" id="studentTable" style="border-collapse:collapse;">
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
						
						$resultFinalGrade = $db->getFinalGrade($args[2], $subject, $finalTerm);
						
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

								if($resultFinalGrade == -1) {
									// if the final grade has been not inserted yet, then check the average 
									
									if(is_int($average)) {
										// if the average is an integer, then the value can be printed in the field and the teacher can already confirm or modify the grade
										echo "<td>
												<select class='form-control comboGrade' name='comboGrade' id='$idButton' style='width:60%' required>
													<option value='' selected>$average</option>";
												for($k=0; $k<=10; $k++) {
													echo "<option value='$k'>$k</option>"; 
												}
											echo "</select></td>";		
	
											// the grade does not exist --> button CONFIRM
											echo "<td><button type='button' class='btn btn-success btn-xs confirmButton' style='width:25%'
												data-student='$args[2]' data-subject='$subject' data-select='$idButton' data-finalTerm='$finalTerm'
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
										echo "<td><button type='button' class='btn btn-success btn-xs confirmButton' style='width:25%' title='You can confirm after changing the final grade to an integer value'
												data-student='$args[2]' data-subject='$subject' data-select='$idButton' data-finalTerm='$finalTerm'
												id='confirmButton$idButton' disabled>Confirm</button></td>";
									}
									$idButton++;
								} else {
									// if the final grade has been inserted, then the value confirmed before is loaded
										echo "<td><p class='form-control comboGrade' name='comboGrade' style='width:60%'>$resultFinalGrade</p></td>";		
										echo "<td><button class='btn btn-success btn-xs' type='button' style='width:25%' title='Final grade assigned' disabled>Done</button></td>";
								}

								?>
							</tr>
					<?php	
						} else { 
							if($resultFinalGrade == -1) {

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
												data-student="<?php echo $args[2] ?>" data-subject="<?php echo $subject ?>" data-select="<?php echo $idButton ?>"
												data-finalTerm="<?php echo $finalTerm ?>" id='confirmButton<?php echo $idButton ?>'>Confirm</button>
								</td>
							</tr>
					<?php
							} else {
								?>
								<tr class="child-<?php echo $args[2] ?>">
								<td class="text-center"> <?php echo $subject ?> </td>
								<td><p class='form-control comboGrade' name='comboGrade' style='width:60%'><?php echo $resultFinalGrade ?></p></td>
								<td><button class='btn btn-success btn-xs' type='button' style='width:25%' disabled>Done</button></td>
							</tr>
								<?php
							}
							$idButton++;
						}
					}
				}
				$i++;
			}
			?>
			</table>
		</form>
		<?php
		} else {
			echo "<div class='alert alert-danger' role='alert'><strong>You are not the coordinator teacher (and you cannot publish final grades) of the class $selectedClass. </strong></div>";
		}
	}
		?>	

    </div>
</div>


<?php 
    require_once("defaultFooter.php")
?>