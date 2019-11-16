<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

require_once("classTeacher.php");

$teacher=new Teacher();

if(!empty($_POST['subject']))
    $_SESSION['subject']=$_POST['subject'];

if(!empty($_POST['class']))
    $_SESSION['class']=$_POST['class']; 
    
if(empty($_SESSION['subject']) || empty($_SESSION['class'])){
    echo "You have not selected a subject or a class.";
    echo "<a href='selectClassForMarks.php'> Go Back </a>";
}
else{

    echo "<h1 align='center'> List of student of class " . $_SESSION['class'] .", for subject: " . $_SESSION['subject'] . "</h1>" ;

    echo "<h2 align='center'> Select a student: </h2>";


    $studentList=$teacher->getStudents($_SESSION['class']);

    if(empty($studentList))
        echo "This class has no students. <a href='selectClassForMarks.php'> Go Back </a>";
    else{
        echo "<div class='container-fluid text-left col-md-8'>";
        echo "<table class='table table-striped '>";
        echo "<tr ><th>Surname</th><th>Name</th><th></th></tr>";
        echo $studentList;
        echo "</table></div>";
    }
}
?>


<?php
require_once("defaultFooter.php")
?>