<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

require_once("classTeacher.php");

$teacher=new Teacher();

if(!empty($_POST['student']))
    $_SESSION['student']=$_POST['student'];

if (!empty($_POST["date"]) && !empty($_POST["hour"]) && !empty($_POST["grade"]) && !empty($_SESSION['student']) && !empty($_SESSION['subject'])) {
    echo $teacher->submitMark($_SESSION['student'], $_SESSION['subject'], $_POST["date"], $_POST["hour"], $_POST["grade"]);
}

if (empty($_SESSION['student']) || empty($_SESSION['subject'])) {
    echo "You have not selected the requested parameters.";
    echo "<a href='selectClassForMarks.php'> Go Back </a>";
}
else{

    echo "<h1 align='center'> List of grades of " . $teacher->getStudentByCod($_SESSION['student']) .", for subject: " . $_SESSION['subject'] . "</h1>" ;


    $markList=$teacher->getStudentMarks($_SESSION['student'], $_SESSION['subject']);

    if(empty($markList))
        echo "This student has no grades for this subject.";
    else{
        echo "<div class='container-fluid text-left col-md-8'>";
        echo "<table class='table table-striped'>";
        echo "<tr><th>Date</th><th>Hour</th><th>Grade</th></tr>";
        echo $markList;
        echo "</table></div>";
    }
        echo "<div class='container-fluid text-left col-md-8'>";
        echo "<br><br>";
        echo "Add grade:";
        echo "<form class='form-inline' action='studentMarks.php' method='post'>";
        echo "<input class='form-control'type='date' name='date'>";
        echo "<input class='form-control' type='num' name='hour' placeholder='Hour (from 1 to 6)'>";
        echo "<input class='form-control' type='num' name='grade' placeholder='Grade (from 0 to 10)'>";
        echo "<input class='form-control btn btn-primary mb-2' type='submit' value='submit'>";
        echo "</form></div>";
}

require_once("defaultFooter.php")
?>