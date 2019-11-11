<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

$_SESSION['user']="ABCD97";
$_SESSION['role']="teacher";

require_once("classTeacher.php");

$teacher=new Teacher();

?>


<style>
table, th, td {
  padding-right: 10px;
  padding-left: 10px;

}
</style>


<body>

<h2> Select the class: </h2>




<?php

$classList=$teacher->getClasses();



echo "<table class=\"students\">";
echo $classList;
echo "</table>";

?>


<?php
require_once("defaultFooter.php")
?>