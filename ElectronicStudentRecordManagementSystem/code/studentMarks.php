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
  padding-right: 10px;
  padding-left: 10px;

}
</style>


<body>

<?php
echo "<h2> List of student of class " . $class ."</h2>";

echo "<h3> Select a student: </h3>";


$studentList=$teacher->getStudents($class);



echo "<table class=\"students\">";
echo $studentList;
echo "</table>";

?>


<?php
require_once("defaultFooter.php")
?>