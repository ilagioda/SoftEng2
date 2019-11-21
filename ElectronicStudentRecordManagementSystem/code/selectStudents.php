<?php	

	require_once("classTeacher.php");    
	$teacher=new Teacher();

	$students = array();

	if(isset($_POST["comboStudent"])) {
		
		$selectedClass = $_POST["comboClass"];
		$students = $teacher->getStudents($selectedClass);
		
		$output = "";
		
		foreach($students as $student) {
			$output .= "<option value=".$student.">".$student."</option>";
		}
		echo $output;
	}
?>
