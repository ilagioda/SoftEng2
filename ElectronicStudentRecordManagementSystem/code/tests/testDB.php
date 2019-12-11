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

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
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

    public function testInsertCommunication(){

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        $res = $db->queryForTesting("SELECT MAX(ID) as oldID FROM Announcements");

        if (!$res) $this->fail();

        $res = $res->fetch_assoc();

        $newID = $res['oldID'] + 1;

        $this->assertTrue($db->insertCommunication("title","text"));

        $q=$db->queryForTesting("SELECT COUNT(*) as n FROM Announcements WHERE ID=$newID");

        if (!$q) $this->fail();

        $q = $q->fetch_assoc();

        if($q['n']!=1) $this->fail();

        // clean DB
        $db->queryForTesting("DELETE FROM Announcements WHERE ID=$newID");
    }

    public function testReadAllClassCompositions(){
        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        $ret = $db->readAllClassCompositions();
        $this->assertNotEmpty($ret);
        $this->assertCount(3,$ret);
        $this->assertContains("1A",$ret);
        $this->assertContains("1B",$ret);
        $this->assertContains("1C",$ret);

        // remove entries and see if the return value is an empty array
        $db->queryForTesting("DELETE FROM ProposedClasses");

        $this->assertEmpty($db->readAllClassCompositions());

        // restore values
        $db->queryForTesting("INSERT INTO `ProposedClasses` (`classID`, `codFisc`) VALUES
                                                                ('1A', 'CLDFLCM'),
                                                                ('1B', 'MRC'),
                                                                ('1C', 'ANDR'),
                                                                ('1C', 'SMN');");

    }

    public function testUpdateStudentsClass(){
        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        $class1A = $db->readClassCompositions("1A"); // already tested

        $studentData = $class1A[0];

        // should set the class of the student
        $db->updateStudentsClass(array($studentData));

        $ret = $db->queryForTesting("SELECT classID FROM Students WHERE codFisc='CLDFLCM'");
        $this->assertNotFalse($ret);
        $this->assertEquals("1A",$ret->fetch_assoc()["classID"]); // student should have an assigned class
        $this->assertEquals(0,$db->queryForTesting("SELECT classID FROM ProposedClasses WHERE classID='1A'")->num_rows); // noone from 1A should be in ProposedClasses

        // restore the state of the db
        $db->queryForTesting("INSERT INTO `ProposedClasses` (`classID`, `codFisc`) VALUES ('1A', 'CLDFLCM')");
        $db->queryForTesting("UPDATE Students SET classID='' WHERE codFisc='CLDFLCM'");
    }

    public function testEnrollStudent(){

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        // add a generic child with two parent
        $this->assertTrue($db->enrollStudent("childNameTEST","childSurnameTEST","SSNCHILDTEST","nameP1TEST","surnameP1TEST","SSNP1TEST","EmailP1TEST","nameP2TEST","surnameP2TEST","SSNP2TEST","EmailP2TEST"));

        // add a generic child with one parent
        $this->assertTrue($db->enrollStudent("childNameTEST2P","childSurnameTEST2P","SSNCHILDTEST2P","nameP1TEST1P","surnameP1TEST1P","SSNP1TEST1P","EmailP1TEST1P"));

        // add a student that is already in the DB
        $this->assertFalse($db->enrollStudent("Bob","Silver","BOB","shouldNotBeHere","shouldNotBeHere","shouldNotBeHere","shouldNotBeHere"));

        // add a student with a parent that is already in the DB
        $this->assertTrue($db->enrollStudent("TESTexistingParent","TESTexistingParent","TESTexistingParent","nameP1TEST1P","surnameP1TEST1P","SSNP1TEST1P","EmailP1TEST1P"));

        // check the DB
        $ret = $db->queryForTesting("SELECT COUNT(*) AS OK FROM Students WHERE codFisc='SSNCHILDTEST'");
        $this->assertNotFalse($ret);
        $this->assertEquals(1,$ret->fetch_assoc()["OK"]);

        $ret = $db->queryForTesting("SELECT COUNT(*) AS OK FROM Students WHERE codFisc='SSNCHILDTEST2P'");
        $this->assertNotFalse($ret);
        $this->assertEquals(1,$ret->fetch_assoc()["OK"]);

        $ret = $db->queryForTesting("SELECT COUNT(*) AS OK FROM Students WHERE codFisc='TESTexistingParent'");
        $this->assertNotFalse($ret);
        $this->assertEquals(1,$ret->fetch_assoc()["OK"]);

        // clean the DB
        $db->queryForTesting("DELETE FROM Parents WHERE codFisc LIKE '%TEST%'");
        $db->queryForTesting("DELETE FROM Students WHERE codFisc LIKE '%TEST%'");
    }

    /*PARENT*/

    public function testGetChildClass(){

        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "test";
        $db = new dbParent();

        //student in DB, expected class ID
        $ssn="FRCWTR";

        $result=$db->getChildClass($ssn);
        $this->assertSame ($result,"1A");

        //The following part of the test is disabled because the function uses the die() function and the tests would stop here.

//        //student not in DB, expected null
//        $ssn='wrong';
//
//        $result=$db->getChildClass($ssn);
//        $this->assertSame ($result,null);


    }



    /* TEACHER */
    public function testViewStudentMarks() {

        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();


        //Student not in DB or without markls, result should be null
        $CodFisc = "absent";
        $subject = "Italian";

        $result = $db->viewStudentMarks($CodFisc, $subject);

        $this->assertSame($result, null);

        //Student in db and with marks 
        //(assuming tests are done after loading createTestDB.sql, as for all subsequent tests)
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

    public function testrecordEarlyExit(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $day='2019-12-11';
        $ssn='FRCWTR';
        $hour='4';

        //Add early exit on new entry (the student was present before, expected true)
        $result=$db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result,true);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '0');
        $this->assertSame($row['earlyExit'], '4');
        
        //restoringDB
        $db->queryForTesting("DELETE FROM attendance WHERE codFisc='$ssn' AND date='$day'");


        //Add early exit on a tuple with late entrance and present student (expected true)
        $day="2019-11-27";
        $result=$db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result,true);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '2');
        $this->assertSame($row['earlyExit'], '4');

        //restoring DB
        $db->queryForTesting("UPDATE attendance SET absence='0', earlyExit='0' WHERE codFisc='$ssn' AND date='$day'");

        
        //Add early exit on a tuple with absent student and no early exit (exception should be thrown -> expected false)
        $day='2019-12-13';
        $db->queryForTesting("INSERT INTO `attendance` (`date`, `codFisc`, `absence`, `lateEntry`, `earlyExit`) VALUES ('2019-12-13', 'FRCWTR', '1', '0', '0')");

        $result=$db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result,false);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '0');
        $this->assertSame($row['earlyExit'], '0');


        //lateEntry > earlyExit (exception should be thrown -> expected false)
        $db->queryForTesting("UPDATE attendance SET absence='1', lateEntry='5' WHERE codFisc='$ssn' AND date='$day'");

        $result=$db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result,false);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '5');
        $this->assertSame($row['earlyExit'], '0');

        //restoring db
        $db->queryForTesting("DELETE FROM attendance WHERE codFisc='$ssn' AND date='$day'");


        //deleting the early extit with hour=0 -> student should become present (expected true, and removal of tuple from table) 
        $hour=0;
        $day='2019-10-03';

        $result=$db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result,true);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 0);

        //restoring DB
        $db->queryForTesting("INSERT INTO `attendance` (`date`, `codFisc`, `absence`, `lateEntry`, `earlyExit`) VALUES ('$day', 'FRCWTR', '1', '0', '2')");
    }

    public function testRecordLateEntrance(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        //Tuple not present -> exception should be thrown (expected false)
        $day='2019-12-11';
        $ssn='FRCWTR';
        $hour='3';

        $result=$db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, false);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 0);

        //Tuple present -> expected true
        $day='2019-11-27';
        $result=$db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '0');
        $this->assertSame($row['lateEntry'], '3');
        $this->assertSame($row['earlyExit'], '0');

        //restoring db
        $db->queryForTesting("UPDATE attendance SET lateEntry='2' WHERE codFisc='$ssn' AND date='$day'");


        //Adding late entrance with hour>early exit (with no late entrance inserted) -> expected false (exception)
        $day='2019-10-03';
        $result=$db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, false);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '0');
        $this->assertSame($row['earlyExit'], '2');
        

        //Same as before but with hour<early exit -> true expected
        $hour=1;
        $day='2019-10-03';
        $result=$db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '1');
        $this->assertSame($row['earlyExit'], '2');

        //restoring db
        $db->queryForTesting("UPDATE attendance SET lateEntry='0' WHERE codFisc='$ssn' AND date='$day'");

        //Adding late entrance to absent student -> expected true, now the student should be present
        $day='2019-10-09';
        $result=$db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '0');
        $this->assertSame($row['lateEntry'], '1');
        $this->assertSame($row['earlyExit'], '0');

        //restoring db
        $db->queryForTesting("UPDATE attendance SET absence='1', lateEntry='0' WHERE codFisc='$ssn' AND date='$day'");


        //Adding late entrance=0 to an absent student -> true expected (with deleting of tuple)
        $hour=0;
        $result=$db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result=$db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 0);
        $row = $result->fetch_assoc();

        //restoring db
        $db->queryForTesting("INSERT INTO `attendance` (`date`, `codFisc`, `absence`, `lateEntry`, `earlyExit`) VALUES ('$day', 'FRCWTR', '1', '0', '0')");


    }


    public function testCheckAbsenceEarlyExitLateEntrance(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $ssn="FRCWTR";
        $day="2019-12-06";
        $array=array();


        //No entry in db (should return an empty array)

        $array[0]=null;
        $array[1]=null;
        $array[2]=null;
        $array[3]=null;
        $array[4]=null;

        $result=$db->checkAbsenceEarlyExitLateEntrance($ssn, $day);
        $this->assertSame($result, $array);


        //Entry in db (should return an array with results)
        $day="2019-09-20";
        $array[0]=$day;
        $array[1]=$ssn;
        $array[2]=1;
        $array[3]=1;
        $array[4]=5;

        $result=$db->checkAbsenceEarlyExitLateEntrance($ssn, $day);
        $this->assertSame($result, $array);
    }

    public function testUpdateAttendance(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $ssn="FRCWTR";
        $day="2019-12-08";

        //New entry -> the student must be absent after the insert (result should be true)
        $result=$db->updateAttendance($ssn, $day);
        $this->assertSame($result, true);

        //there should be one row with absence set to 1
        $result=$db->selectAttendanceStudent($day, $ssn);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows,1);

        $array=$result->fetch_assoc();
        $this->assertSame($array['absence'], '1');


        //Entry is present without late or early -> entry must be deleted after the update
        $result=$db->updateAttendance($ssn, $day);
        $this->assertSame($result, true);

        //there should be no rows beecause the entry should have been deleted
        $result=$db->selectAttendanceStudent($day, $ssn);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows,0);


        //Entry is present with both late and early -> exception thrown (result should be false)
        $day="2020-01-12";
        $result=$db->updateAttendance($ssn, $day);
        $this->assertSame($result, false);

        //No changes should be made to the DB
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

        //No changes should be made to the DB
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

        //Existing class (should return 5 entries, the data cannot be tested because the query is not ordered)
        $class="1B";
        $result=$db->getStudentsByClass2($class);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 5);

        //Unexisting class (return should be null)
        $class="wrong";
        $result=$db->getStudentsByClass2($class);
        $this->assertSame($result, null);
        
    }


    public function testGetAssignments(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //Existing teacher with subjects and assignments (should return one assignment)
        $result=$db->getAssignments($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        //testing the value returned
        $this->assertSame($result[0], "1A,Physics,2019-11-27,Vectors");

        //Non existing teacher (result should be null)
        $result=$db->getAssignments("iAmNotHere");
        $this->assertSame($result, null);

    }

    public function testInsertNewAssignments(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //New assignment, all correct (should return one assignment)
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

        //Existing assignment -> insert failure (should return -1)
        $result=$db->insertNewAssignments($day, $class, $_SESSION['user'], $subject, $assignments);
        $this->assertSame($result, -1);


    }

    public function testUpdateAssignments(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Existing assignment (should return 1 assignment, modified with the new text)
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


        //Non existing assignment (should return the same assignment from before, not modified)
        $day2="2019-12-03";
        $assignments2="Some WRONG Assignments";
        $db->updateAssignments($day2, $class, $subject, $assignments2);

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
        //Should return no assignment for the current teacher
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


        //Non existing lectures (should return null)
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

        //Non existing subject or teacher (should return null)
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

        //Non existing class (should return null)
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

        //Entry already in db -> insert fail (should return -1 and nothing should change in the DB)
        $topics="Some WRONG topics";
        $result=$db->insertDailyLesson($date, $hour, $class, $teacher, $subject, $topics);
        $this->assertSame($result, -1);

        $result=$db->getLecturesByTeacher($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0],"1A,Philosophy,2019-12-06,1,Some nice topics");
    }

    public function testUpdateDailyLesson(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Entry in DB
        $date="2019-12-06";
        $hour="1";
        $class="1A";
        $teacher="FLCM";
        $subject="Philosophy";
        $topics="Some updated topics";
        //the function always return null, both in case of success and in case of failure
        $result=$db->updateDailyLesson($date, $hour, $class, $subject, $topics);
        $this->assertSame($result, null);

        $result=$db->getLecturesByTeacher($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0],"1A,Philosophy,2019-12-06,1,Some updated topics");

        //Entry not in DB (nothing should change in the DB)
        $date2="2019-12-17";
        $topics2="Some WRONG topics";
        $result=$db->updateDailyLesson($date, $hour, $class, $subject, $topics);
        $this->assertSame($result, null);

        $result=$db->getLecturesByTeacher($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0],"1A,Philosophy,2019-12-06,1,Some updated topics");
    }


    public function testDeleteDailyLesson(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Entry not in DB -> nothing should change
        $date="2019-12-07";
        $hour="1";
        $class="1A";

        //this function always returns null
        $result=$db->deleteDailyLesson($date, $hour, $class);
        $this->assertSame($result, null);

        $result=$db->getLecturesByTeacher($_SESSION['user']);
        if(!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0],"1A,Philosophy,2019-12-06,1,Some updated topics");


        //Entry in DB -> should delete it (getLectures should return null)
        $date="2019-12-06";

        $result=$db->deleteDailyLesson($date, $hour, $class);
        $this->assertSame($result, null);

        $result=$db->getLecturesByTeacher($_SESSION['user']);
        $this->assertSame($result, null);
    }

    public function testGetStudentsName(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Student in DB (should return "Surname Name" of the student)
        $codfisc="MRC";
        $result=$db->getStudentsName($codfisc);
        $this->assertSame($result, "Cipriano Marco");

        //Student not in DB (Should return "")
        $codfisc="WRNG";
        $result=$db->getStudentsName($codfisc);
        $this->assertSame($result, "");
    }

    public function testGetGradesByTeacher(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //Teacher in DB with marks inserted (there is 1 mark for that teacher);
        $codfisc="GNV";
        $result=$db->getGradesByTeacher($codfisc);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");

        //Teacher not in DB or without marks (should return null);
        $codfisc="WRNG"; 
        $result=$db->getGradesByTeacher($codfisc);
        $this->assertSame($result, null);
    }

    public function testInsertGrade(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //New entry->insert successful (should return null)
        $date="2019-12-12";
        $hour=2;
        $student="FRCWTR";
        $subject="Physics";
        $grade="5/6";
        $codfisc="GNV";

        $result=$db->insertGrade($date, $hour, $student, $subject, $grade);
        $this->assertSame($result, null);
        //controls on data
        $result=$db->getGradesByTeacher($codfisc);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-12-12,2,5/6");
        $this->assertSame($result[1], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");
        
        //Existing entry (should return -1 and make no changes to DB)
        $result=$db->insertGrade($date, $hour, $student, $subject, $grade);
        $this->assertSame($result, -1);

        $result=$db->getGradesByTeacher($codfisc);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-12-12,2,5/6");
        $this->assertSame($result[1], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");
    }

    public function testUpdateMark(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        $codFiscTeacher="GNV";

        //Mark is present -> update should be successful
        $date="2019-12-12";
        $hour=2;
        $codFisc="FRCWTR";
        $subject="Physics";
        $grade=10;
        
        $result=$db->updateMark($codFisc, $subject, $date, $hour, $grade);
        //this function always return null
        $this->assertSame($result, null);
        //data controls
        $result=$db->getGradesByTeacher($codFiscTeacher);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-12-12,2,10");
        $this->assertSame($result[1], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");

        //Mark is not present -> update should fail
        $date="wrong";
        $result=$db->updateMark($codFisc, $subject, $date, $hour, $grade);
        $this->assertSame($result, null);

        $result=$db->getGradesByTeacher($codFiscTeacher);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-12-12,2,10");
        $this->assertSame($result[1], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");        
    }

    public function testDeleteMark(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //Existing mark (this function always returns 0)
        $date="2019-12-12";
        $hour=2;
        $codFisc="FRCWTR";
        $subject="Physics";
        $result=$db->deleteMark($codFisc, $date, $hour, $subject);
        $this->assertSame($result, 0);
        //data control
        $codfiscTeacher="GNV";
        $result=$db->getGradesByTeacher($codfiscTeacher);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");

        //Non existing mark (no changes to the DB should occur)
        $result=$db->deleteMark($codFisc, $date, $hour, $subject);
        $this->assertSame($result, 0);
        //data control
        $codfiscTeacher="GNV";
        $result=$db->getGradesByTeacher($codfiscTeacher);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");
    }
    



    





}
