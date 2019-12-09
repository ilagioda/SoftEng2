<?php

use PHPUnit\Framework\TestCase;

require_once("../classTeacher.php");
require_once("../db.php");

final class dbTest extends TestCase{


    ///////////////////////////////////////////////////////////

     /* DB */
     public function testGetSubjectTaughtInClass(){
        $db = new db();

        try{
            $db->getSubjectTaughtInClass("6A");
            // should throw an exception
            $this->fail("Accepted 6A as a valid class");
        } catch (Exception $e){}

        try{
            $db->getSubjectTaughtInClass("AA");
            // should throw an exception
            $this->fail("Accepted AA as a valid class");
        } catch (Exception $e){}

        try{
            $db->getSubjectTaughtInClass("0B");
            // should throw an exception
            $this->fail("Accepted 0A as a valid class");
        }catch (Exception $e){}

        $subjects = $db->getSubjectTaughtInClass("1A"); // this should work

        $this->assertNotNull($subjects);
        $this->assertTrue(in_array("Maths",$subjects));
        $this->assertTrue(in_array("Biology and Chemistry",$subjects));
        $this->assertTrue(in_array("Latin",$subjects));
        $this->assertTrue(in_array("Physics",$subjects));
        $this->assertTrue(in_array("English",$subjects));
        $this->assertTrue(in_array("History",$subjects));
        $this->assertTrue(in_array("Italian",$subjects));
        $this->assertTrue(in_array("Philosophy",$subjects));
        $this->assertTrue(in_array("Geography",$subjects));        
    }

    public function testGetHashedPassword(){
        $db = new db();

        // non existing user

        $this->assertEmpty($db->getHashedPassword("nonExistentUser"));

        // sysadmin

        $ret = $db->getHashedPassword("FLC");   #sysadmin

        $this->assertEquals("FLC",$ret["user"]);
        $this->assertEquals("admin",$ret["role"]);
        $this->assertTrue(password_verify("ciao",$ret["hashedPassword"])); 
        $this->assertEquals(1,$ret["sysAdmin"]);

        // admin
        $ret = $db->getHashedPassword("ADM");   #admin

        $this->assertEquals("ADM",$ret["user"]);
        $this->assertEquals("admin",$ret["role"]);
        $this->assertTrue(password_verify("ciao",$ret["hashedPassword"])); 
        $this->assertEquals(0,$ret["sysAdmin"]);

        // parent with first login = 0
        $ret = $db->getHashedPassword("parent@parent.it");

        $this->assertEquals("parent@parent.it",$ret["user"]);
        $this->assertEquals("parent",$ret["role"]);
        $this->assertTrue(password_verify("ciao",$ret["hashedPassword"])); 
        $this->assertEquals(0,$ret["firstLogin"]);

        // parent with first login = 1
        $ret = $db->getHashedPassword("cla_9_6@hotmail.it");

        $this->assertEquals("cla_9_6@hotmail.it",$ret["user"]);
        $this->assertEquals("parent",$ret["role"]);
        $this->assertTrue(password_verify("ciao",$ret["hashedPassword"])); 
        $this->assertEquals(1,$ret["firstLogin"]);
           
        // teacher 
        $ret = $db->getHashedPassword("TEA");

        $this->assertEquals("TEA",$ret["user"]);
        $this->assertEquals("teacher",$ret["role"]);
        $this->assertTrue(password_verify("ciao",$ret["hashedPassword"])); 
        $this->assertEquals(0,$ret["principal"]);

        // principal
        $ret = $db->getHashedPassword("FLCM");

        $this->assertEquals("FLCM",$ret["user"]);
        $this->assertEquals("teacher",$ret["role"]);
        $this->assertTrue(password_verify("ciao",$ret["hashedPassword"])); 
        $this->assertEquals(1,$ret["principal"]);
    }

    /* dbAdmin */

    public function testReadClassCompositions(){

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();


        // should return an empty array
        $this->assertEmpty($db->readClassCompositions(6.8));
        
        // this should work
        $res = $db->readClassCompositions("1B");
        $this->assertEquals(1,count($res));
        $this->assertEquals("MRC",$res[0][0]); #codFisc
        $this->assertEquals("Marco",$res[0][1]); #name
        $this->assertEquals("Cipriano",$res[0][2]); #surname
        $this->assertEquals("1B",$res[0][3]); #classID

    }

    public function testInsertOfficialAccount(){
        $db = new dbAdmin();

        // add admins and teachers with privileges or not
        $this->assertTrue($db->insertOfficialAccount("Teachers","TestA~","a","a","a"));
        $this->assertFalse($db->insertOfficialAccount("Teachers","TestB~","a","a","a",1)); // just one principal
        $this->assertTrue($db->insertOfficialAccount("Admins","TestC~","a","a","a"));
        $this->assertTrue($db->insertOfficialAccount("Admins","TestD~","a","a","a",1));

        // check if the insertion was good
        
        // Teacher
        $ret = $db->getHashedPassword("TestA~"); 
        $this->assertEquals("TestA~",$ret["user"]);
        $this->assertEquals("teacher",$ret["role"]);
        $this->assertEquals("a",$ret["hashedPassword"]); 
        $this->assertEquals(0,$ret["principal"]);

        // principal => should not have been inserted
        $this->assertEmpty($db->getHashedPassword("TestB~"));

        // admin
        $ret = $db->getHashedPassword("TestC~");
        $this->assertEquals("TestC~",$ret["user"]);
        $this->assertEquals("admin",$ret["role"]);
        $this->assertEquals("a",$ret["hashedPassword"]); 
        $this->assertEquals(0,$ret["sysAdmin"]);

        // sysadmin
        $ret = $db->getHashedPassword("TestD~");
        $this->assertEquals("TestD~",$ret["user"]);
        $this->assertEquals("admin",$ret["role"]);
        $this->assertEquals("a",$ret["hashedPassword"]); 
        $this->assertEquals(1,$ret["sysAdmin"]);

        // clean the db
        $db->queryForTesting("DELETE FROM Teachers WHERE codFisc='TestA~'");
        $db->queryForTesting("DELETE FROM Admins WHERE codFisc='TestC~' OR codFisc='TestD~'");

    }

    /* TEACHER */
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

    public function testInsertDailyLesson(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //New entry -> insert
        $date="2019-12-06";
        $hour="1";
        $class="1A";
        $teacher="FLCM";
        $subject="Philosophy";
        $topics="Some nice topics";

        $result=$db->insertDailyLesson($date, $hour, $class, $teacher, $subject, $topics);
        $this->assertSame($result, null);

        $result=$db->getLecturesByTeacher($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0],"1A,Philosophy,2019-12-06,1,Some nice topics");

        //Entry already in db -> insert fail
        $topics="Some WRONG topics";
        $result=$db->insertDailyLesson($date, $hour, $class, $teacher, $subject, $topics);
        $this->assertSame($result, -1);

        $result=$db->getLecturesByTeacher($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0],"1A,Philosophy,2019-12-06,1,Some nice topics");
    }

    public function testDeleteDailyLesson(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //just for replayability, will be implemented later

        $date="2019-12-06";
        $hour="1";
        $class="1A";
        $teacher="FLCM";
        $subject="Philosophy";
        $topics="Some nice topics";
        $this->assertSame(true,true);

        $result=$db->deleteDailyLesson($date, $hour, $class);
    }
    



    





}
