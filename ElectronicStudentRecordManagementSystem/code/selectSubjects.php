<?php	

if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
	
	require_once("classTeacher.php");    
	$teacher=new Teacher();

	$subjects = array();

	if(isset($_POST["comboClass"])) {
		
		$selectedClass = $_POST["comboClass"];
		$subjects = $teacher->getSubjectByClassAndTeacher($selectedClass);
		
		$output = "";
		
		foreach($subjects as $subject) {
			$output .= "<option value=".$subject.">".$subject."</option>";
		}
		echo $output;
	}
}
?>