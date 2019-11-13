<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
	
	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher";
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
    
?>

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
				<select name="comboClass" style="width: 350px" required> 
				<?php 
					$classes=$teacher->getClasses();
					foreach($classes as $value) {
						echo "<option value=".$value.">".$value."</option>";
					}
				?>
				</select></td></tr>
				<tr><td><label>Subject </label></td><td>
				<select name="comboSubject" style="width: 350px" required>
				<?php 
					if(isset($_POST['comboClass'])) {
						$selectedClass = $_POST['comboClass'];

						$subjects=$teacher->getSubjectByClass($selectedClass);
						foreach($subjects as $value) {
							echo "<option value=".$value.">".$value."</option>";
						}
					}
					if(isset($_POST['comboSubject'])) {
						$selectedSubject = $_POST['comboSubject'];
					}
				?>
				</select></td></tr>
				<tr><td><label>Date</label></td><td>  
				<?php 					
					
					?>
				<input type="date" name="lessontime"
						min="<?php echo date("Y-m-d", strtotime('monday this week'));  ?>" 
						max="<?php 
							if(date("Y-m-d") <= strtotime('friday this week')) {
								echo date("Y-m-d");
							} else {
								echo date("Y-m-d", strtotime('friday this week')); 
							} 
							?>"
						style="width: 350px" required> </td></tr>
						<?php 
	
							if(isset($_POST['lessontime'])) {
								$selectedDate = date('Y-m-d', strtotime($_POST['lessontime'])); 
							}
						?>
				<tr><td><label>Hour</label></td><td>
				<select name="comboHour" style="width: 350px" required>
				<?php
					for($i=1; $i<=5; $i++) 
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