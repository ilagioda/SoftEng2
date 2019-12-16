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
	require_once("db.php");

	$teacher=new Teacher();
	$db = new dbTeacher();
    
	if(isset($_POST['yesDelete'])) {
		if(!isset($_POST["lessontime"]) || !isset($_POST["comboHour"]) 
			|| !isset($_POST["comboClass"])) {
?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Oh no! Something went wrong...</p>
			</div>
<?php
		} else {
			$db->deleteDailyLesson($_POST['lessontime'], $_POST['comboHour'], $_POST['comboClass']);
?>
			<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link">  Daily lesson successfully deleted!</p>
			</div>
<?php	
		} 

	}
		
	if(isset($_POST['editButton'])) {
		if(!isset($_POST["comboClass"]) || !isset($_POST["comboSubject"]) ||
			!isset($_POST["lessontime"]) || !isset($_POST["comboHour"]) || !isset($_POST["topics"]) 
				|| empty($_POST["topics"])){
?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Oh no! Something went wrong...</p>
			</div>
<?php
		} else {		
			$db->updateDailyLesson($_POST['lessontime'], $_POST['comboHour'], $_POST['comboClass'], $_POST['comboSubject'], $_POST['topics']);
?>
			<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<p class="alert-link"> Daily lesson successfully updated!</p>
			</div>
<?php	
		} 
	}
?>

<script>

function modalDelete(obj) {
	
	var classID = obj.getAttribute("data-class");
	document.getElementById("modalClassDelete").value = classID;
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectDelete").value = subject;
	
	var date = obj.getAttribute("data-date");
	document.getElementById("modalDateDelete").value = date;
	
	var hour = obj.getAttribute("data-hour");
	document.getElementById("modalHourDelete").value = hour;
	
	var topic = obj.getAttribute("data-topics");
	document.getElementById("modalTopicDelete").value = topic;
	
}

function modalEdit(obj) {
	
	var classID = obj.getAttribute("data-class");
	document.getElementById("modalClassEdit").value = classID;
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectEdit").value = subject;
	
	var date = obj.getAttribute("data-date");
	document.getElementById("modalDateEdit").value = date;
	
	var hour = obj.getAttribute("data-hour");
	document.getElementById("modalHourEdit").value = hour;
	
	var topic = obj.getAttribute("data-topics");
	document.getElementById("modalTopicEdit").value = topic;
	
}
</script>


<ul class="nav nav-tabs">
  <li role="presentation"><a href="recordLesson.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>
<div class="panel panel-default" id="container">
	<div class="panel-body">

<h1> All lectures: </h1>

	<?php 
		$lectures = $teacher->getLectures();
		foreach((array)$lectures as $value) {
	?>	
		<div class="panel panel-default">
			<div class="panel-body">
				<form role="class" method="POST" action="">
					<table class="table">
					<?php
						$args = explode(",",$value);
					?>
						<tr><td> Class: </td><td><?php echo $args[0];?></td></tr>
						<input type="hidden" name="comboClass" value="<?php echo $args[0];?>">

						<tr><td> Subject: </td><td><?php echo $args[1];?></td></tr>
						<input type="hidden" name="comboSubject" value="<?php echo $args[1];?>">

						<tr><td> Date: </td><td><?php echo $args[2];?></td></tr>
						<input type="hidden" name="lessontime" value="<?php echo $args[2];?>">

						<tr><td> Hour: </td><td><?php echo $args[3];?></td></tr>
						<input type="hidden" name="comboHour" value="<?php echo $args[3];?>">

						<tr><td> Topics: </td><td><?php echo $args[4];?></td></tr>
						<input type="hidden" name="topics" value="<?php echo $args[4];?>">
					</table>
					<?php
						$dateLesson = $args[2];
						if($dateLesson >= date("Y-m-d", strtotime('monday this week')) && $dateLesson <= date("Y-m-d", strtotime('sunday this week'))) { 
					?>
						<button type="button" class="btn btn-default btn-xs" style='width:20%'
							data-toggle="modal" data-target="#modalEdit"
							<?php 
								echo "data-class='$args[0]' data-subject='$args[1]' data-date='$args[2]' data-hour='$args[3]' data-topics='$args[4]'"; 
							 ?>
							onclick="modalEdit(this)">Edit</button>
							
						<button type="button" class="btn btn-danger btn-xs" style='width:20%'
							data-toggle="modal" data-target="#modalDelete"
							<?php 
							echo "data-class='$args[0]' data-subject='$args[1]' data-date='$args[2]' data-hour='$args[3]' data-topics='$args[4]'"; 
							?>	
							onclick="modalDelete(this)">Delete</button>
					<?php	
						} else { ?>
							<div class="alert alert-warning" role="alert"> 
								<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								 This lecture is not editable/erasable as it relates to past weeks...
							</div> <?php
						}
					?>
				</form>
			</div>
		</div>
	<?php
		}
	?>
	
	
	<!-- Modal -->
<div id="modalDelete" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body form-group text-center"></br>
	  
	    <button type="button" class="close" data-dismiss="modal">&times;</button>
		<form method="POST" action="">
			<table class="table table-hover text-center">
				<tr><td><label> Class </label></td>
					<td><input id="modalClassDelete" type="text" name="comboClass" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Subject </label></td>
					<td><input id="modalSubjectDelete" type="text" name="comboSubject" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input id="modalDateDelete" type="text" name="lessontime" readonly="readonly" style="border:none; background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Hour </label></td>
					<td><input id="modalHourDelete" type="text" name="comboHour" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Topics </label></td>
					<td>
						<textarea id="modalTopicDelete" name="topics" readonly="readonly" style="border:none;  background:none; outline: none;"></textarea>
					</td>
				</tr>
			</table>
	
			<h3><strong>Do you really want to delete this record?</strong> </h3>
			<button name="yesDelete" type="submit" class="btn btn-default btn-lg" onclick="this.form.action='viewAllLessonTopics.php'">Yes</button>
			<button type="submit" class="btn btn-default btn-lg" data-dismiss="modal">No</button>
		</form>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="modalEdit" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body form-group text-center"></br>
	  
	    <button type="button" class="close" data-dismiss="modal">&times;</button>
		<form method="POST" action="">
			<table class="table table-hover text-center">
				<tr><td><label> Class </label></td>
					<td><input id="modalClassEdit" type="text" name="comboClass" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Subject </label></td>
					<td><input id="modalSubjectEdit" type="text" name="comboSubject" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input id="modalDateEdit" type="text" name="lessontime" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Hour </label></td>
					<td><input id="modalHourEdit" type="text" name="comboHour" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Topics </label></td>
					<td>
						<textarea id="modalTopicEdit" class="form-control" name="topics" rows="4" style="width:60%"></textarea>
					</td>
				</tr>
			</table>
			<button name="editButton" type="submit" class="btn btn-success" style="width:20%" onclick="this.form.action='viewAllLessonTopics.php'">Edit</button>
			<button type="submit" class="btn btn-default" style="width:20%" data-dismiss="modal">Cancel</button>
		</form>
      </div>
    </div>

  </div>
</div>
	
	
	</div>
</div>

<?php 
    require_once("defaultFooter.php")
?>

