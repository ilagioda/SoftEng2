<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

$_SESSION['user']="ABCD97";
$_SESSION['role']="teacher";

require_once("classTeacher.php");

$teacher=new Teacher();

?>





<h2> Select the class: </h2>




<?php

$classList=$teacher->getClasses();



echo $classList;


?>


<?php
require_once("defaultFooter.php")
?>