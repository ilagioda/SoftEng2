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
});

$(document).ready(function(){
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
});

</script>

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

  <li role="presentation" class="active"><a href="#">New mark</a></li>
  <li role="presentation"><a href="viewAllMarks.php">View all marks</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body" >
	<h1> Submit new Mark: </h1>
	<div class="form-group">

		<form class="navbar-form navbar-left form-inline" method="POST" action="viewSubmittedMarks.php">
		
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
				</td></tr>
				</select>
                <tr><td><label>Student </label></td><td>
                <select class="form-control" id="comboStudent" name="comboStudent" style="width:100%" required>
				<option value="" disabled selected>Select student...</option>
				</td></tr>
				</select>
                <tr><td><label>Date</label></td><td>  
				<input class="form-control" type="date" name="lessontime" id="lessontime"
						min="<?php echo date("Y-m-d", strtotime('monday this week'));  ?>" 
						max="<?php 
							if(date("Y-m-d") <= date("Y-m-d", strtotime('friday this week'))) {
								echo date("Y-m-d");
							} else {
								echo date("Y-m-d", strtotime('friday this week')); 
							}
							?>"
						style="width:100%" required> </td></tr>
				<tr><td><label>Hour</label></td><td>
				<select class="form-control" name="comboHour" id="comboHour" style="width:100%" required>
				<?php
					for($i=1; $i<=6; $i++) 
						echo "<option value=" . $i . ">" . $i . "</option>";
				?>	
				</select></td></tr>	
				<tr><td><label>Grade</label></td><td>
				<select class="form-control" name="comboGrade" id="comboGrade" style="width:100%" required>
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
				</select></td></tr>	
	
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
