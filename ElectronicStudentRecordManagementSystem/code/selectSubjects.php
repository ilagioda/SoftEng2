<?php	

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedNavbar.php";
}

/* 	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher"; */
	
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
?>