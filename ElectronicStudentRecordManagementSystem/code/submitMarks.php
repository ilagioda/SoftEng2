<?php
	require_once("basicChecks.php");
	
	$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}

if (!$loggedin) {
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
	$err = $msg= "";
	
if(isset($_POST["comboSubject"]) && isset($_POST["lessontime"]) && isset($_POST["comboHour"]) && isset($_POST["comboGrade"]) && isset($_POST["ssn"])){
	
	foreach($_POST['ssn'] as $key => $value) {
		$value = htmlspecialchars($value);
		if($_POST['comboGrade'][$key] > 0) {

			$result = $db->insertGrade($_POST['lessontime'], $_POST['comboHour'], $value, $_POST['comboSubject'], $_POST['comboGrade'][$key]);
			
			if($result == -1) {
				$err = "The student ".$value." already has a mark for the selected date and hour.";
			} else {
				$msg = "Marks successfully recorded!";
			}
		}
	}

        if($err != ""){
            echo <<<_ERR
            <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><span class="glyphicon glyphicon-send"></span> $err</strong></div>
_ERR;
        } 
        if ($msg != ""){
            echo <<<_MSG
            <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><span class="glyphicon glyphicon-send"></span> $msg</strong></div>
_MSG;
        }
}

?>

<ul class="nav nav-tabs">

  <li role="presentation" class="active"><a href="#">New record</a></li>
  <li role="presentation"><a href="viewAllMarks.php">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1> Record mark </h1>
	<div class="form-group">
	
		<form class="navbar-form navbar form-inline" method="POST" action="submitMarks.php">
		
			<table class="table table-hover">
			
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
				
				<tr><td><label>Subject </label></td><td>
					<select class="form-control" id="comboSubject" name="comboSubject" style="width:100%" required>
						<option value="" disabled selected>Select subject...</option>
						<?php
						$subjects = $teacher->getSubjectByClassAndTeacher($_SESSION['comboClass']);
						foreach($subjects as $subject) {
							echo "<option value=".$subject.">".$subject."</option>";
						}
					?>
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
<?php
	$students = $teacher->getStudents2($_SESSION["comboClass"]);
	if(!empty($students)) {

		
?>		
			<div class="panel panel-default" id="listStudents">
				<div class="panel-heading">
	
					<h2 class="panel-title">Students list: </h2>
				</div>

				<div class="panel-body">
					<table class="table" id="studentsTable" style="width:100%">
						<thead>
							<tr><td><label> Name</label> </td><td><label> Surname </label></td><td><label> SSN</label> </td><td><label> Grade </label></td></tr>
						</thead>
						<tbody id="result" style="width:100%">
						<?php 		
						foreach($students as $student) {
							$args = explode(",", $student);
				
							$name = $args[0];
							$surname = $args[1];
							$ssn = $args[2];
						?>
							<tr>
								<td><?php echo $name; ?></td>
								<td><?php echo $surname; ?></td>
								<td><input type="hidden" name="ssn[]" value="<?php echo $ssn; ?>"><?php echo $ssn; ?></td>
								<td><select class="form-control" name="comboGrade[]" id="comboGrade[]" style="width:100%"> 
								<?php 
								for($i=0; $i<=10; $i++) {
									$j=$i+1;
									echo "<option value='$i'>$i</option>";
									if($i!=10){
										echo "<option value='$i.25'>$i+</option>";
										echo "<option value='$i.5'>$i</option>";
										echo "<option value='($j-1).75'>$j-</option>";
									}
								} 
								
								?>
								</select></td>
							</tr>
						<?php } ?>
						</tbody>				
					</table>
				</div>
			</div>

			
			<table>
				<tr><td></td><td><button type="reset" class = "btn btn-default">Reset</button>
				<button type="submit" class="btn btn-success">Confirm</button></td></tr>
			</table>
<?php 
		
	} else {
		echo "<div class='alert alert-warning'>
					<h4 id='assignmentsTitle'>
					<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;
					No students for this class </h4></div>";
	}
?>
		</form>
		</div>
	</div>
</div>
<?php
	require_once("defaultFooter.php")
?>