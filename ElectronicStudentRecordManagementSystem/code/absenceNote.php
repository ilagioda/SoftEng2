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

<script type="text/javascript">

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
		var comboClass = $("option:selected", this).val();

		$.ajax({
			type:		"POST",
			dataType:	"text",
			url:		"selectStudents.php",
			data:		"comboClass="+comboClass,
			cache:		false,
			success:	function(response){
							$('#comboStudent').html(response);
						},
			error: 		function(){
							alert("Error: students not loaded");
						}
		});
	});
	
	var warning = $('<p class="text-danger">').text('Error: you must select a working day!')
	$('#absencetime').change(function(e) {

		var d = new Date(e.target.value)
		if(d.getDay() === 6 || d.getDay() === 0 || checkIfHoliday(d)) {
			$('#confirm').attr('disabled',true)
			$('#absencetime').after(warning);
		} else {
			warning.remove()
			$('#confirm').attr('disabled',false);
		}
	});
	
});

</script>

<style> 
	#container {
		box-shadow: 0px 2px 25px rgba(0, 0, 0, .25);
		padding:0 15px 0 15px;
	}
</style>


<div class="panel panel-default" id="container">
	<div class="panel-body">

	<h1> Record absence note: </h1>
	<div class="form-group">

		<form class="navbar-form navbar-left form-inline" method="POST" action="viewAbsenceNote.php">
		
			<table class="table">
				<tr><td><label>Class </label></td><td>
				<select class="form-control" id="comboClass" name="comboClass" style="width:100%" required> 
				<option value="" disabled selected>Select class...</option>

				<?php 
					$classes=$teacher->getClassesByTeacher();
					foreach($classes as $value) {
						echo "<option value=".$value.">".$value."</option>";
					}
				?>
				</select></td></tr>
				
				<tr><td><label>Subject </label></td><td>
				<select class="form-control" id="comboSubject" name="comboSubject" style="width:100%" required>
				<option value="" disabled selected>Select subject...</option>
				</select></td></tr>
				
				
				<tr><td><label>Student</label></td><td>
				<select class="form-control" id="comboStudent" name="comboStudent" style="width:100%" required> 
				<option value="" disabled selected>Select student...</option>				
				</select></td></tr>
				
				
				<tr><td><label>Date</label></td><td>  
				<input class="form-control" type="date" name="absencetime" id="absencetime"
						min="<?php echo date("Y-m-d", strtotime('2019-09-09')); ?>" max="<?php echo date("Y-m-d");  ?>" 
						style="width:100%" required> </td></tr>
				<tr><td><label>Hour</label></td><td>
				<select class="form-control" name="comboHour" id="comboHour" style="width:100%" required>
				<?php
					for($i=1; $i<=6; $i++) 
						echo "<option value=" . $i . ">" . $i . "</option>";
				?>	
				</select></td></tr>	
				<tr><td><label>Absence note</label></td><td>
				<textarea class="form-control" name="absencenote" rows="4" cols="50" placeholder="Absence note..." style="width:100%" required></textarea></td></tr>
	
				<tr><td></td><td><button type="reset" class = "btn btn-default">Reset</button>
				<button type="submit" class="btn btn-success">Confirm</button></td></tr>
			</table>
		</form>
		</div>
	</div>
</div>
<?php 
    require_once("defaultFooter.php")
?>

