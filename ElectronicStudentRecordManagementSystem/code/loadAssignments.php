<?php	

if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {

	if (!isset($_SESSION['comboClass'])) {
        header("Location: chooseClass.php");
        exit;
    }

	require_once("classTeacher.php");    
	$teacher=new Teacher();

	$assignments = array();

	if(isset($_POST["date"])) {
		
		$date = $_POST["date"];
		echo json_encode($teacher->getAssignmentsByClassAndDate($_SESSION["comboClass"], $date)); 
	}
}
?>
