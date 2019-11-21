<?php

require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedTeacherNavbar.php";
}
require_once("classTeacher.php");
    $teacher=new Teacher();
    
    $classes =$teacher->getClassesByTeacher();

echo "<div class=text-center>";
echo "<h2>Attendance</h2>";

    if(isset($_GET['class'])){
        //It was requested a class so the list of students of that particular class must be shown
        
        //debug
        echo $_GET['class'];


        // data


        //tabella studenti





    

        
    }else{
    //The class has not yet be chosen so the list of classses must be shown
    echo<<<_LIST
        <ul class="list-group">
_LIST;
    foreach($classes as $class){
        echo<<<_ROW
        <a href="attendance.php?class=$class" class="list-group-item">$class</a>     
_ROW;
    }
    echo<<<_ENDLIST
    </ul>
_ENDLIST;
    }


echo "</div>";

require_once("defaultFooter.php");
?>


