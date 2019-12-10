<?php
	require_once("basicChecks.php");
	
	$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}

if (!$loggedin) {
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
	
});

$(document).ready(function(){
	$("#comboClass").change(function() {
		
		document.getElementById('listStudents').style.display= 'block' ;
		document.getElementById('buttons_table').style.display= 'block' ;
		
		var comboClass = $("option:selected", this).val();

		$.ajax({
			type:		"POST",
			dataType:	"json",
			url:		"selectStudents.php",
			data:		"comboClass="+comboClass,
			cache:		false,
			success:	function(response){
							var first = '<tr><td><label> Name</label> </td><td><label> Surname </label></td><td><label> SSN</label> </td><td><label> Grade </label></td></tr>';
							$('#result').html(first);
							$.each(response, function(index, row) {	
								var args = row.split(",");
								var name = '<td>' + args[0] + '</td>';
								var surname = '<td>' + args[1] + '</td>';
								var ssn = '<td><input type="hidden" name="ssn[]" value="'+args[2]+'">' + args[2] + '</td>';
								var grade = '<td><select class="form-control" name="comboGrade[]" id="comboGrade[]" style="width:100%"> ';;
								for(var i=0; i<=10; i++) {
									j=i+1;
									grade += '<option value=' +i+ '>'+i+'</option>';
									if(i!=10){
										grade += '<option value='+i+'>'+i+'+</option>';
										grade += '<option value='+i+'.5>'+i+'.5</option>';
										grade += '<option value='+j+'->'+j+'-</option>';
									}
								}
								grade += '</select></td>';
								$('#result').append('<tr>' + name + surname + ssn + grade +'</tr>');
								
							});      
						},
			error: 		function(){
							alert("Error: students not loaded");
						}
		});
	});
});

</script>

<ul class="nav nav-tabs">

  <li role="presentation" class="active"><a href="#">New record</a></li>
  <li role="presentation"><a href="viewAllMarks.php">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1> Record mark </h1>
	<div class="form-group">
	
		<form class="navbar-form navbar form-inline" method="POST" action="viewSubmittedMarks.php">
		
			<table class="table">
			
				<tr><td><label> Date </label></td><td>  
				<input class="form-control" type="date" name="lessontime" id="lessontime" style="width:100%"
						min="<?php echo date("Y-m-d", strtotime('monday this week'));  ?>" 
						max="<?php 
							if(date("Y-m-d") <= date("Y-m-d", strtotime('friday this week'))) {
								echo date("Y-m-d");
							} else {
								echo date("Y-m-d", strtotime('friday this week')); 
							}
							?>"
						 required> </td>
				</tr>		
						
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
                
				<tr><td><label>Hour</label></td><td>
					<select class="form-control" name="comboHour" id="comboHour" style="width:100%" required>
						<option value="" disabled selected>Select hour...</option>
						<?php
							for($i=1; $i<=6; $i++) 
								echo "<option value=" . $i . ">" . $i . "</option>";
						?>	
					</select></td>
				</tr>
			</table>
			<div class="panel panel-default" id="listStudents" style="display: none;">
				<div class="panel-heading">
					<h2 class="panel-title">Students list: </h2>
				</div>
				<div class="panel-body">
					<table class="table" id="studentsTable" style="width:100%">
						<tbody id="result" style="width:100%">
						</tbody>				
					</table>
				</div>
			</div>

			
			<table id="buttons_table" style="display: none;">
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