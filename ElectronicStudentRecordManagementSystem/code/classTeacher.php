<?php

require_once("db.php");

class Teacher
{

    private $db;
    private $codfisc;

    function __construct()
    {
        $this->codfisc = $_SESSION['user'];
        $this->db = new dbTeacher();
    }


    function getStudentByCod($codfisc)
    {
        return $this->db->getStudentsName($codfisc);
    }

    function getGrades()
    {
        return $this->db->getGradesByTeacher($this->codfisc);
    }

    function getLectures()
    {
        return $this->db->getLecturesByTeacher($this->codfisc);
    }

    function getClassesByTeacher()
    {
        return $this->db->getClassesByTeacher2($this->codfisc);
    }

    function getSubjectByClassAndTeacher($class)
    {
        return $this->db->getSubjectsByTeacherAndClass2($this->codfisc, $class);
    }

    function getAssignments()
    {
        return $this->db->getAssignments($this->codfisc);
    }

    function getStudents2($class)
    {
        return $this->db->getStudentsByClass2($class);
    }

    function updateAttendance($ssn, $day)
    {
        return $this->db->updateAttendance($ssn, $day);
    }

    function checkAbsenceEarlyExitLateEntrance($ssn, $day)
    {
        return $this->db->checkAbsenceEarlyExitLateEntrance($ssn, $day);
    }

    function recordLateEntrance($day, $ssn, $hour)
    {
        return $this->db->recordLateEntrance($day, $ssn, $hour);
    }

    function recordEarlyExit($day, $ssn, $hour)
    {
        return $this->db->recordEarlyExit($day, $ssn, $hour);
    }

    function viewStudentMarks($ssn, $subject)
    {
        return $this->db->viewStudentMarks($ssn, $subject);
    }
    function recordStudentNote($ssnStudent, $ssnTeacher, $subject, $note, $date, $hour)
    {
        return $this->db->recordStudentNote($ssnStudent, $ssnTeacher, $subject, $note, $date, $hour);
    }
    function removeStudentNote($ssnStudent, $ssnTeacher, $date, $subject)
    {
        return $this->db->removeStudentNote($ssnStudent, $ssnTeacher, $date, $subject);
    }
	
	function getAssignmentsByClassAndDate($class, $date) {
        return $this->db->getAssignmentsByClassAndDate($this->codfisc, $class, $date);

	}

}
