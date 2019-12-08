<?php

use PHPUnit\Framework\TestCase;

require_once("../classTeacher.php");
require_once("../db.php");

final class dbTest extends TestCase{


    ///////////////////////////////////////////////////////////

    public function testViewStudentMarks() {

        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();


        //Studente non presente nel db o senza voti
        $CodFisc = "absent";
        $subject = "Italian";

        $result = $db->viewStudentMarks($CodFisc, $subject);

        $this->assertSame($result, null);


        //Studente presente e con voti (si assume che si testi dopo aver caricato il db)
        $CodFisc = "FRCWTR";

        $result = $db->ViewStudentMarks($CodFisc, $subject);

        $marks=array();
        array_push($marks,"2019-10-15,9,3");
        array_push($marks,"2019-10-14,9/10,2");
        array_push($marks,"2019-10-10,7+,1");

        $this->assertSame($result, $marks);


        //Errore nella query
        $result = $db->viewStudentMarks($subject, $CodFisc);
        $this->assertSame($result,null);

    }

    // public function testrecordEarlyExit(){
    //     $_SESSION['role'] = "teacher";
    //     $_SESSION['user'] = "test";
    //     $db = new dbTeacher();

    //     $day='wrong';
    //     $ssn='wrong';
    //     $hour='wrong';

    //     //Errore nella query
    //     $result=$db->recordEarlyExit($day, $ssn, $hour);
    //     $this->assertSame($result,false);

    // }


    public function testCheckAbsenceEarlyExitLateEntrance(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $ssn="FRCWTR";
        $day="2019-12-06";

        $array=array();


        //No entry in db (empty array)

        $array[0]=null;
        $array[1]=null;
        $array[2]=null;
        $array[3]=null;
        $array[4]=null;

        $result=$db->checkAbsenceEarlyExitLateEntrance($ssn, $day);
        $this->assertSame($result, $array);


        //Entry in db
        $day="2019-09-20";
        $array[0]=$day;
        $array[1]=$ssn;
        $array[2]=1;
        $array[3]=1;
        $array[4]=5;

        $result=$db->checkAbsenceEarlyExitLateEntrance($ssn, $day);
        $this->assertSame($result, $array);

        //Exception thrown (false is returned)

    }

    public function testUpdateAttendance(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $ssn="FRCWTR";
        $day="2019-12-08";

        //New entry -> the student must be absent after the insert
        $result=$db->updateAttendance($ssn, $day);
        $this->assertSame($result, true);

        $result=$db->selectAttendanceStudent($day, $ssn);

        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows,1);

        $array=$result->fetch_assoc();
        $this->assertSame($array['absence'], '1');


        //Entry is present without late or early -> entry must be deleted after the update
        $result=$db->updateAttendance($ssn, $day);
        $this->assertSame($result, true);

        $result=$db->selectAttendanceStudent($day, $ssn);

        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows,0);


        //Entry is present with both late and early -> exception thrown

        $day="2020-01-12";
        $result=$db->updateAttendance($ssn, $day);
        $this->assertSame($result, false);

        $result=$db->selectAttendanceStudent($day, $ssn);

        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows,1);

        $array=$result->fetch_assoc();
        $this->assertSame($array['absence'], '1');
        $this->assertSame($array['lateEntry'], '2');
        $this->assertSame($array['earlyExit'], '4');


        //Entry is asbent only with late entry -> exception thrown
        $day="2019-12-03";

        $result=$db->updateAttendance($ssn, $day);
        $this->assertSame($result, false);

        $result=$db->selectAttendanceStudent($day, $ssn);

        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows,1);
        $array=$result->fetch_assoc();

        $this->assertSame($array['absence'], '0');
        $this->assertSame($array['lateEntry'], '3');
        $this->assertSame($array['earlyExit'], '0');
        
    } 

    public function testGetStudentsByClass2(){

        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        //Existing class
        $class="1B";
        $result=$db->getStudentsByClass2($class);

        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 5);

        //Unexisting class

        $class="wrong";
        $result=$db->getStudentsByClass2($class);

        if($result)
            $this->assertTrue(false);
        
    }


    public function testGetAssignments(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //Existing teacher (with subjects and assignments)
        $result=$db->getAssignments($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);

        //Non existing teacher
        $result=$db->getAssignments("iAmNotHere");
        if($result)
            $this->assertTrue(false);

    }

    public function testInsertNewAssignments(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //New assignment, all correct
        $day="2019-12-05";
        $class="1A";
        $subject="Philosophy";
        $assignments="Some Assignments";

        $result=$db->insertNewAssignments($day, $class, $_SESSION['user'], $subject, $assignments);
        if($result)
            $this->assertTrue(false);

        $result=$db->getAssignments($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], $class . "," . $subject . "," . $day . "," . $assignments);

        //Existing assignment -> insert failure

        $result=$db->insertNewAssignments($day, $class, $_SESSION['user'], $subject, $assignments);
        $this->assertSame($result, -1);


    }

    public function testUpdateAssignments(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Existing assignment
        $day="2019-12-05";
        $class="1A";
        $subject="Philosophy";
        $assignments="Some NEW Assignments";

        $db->updateAssignments($day, $class, $subject, $assignments);

        $result=$db->getAssignments($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], $class . "," . $subject . "," . $day . "," . $assignments);


        //Non existing assignment

        $day2="2019-12-03";
        $assignmnents="Some WRONG Assignments";
        $db->updateAssignments($day, $class, $subject, $assignments);

        $result=$db->getAssignments($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], $class . "," . $subject . "," . $day . "," . $assignments);

        
    }

    public function testDeleteAssignments(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        $day="2019-12-05";
        $class="1A";
        $subject="Philosophy";

        $db->deleteAssignments($day, $subject, $class);
        
        $result=$db->getAssignments($_SESSION['user']);
        $this->assertSame($result, null);
        
    }

    public function testGetLecturesByTeacher(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "TEA";
        $db = new dbTeacher();

        //Existing lectures
        $result=$db->getLecturesByTeacher($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0],"1A,History,2019-11-11,1,arg1");
        $this->assertSame($result[1],"1A,History,2019-11-05,1,arg0");


        //Non existing lectures
        $result=$db->getLecturesByTeacher("wrong");
        $this->assertSame($result, null);
    }

    public function testGetSubjectsByTeacherAndClass2(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Existing subject
        $class="1A";
    
        $result=$db->getSubjectsByTeacherAndClass2( $_SESSION['user'], $class);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0],"Philosophy");

        //Non existing subject or teacher
        $class="wrong";
        $result=$db->getSubjectsByTeacherAndClass2("Wrong", $class);
        $this->assertSame($result, null);
    }

    public function testgetClassesByTeacher2(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Existing class

        $result=$db->getClassesByTeacher2($_SESSION['user']);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0],"1A");

        //Non existing class

        $result=$db->getClassesByTeacher2("wrong");
        $this->assertSame($result, null);

    }

    public function testget
    



    





}
?>