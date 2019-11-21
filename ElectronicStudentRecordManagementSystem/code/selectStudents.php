<?php	

	require_once("classTeacher.php");    
	$teacher=new Teacher();

	$students = array();

	if(isset($_POST["comboClass"])) {
		
		$selectedClass = $_POST["comboClass"];
		$students = $teacher->getStudents2($selectedClass);
		
		$output = "";
		
		foreach($students as $student) {
			$args = explode(",",$student);
			echo $args[0]." ".$args[1]." ".$args[2];
			$output .= "<option value=".$args[2].">".$args[0]." ".$args[1]." (".$args[2].")</option>";
		}
		echo $output;
	}
?>
