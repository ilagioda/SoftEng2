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
	
/* 	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher"; */
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
	
    if(!isset($_REQUEST["comboClass"]) || !isset($_REQUEST["comboStudent"]) || !isset($_REQUEST["comboSubject"]) ||
		!isset($_REQUEST["lessontime"]) || !isset($_REQUEST["comboHour"]) || !isset($_REQUEST['comboGrade'])){
		
		if(!isset($_SESSION['comboClass']) || !isset($_SESSION['comboStudent']) || !isset($_SESSION['comboSubject']) ||
			!isset($_SESSION['lessontime']) || !isset($_SESSION['comboHour']) || !isset($_SESSION['comboGrade'])) {

			$error = 1;
		}
	} else {
        
        $_SESSION['comboClass'] = $_POST['comboClass'];
		$_SESSION['comboStudent'] = $_POST['comboStudent'];
		$_SESSION['comboSubject'] = $_POST['comboSubject'];
		$_SESSION['lessontime'] = $_POST['lessontime'];
        $_SESSION['comboHour'] = $_POST['comboHour'];
		$_SESSION['comboGrade'] = $_POST['comboGrade'];
	}
    
?>

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

  <li role="presentation"><a href="submitMarks.php">New mark</a></li>
  <li role="presentation"><a href="viewAllMarks.php">View all marks</a></li>
  <li role="presentation" class="active"><a href="#">Edit mark</a></li>

</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1> Update mark: </h1>
		<form class="navbar-form navbar-left" role="class" method="POST" action="updateMark.php">
			<table class="table">

				<tr><td><label>Class </label></td><td>
				<input class="form-control" disabled style="width:200%" value=" <?php 
					$selectedClass = $_SESSION["comboClass"];
					echo $selectedClass;
				?>">
				<input hidden name="comboClass" value="<?php echo $selectedClass ?>"/>
				</td></tr>


                <tr><td><label>Student </label></td><td>
				<input class="form-control" disabled style="width:200%" value="<?php 
					$selectedStudent = $_SESSION["comboStudent"];
					echo $teacher->getStudentByCod($selectedStudent) . " (" . $selectedStudent . ")";
				?>"> 
				<input hidden name="comboStudent" value="<?php echo $selectedStudent ?>"/>
				</td></tr>

				<tr><td><label>Subject </label></td><td>
				<select class="form-control" id="comboSubject" name="comboSubject" style="width:200%">	
				<?php 
					$selectedSubject = $_SESSION["comboSubject"];
				?>				
				<option value="<?php echo $selectedSubject ?>" selected> 
				<?php echo $selectedSubject ?>
				</option>
				<?php 
					$subjects=$teacher->getSubjectByClassAndTeacher($selectedClass);
					foreach($subjects as $value) {
						if($value != $selectedSubject)
							echo "<option value=".$value.">".$value."</option>";
					}		
				?>
				</select></td></tr>

				<tr><td><label>Date</label></td><td>  
				<input class="form-control" disabled type="date" value="<?php echo $_SESSION["lessontime"]; ?>"
						style="width:200%" required>
				<input hidden name="lessontime" value="<?php echo $_SESSION["lessontime"]; ?>"/>
				</td></tr>

				<tr><td><label>Hour</label></td><td>
				<input class="form-control" disabled name="comboHour" style="width:200%" value="<?php 
					$selectedHour = $_SESSION["comboHour"];
					echo $selectedHour;
				?>">
				<input hidden name="comboHour" value="<?php echo $selectedHour ?>"/>	
				</td></tr>	

				<tr><td><label>Grade</label></td><td>
				<select class="form-control" name="comboGrade" id="comboGrade" style="width:200%" required>
                <?php 
					$selectedGrade = $_SESSION["comboGrade"];
				?>
                <option value="<?php echo $selectedGrade ?>" selected>
                <?php echo $selectedGrade ?>
				</option> 
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
	
				<tr><td></td><td><button type="submit" class="btn btn-success">Update</button></td></tr>
			</table>
		</form>
	</div>
</div>



<?php
	require_once("defaultFooter.php")
?>