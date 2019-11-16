<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

require_once("classTeacher.php");

$teacher=new Teacher();

if(!empty($_POST['class']))
    $_SESSION['class']=$_POST['class'];

if(empty($_SESSION['class'])){
    echo "You have not selected a class.";
    echo "<a href='selectClassForMarks.php'> Go Back </a>";
}
else{

    //CHECK SU MATERIE DEL PROFESSORE

    echo "<h1 align='center'> Select the subject for the class: ";
    echo $_SESSION['class'] . "</h1>";

    $subjects=$teacher->getSubjectByClass($_SESSION['class']);


    if(empty($subjects))
        echo "You have no subject for this class. <a href='selectClassForMarks.php'> Go Back </a>";
    else{
        echo "<div class='container-fluid text-center'>";
        echo "<table class='table-borderless'><tr>";
        echo $subjects;
        echo "</tr></table>";
        echo "</div>";
    }

}
?>


<?php
require_once("defaultFooter.php")
?>