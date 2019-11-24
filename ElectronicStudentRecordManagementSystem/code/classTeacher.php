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
        return $this->db->getAssignments();
    }

    function getStudents2($class)
    {
        return $this->db->getStudentsByClass2($class);
    }

    function updateAttendance($ssn, $day)
    {
        return $this->db->updateAttendance($ssn, $day);
    }
}
