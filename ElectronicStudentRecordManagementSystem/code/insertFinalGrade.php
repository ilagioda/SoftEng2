<?php
if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
	
	require_once("classTeacher.php");    
	$db=new dbTeacher();
	
	if(isset($_POST["student"]) && isset($_POST["subject"]) && isset($_POST["finalGrade"]) && $_POST["finalTerm"]) {
	
		$result = $db->insertFinalGrade($_POST["student"], $_POST["subject"], $_POST["finalGrade"], $_POST["finalTerm"]);
		if($result == -1) {
			echo "existingValue";
		} else {
			echo "ok";
		}
	} else {
		echo "error";
	}
}
?>