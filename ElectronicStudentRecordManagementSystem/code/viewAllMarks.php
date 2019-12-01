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
			// show students list
			$selectedClass = $_POST["comboClass"];
			$selectedSubject = $_POST["comboSubject"];
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
					echo "".$args[0]." ".$args[1]." ".$args[2]."";
				?> 
				</td><td><span class="btn btn-primary pull-right" id="viewMarks">View marks</span></td></tr>
			
			<?php 
				$marks = $teacher->viewStudentMarks($args[2], $selectedSubject);
				if ($marks) {
			?>
				<tr class="child-<?php echo $args[2] ?>">
					<td class="text-center"> <strong> Date </strong>  </td>
					<td><strong> Specific marks </strong>  </td>
				</tr>
				<?php
					foreach($marks as $mark){
						
						$argm = explode(",",$mark);
						$date = $argm[0];
						$specificMark = $argm[1];
						
			?>
				<tr class="child-<?php echo $args[2] ?>">
                <td class="text-center"> <?php echo $date ?> </td>
                <td><?php echo $specificMark ?> </td>
            </tr>
			<?php
					}
				} else { ?>
				<tr class="child-<?php echo $args[2] ?>">
						<td></td><td>No marks </td>
					</tr>
				<?php
				}
			}
			?>
			</tbody>
			</table>
			<?php
	}
			?>
	</div>
</div>

<?php 
    require_once("defaultFooter.php")
?>