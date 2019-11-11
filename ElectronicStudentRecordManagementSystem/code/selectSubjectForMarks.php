<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

$_SESSION['user']="ABCD97";
$_SESSION['role']="teacher";

require_once("classTeacher.php");

$teacher=new Teacher();

$_SESSION['class']=$_POST['class'];

//CHECK SU MATERIE DEL PROFESSORE

?>



<body>

<h2> Select the subject for the class: 


<?php

echo $_SESSION['class'] . "</h2>";

$classList=$teacher->getSubjectByClass($_SESSION['class']);


echo $classList;


?>


<?php
require_once("defaultFooter.php")
?>