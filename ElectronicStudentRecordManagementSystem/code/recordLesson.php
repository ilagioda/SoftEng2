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
    
?>

<ul class="nav nav-tabs">

  <li role="presentation" class="active"><a href="#">New record</a></li>
  <li role="presentation"><a href="viewAllLessonTopics.php">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1> Record daily lesson topics </h1>
	<div class="form-group">
		<form class="navbar-form navbar form-inline" method="POST" action="viewRecordedLesson.php">
		
			<table class="table table-hover">
				<tr><td><label>Subject </label></td>
				<td>
					<select class="form-control" id="comboSubject" name="comboSubject" style="width:100%" required>
					<option value="" disabled selected>Select subject...</option>
					<?php
						$subjects = $teacher->getSubjectByClassAndTeacher($_SESSION['comboClass']);
						foreach($subjects as $subject) {
							echo "<option value=".$subject.">".$subject."</option>";
						}
					?>
					</select>
				</td></tr>
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
					<option value='' disabled selected>Select hour...</option>
				<?php
					for($i=1; $i<=6; $i++) 
						echo "<option value='$i'>$i</option>";
				?>	
				</select></td></tr>	
				<tr><td><label>Topic(s)</label></td><td>
				<textarea class="form-control" name="topics" rows="4" cols="50" placeholder="Daily lesson topics..." style="width:100%" required></textarea></td></tr>
	
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
