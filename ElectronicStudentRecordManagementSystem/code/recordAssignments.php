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
	
	var warning = $('<p class="text-danger">').text('Error you cannot select a weekend')
	$('#assignmentstime').change(function(e) {

      var d = new Date(e.target.value)
      if(d.getDay() === 6 || d.getDay() === 0) {
        $('#confirm').attr('disabled',true)
        $('#assignmentstime').after(warning);
      } else {
        warning.remove()
       $('#confirm').attr('disabled',false);
    }
})
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

  <li role="presentation" class="active"><a href="#">New record</a></li>
  <li role="presentation"><a href="viewAllAssignments.php">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1> Record assignments </h1>
	<div class="form-group">

		<form class="navbar-form navbar-left form-inline" role="class" method="POST" action="viewRecordedAssignments.php">
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
				</select><tr><td><label>Date</label></td><td>  
				<input class="form-control" type="date" name="assignmentstime" id="assignmentstime"
						min="<?php echo date("Y-m-d");  ?>" max="<?php echo date("Y-m-d", strtotime('2020-06-10')); ?>"
						style="width:100%" required> </td></tr>
				<tr><td><label>Assignments</label></td><td>
				<textarea class="form-control" name="assignments" rows="4" cols="50" placeholder="Assignments..." style="width:100%" required></textarea></td></tr>
	
				<tr><td></td><td><button type="reset" class = "btn btn-default">Reset</button>
				<button type="submit" id="confirm" class="btn btn-success">Confirm</button></td></tr>
			</table>
		</form>
		</div>
	</div>
</div>

<?php
	require_once("defaultFooter.php")
?>
