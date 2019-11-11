<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

$_SESSION['user']="ABCD97";
$_SESSION['role']="teacher";

require_once("classTeacher.php");

$teacher=new Teacher();

$_SESSION['student']=$_POST['student'];

?>




<body>

<?php

echo "<h2> List of Marsk of " . $_SESSION['student'] .", for subject: " . $_SESSION['subject'] . "</h2>" ;

echo "<h3> Select a student: </h3>";


$markList=$teacher->getStudentMarks($_SESSION['student'], $_SESSION['subject']);


echo "<table>";
echo $markList;
echo "</table>";

?>

<br><br>
Add Mark:
<form>
<input type='date'>
<input type='num' value="Hour">
<input type='num' value="Grade">

</form>


<?php
require_once("defaultFooter.php")
?>