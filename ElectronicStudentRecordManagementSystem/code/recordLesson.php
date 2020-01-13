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
	$class = $_SESSION["comboClass"];
	
    
	if(isset($_POST["comboSubject"]) && isset($_POST["lessontime"]) && isset($_POST["comboHour"]) && isset($_POST["topics"]) 
		&& !empty($_REQUEST["topics"])){
			
		$subject = $_POST['comboSubject'];
		$date = $_POST['lessontime'];
		$hour = $_POST['comboHour'];
		$topics = $_POST['topics'];
		
		$result = $db->insertDailyLesson($date, $hour, $class, $_SESSION['user'], $subject, $topics);
		
		if($result == -1) {
			?>
			<div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><span class="glyphicon glyphicon-send"></span>Lecture already inserted! </strong>
			</div>
			<?php
		} else {
			?> 
			<div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><span class="glyphicon glyphicon-send"></span> Daily lesson successfully recorded!</strong>
				</div>
			<?php 
		}
	}
?>

<ul class="nav nav-tabs">

  <li role="presentation" class="active"><a href="#">New record</a></li>
  <li role="presentation"><a href="viewAllLessonTopics.php">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1> Record daily lesson topics </h1>
	<div class="form-group">
		<form class="navbar-form navbar form-inline" method="POST" action="recordLesson.php">
		
			<table class="table table-hover">
				<tr><td><label>Subject </label></td>
				<td>
					<select class="form-control" id="comboSubject" name="comboSubject" style="width:100%" required>
					<option value="" disabled selected>Select subject...</option>
					<?php
						$subjects = $teacher->getSubjectByClassAndTeacher($_SESSION['comboClass']);
						foreach($subjects as $subject) {
							echo "<option value='$subject'>".$subject."</option>";
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
			</table>
			<button type="reset" class="btn btn-default" style="margin-right:5px">Reset</button>
			<button type="submit" class="btn btn-success">Confirm</button>
		</form>
		</div>
	</div>
</div>

<?php
	require_once("defaultFooter.php")
?>
