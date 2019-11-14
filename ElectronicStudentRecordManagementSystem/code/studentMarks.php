<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

$_SESSION['user']="ABCD97";
$_SESSION['role']="teacher";

require_once("classTeacher.php");

$teacher=new Teacher();

if(!empty($_POST['student']))
    $_SESSION['student']=$_POST['student'];

if (!empty($_POST["date"]) && !empty($_POST["hour"]) && !empty($_POST["grade"]) && !empty($_SESSION['student']) && !empty($_SESSION['subject'])) {
    $teacher->submitMark($_SESSION['student'], $_SESSION['subject'], $_POST["date"], $_POST["hour"], $_POST["grade"]);
}


?>


<?php

echo "<h2> List of Marks of " . $_SESSION['student'] .", for subject: " . $_SESSION['subject'] . "</h2>" ;


$markList=$teacher->getStudentMarks($_SESSION['student'], $_SESSION['subject']);


echo "<table>";
echo $markList;
echo "</table>";

?>

<br><br>
Add Mark:
<form action='studentMarks.php' method='post'>
<input type='date' name='date'>
<input type='num' name='hour' value="Hour">
<input type='num' name='grade' value="Grade">
<input type="submit" value="submit">
</form>





<?php
require_once("defaultFooter.php")
?>