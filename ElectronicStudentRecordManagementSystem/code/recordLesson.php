<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
	
	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher";
	
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
							// document.getElementById("comboSubject").innerHTML =	response;
							$('#comboSubject').html(response);
						},
			error: 		function(){
							alert("Error: subjects not loaded");
						}
		});
	});
});

</script>


<ul class="nav nav-tabs">

  <li role="presentation" class="active"><a href="#">New record</a></li>
  <li role="presentation"><a href="viewAllLessonTopics.php">View all records</a></li>
</ul>

<div class="panel panel-default">
	<div class="panel-body">
	<h1> Record daily lesson topics </h1>
		<form class="navbar-form navbar-left" role="class" method="POST" action="viewRecordedLesson.php">
			<table class="table">
				<tr><td><label>Class </label></td><td>
				<select id="comboClass" name="comboClass" style="width: 350px" required> 
				<option value="" disabled selected>Select class...</option>

				<?php 
					$classes=$teacher->getClassesByTeacher();
					foreach($classes as $value) {
						echo "<option value=".$value.">".$value."</option>";
					}
				?>
				</select></td></tr>
				<tr><td><label>Subject </label></td><td>
				<select id="comboSubject" name="comboSubject" style="width: 350px" required>
				<option value="" disabled selected>Select subject...</option>
				</td></tr>
				</select><tr><td><label>Date</label></td><td>  
				<input type="date" name="lessontime" id="lessontime"
						min="<?php echo date("Y-m-d", strtotime('monday this week'));  ?>" 
						max="<?php 
							if(date("Y-m-d") <= date("Y-m-d", strtotime('friday this week'))) {
								echo date("Y-m-d");
							} else {
								echo date("Y-m-d", strtotime('friday this week')); 
							}
							?>"
						style="width: 350px" required> </td></tr>
				<tr><td><label>Hour</label></td><td>
				<select name="comboHour" id="comboHour" style="width: 350px" required>
				<?php
					for($i=1; $i<=6; $i++) 
						echo "<option value=" . $i . ">" . $i . "</option>";
				?>	
				</select></td></tr>	
				<tr><td><label>Topic(s)</label></td><td>
				<textarea name="topics" rows="4" cols="50" style="width: 350px" placeholder="Daily lesson topics..." required></textarea></td></tr>
	
				<tr><td></td><td><button type="reset" class = "btn btn-default">Reset</button>
				<button type="submit" class="btn btn-success">Confirm</button></td></tr>
			</table>
		</form>
	</div>
</div>

<?php
	require_once("defaultFooter.php")
?>
