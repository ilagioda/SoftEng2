<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

$_SESSION['user']="ABCD97";
$_SESSION['role']="teacher";

require_once("classTeacher.php");

$teacher=new Teacher();
$class="1A";

?>


<style>
table, th, td {
  border: 1px solid black;

}
</style>


<body>

<h2> Lista studenti per la classe A1 </h2>
<br>

<?php
$studentList=$teacher->getStudents($class);

echo "<table class=\"students\">";
echo $studentList;
echo "</table>";

?>


<?php
require_once("defaultFooter.php")
?>