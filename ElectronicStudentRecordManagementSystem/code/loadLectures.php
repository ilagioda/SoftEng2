<?php	

if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {

	if (!isset($_SESSION['comboClass'])) {
        header("Location: chooseClass.php");
        exit;
    }

	require_once("classTeacher.php");    
	$db=new dbTeacher();

	if(isset($_POST["date"]) && isset($_POST["subject"])) {
		
		$date = $_POST["date"];
		$subject = $_POST["subject"];
		echo json_encode($db->getLecturesByTeacherClassSubjectAndDate($_SESSION['user'], $_SESSION['comboClass'], $subject, $date)); 
	}
}
?>
