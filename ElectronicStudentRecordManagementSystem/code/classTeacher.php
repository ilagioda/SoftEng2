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

    function getClasses(){
        return $this->db->getClassesByTeacher($this->codfisc);
    }

    function getSubjectByClass($class){
        return $this->db->getSubjectsByTeacherAndClass($this->codfisc, $class);
    }

    function getStudentMarks($student, $subject){
        return $this->db->getStudentSubjectMarks($student, $subject);
    }
    
    function getLectures() {
		return $this->db->getLecturesByTeacher($this->codfisc);
    }
	
   function getClassesByTeacher(){
        return $this->db->getClassesByTeacher2($this->codfisc);
    }

    function getSubjectByClassAndTeacher($class){
        return $this->db->getSubjectsByTeacherAndClass2($this->codfisc, $class);
    }
}


?>
