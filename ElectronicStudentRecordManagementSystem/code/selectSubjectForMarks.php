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

    echo "<h2> Select the subject for the class: ";
    echo $_SESSION['class'] . "</h2>";

    $subjects=$teacher->getSubjectByClass($_SESSION['class']);


    if(empty($subjects))
        echo "You have no subject for this class. <a href='selectClassForMarks.php'> Go Back </a>";
    else
        echo $subjects;


}
?>


<?php
require_once("defaultFooter.php")
?>