<?php

require_once("db.php");

class Teacher{

    private $db;
    private $codfisc;

    function __construct(){
        $this->codfisc=$_SESSION['user'];
        $this->db = new dbTeacher();
        
    }

    function getStudents($class){
        return $this->db->getStudentsByClass($class);
        
    }
  
    function submitMark($codStudent, $subject, $date, $hour, $mark) {
        
        $this->db->insertMark($codStudent, $subject, $date, $hour, $mark);
    }


}


?>