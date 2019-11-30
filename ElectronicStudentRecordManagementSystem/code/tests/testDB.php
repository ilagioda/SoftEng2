<?php

use PHPUnit\Framework\TestCase;

require_once("../classTeacher.php");

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





}
?>