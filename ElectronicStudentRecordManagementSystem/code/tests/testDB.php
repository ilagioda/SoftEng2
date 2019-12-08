<?php

use PHPUnit\Framework\TestCase;

require_once("../classTeacher.php");
require_once("../db.php");

final class dbTest extends TestCase{

    // public function testGetSubjectTaughtInClass(): void{
    //     $this->assertSame(1,1);
    // }

    // public function updateAttendanceTest(): void
    // {
    //     $teacher = new Teacher();

    //     $ssn = "FRCWTR";
    //     $day = date('Y-m-j');

    //     $text = $teacher->updateAttendance($ssn, $day);
    //     $this->assertSame($text, "Event recorded.");


    //     $result = $this->selectAttendanceStudent($day, $ssn);

    //     if (!$result) {
    //         $this->assertTrue(false);
    //     }

    //     //Lo studente deve essere assente
    //     if ($result->num_rows != 1)
    //         $this->assertTrue(false);

    //     $attendance = $result->fetch_assoc();

    //     //Se è stata settata un uscita e l'ora di uscita < ora entrata
    //     $this->assertEquals($attendance['absence'] == 1);
    // }




    ///////////////////////////////////////////////////////////

    public function testViewStudentMarks(): void {

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

        //Entry is present with no late or early -> should be absent

        $day="2019-12-09";
        $db->recordEarlyExitHavingAlreadyLateEntryQUERY($day, 0, $ssn, 0);
        $result=$db->updateAttendance($ssn, $day);
        $this->assertSame($result, true);

        $result=$db->selectAttendanceStudent($day, $ssn);

        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows,1);
        
        $array=$result->fetch_assoc();
        $this->assertSame($array['absence'], '1');
        $this->assertSame($array['lateEntry'], '0');
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




    





}
?>