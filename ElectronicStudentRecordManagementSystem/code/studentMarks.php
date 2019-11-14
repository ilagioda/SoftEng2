<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

require_once("classTeacher.php");

$teacher=new Teacher();

if(!empty($_POST['student']))
    $_SESSION['student']=$_POST['student'];

if (!empty($_POST["date"]) && !empty($_POST["hour"]) && !empty($_POST["grade"]) && !empty($_SESSION['student']) && !empty($_SESSION['subject'])) {
    //echo $_POST["date"] . " " . $_POST["hour"] . " " . $_POST["grade"] . " " . $_SESSION['student'] . " " . $_SESSION['subject'];
    echo $teacher->submitMark($_SESSION['student'], $_SESSION['subject'], $_POST["date"], $_POST["hour"], $_POST["grade"]);
}

if (empty($_SESSION['student']) || empty($_SESSION['subject'])) {
    echo "You have not selected the requested parameters.";
    echo "<a href='selectClassForMarks.php'> Go Back </a>";
}
else{

    echo "<h2> List of grades of " . $teacher->getStudentByCod($_SESSION['student']) .", for subject: " . $_SESSION['subject'] . "</h2>" ;


    $markList=$teacher->getStudentMarks($_SESSION['student'], $_SESSION['subject']);

    if(empty($markList))
        echo "This student has no grades for this subject.";
    else{
        echo "<table>";
        echo "<tr><th>Date</th><th>Hour</th><th>Grade</th></tr>";
        echo $markList;
        echo "</table>";
    }
        echo "<br><br>";
        echo "Add grade:";
        echo "<form action='studentMarks.php' method='post'>";
        echo "<input type='date' name='date'>";
        echo "<input type='num' name='hour' value='Hour'>";
        echo "<input type='num' name='grade' value='Grade'>";
        echo "<input type='submit' value='submit'>";
        echo "</form>";
}

require_once("defaultFooter.php")
?>