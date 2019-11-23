<?php

if (!isset($_SESSION))
    session_start();
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    if (isset($_POST["event"])) {

        if ( ($_POST["event"] == "presence") && isset($_SESSION['students'])&&isset($_POST['i'])) {
            $students =$_SESSION['students'];
            $index =$_POST['i'];
            $tuple = explode(",",$students[$index]);
            $ssn = $tuple[2];
            /* echo $ssn; */
             
            require_once("classTeacher.php");

            $teacher = new Teacher();

            $day = date('Y-m-j');

            $text = $teacher->updateAttendance($ssn, $day);

            echo $text;

            /* if($text){
                echo "The student absence has been recorded";
            }else{
                echo "Something has gone wrong";
            } */
        }
    }
} else {
    die("You are not supposed to be here.");
}
