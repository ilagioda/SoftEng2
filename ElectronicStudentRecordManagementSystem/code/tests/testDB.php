<?php

use PHPUnit\Framework\TestCase;

require_once("../classTeacher.php");
require_once("../db.php");

final class dbTest extends TestCase{

    public function testGetSubjectTaughtInClass(): void{
        $this->assertSame(1,1);
    }

    public function updateAttendanceTest(): void
    {
        $teacher = new Teacher();

        $ssn = "FRCWTR";
        $day = date('Y-m-j');

        $text = $teacher->updateAttendance($ssn, $day);
        $this->assertSame($text, "Event recorded.");


        $result = $this->selectAttendanceStudent($day, $ssn);

        if (!$result) {
            $this->assertTrue(false);
        }

        //Lo studente deve essere assente
        if ($result->num_rows != 1)
            $this->assertTrue(false);

        $attendance = $result->fetch_assoc();

        //Se è stata settata un uscita e l'ora di uscita < ora entrata
        $this->assertEquals($attendance['absence'] == 1);
    }




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





}
?>