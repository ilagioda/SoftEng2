<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

$_SESSION['user']="ABCD97";
$_SESSION['role']="teacher";

require_once("classTeacher.php");

$teacher=new Teacher();
$_SESSION['subject']=$_POST['subject'];

?>



<body>

<?php

echo "<h2> List of student of class " . $_SESSION['class'] .", for subject: " . $_SESSION['subject'] . "</h2>" ;

echo "<h3> Select a student: </h3>";


$studentList=$teacher->getStudents($_SESSION['class']);



echo $studentList;

?>


<?php
require_once("defaultFooter.php")
?>