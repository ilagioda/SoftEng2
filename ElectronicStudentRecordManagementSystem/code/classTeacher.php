<?php

require_once("db.php");

class Teacher{
    
    function __construct(){
        $codfisc=$_SESSION['user'];
        $db = new dbTeacher;
    }

    function getStudents($subject){
        
    }
  
    function submitMark($codStudent, $subject, $date, $hour, $mark) {
            $db->insertMark($codStudent, $subject, $date, $hour, $mark);
    }


}


?>