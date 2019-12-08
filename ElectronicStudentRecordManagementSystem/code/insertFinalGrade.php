<?php
if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
	
	require_once("classTeacher.php");    
	$db=new dbTeacher();
	
	if(isset($_POST["student"]) && isset($_POST["subject"]) && isset($_POST["mark"]) && $_POST["date"]) {
		$result = $db->insertFinalGrade($_POST["student"], $_POST["subject"], $_POST["mark"], $_POST["date"]);
		if($result == -1) {
			echo "error";
		} else {
			echo "ok";
		}
	} else {
		echo "error";
	}
}
?>