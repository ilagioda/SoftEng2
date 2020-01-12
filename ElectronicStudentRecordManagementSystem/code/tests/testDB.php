<?php

use PHPUnit\Framework\TestCase;

require_once("../classTeacher.php");
require_once("../db.php");



final class dbTest extends TestCase
{


    ///////////////////////////////////////////////////////////

    /* DB */
    public function testGetSubjectTaughtInClass()
    {
        $db = new db();

        try {
            $db->getSubjectTaughtInClass("6A");
            // should throw an exception
            $this->fail("Accepted 6A as a valid class");
        } catch (Exception $e) { }

        try {
            $db->getSubjectTaughtInClass("AA");
            // should throw an exception
            $this->fail("Accepted AA as a valid class");
        } catch (Exception $e) { }

        try {
            $db->getSubjectTaughtInClass("0B");
            // should throw an exception
            $this->fail("Accepted 0A as a valid class");
        } catch (Exception $e) { }

        $subjects = $db->getSubjectTaughtInClass("1A"); // this should work

        $this->assertNotNull($subjects);
        $this->assertTrue(in_array("Maths", $subjects));
        $this->assertTrue(in_array("Biology and Chemistry", $subjects));
        $this->assertTrue(in_array("Latin", $subjects));
        $this->assertTrue(in_array("Physics", $subjects));
        $this->assertTrue(in_array("English", $subjects));
        $this->assertTrue(in_array("History", $subjects));
        $this->assertTrue(in_array("Italian", $subjects));
        $this->assertTrue(in_array("Philosophy", $subjects));
        $this->assertTrue(in_array("Geography", $subjects));
    }

    public function testGetHashedPassword()
    {
        $db = new db();

        // non existing user

        $this->assertEmpty($db->getHashedPassword("nonExistentUser"));

        // sysadmin

        $ret = $db->getHashedPassword("FLC");   #sysadmin

        $this->assertEquals("FLC", $ret["user"]);
        $this->assertEquals("admin", $ret["role"]);
        $this->assertTrue(password_verify("ciao", $ret["hashedPassword"]));
        $this->assertEquals(1, $ret["sysAdmin"]);

        // admin
        $ret = $db->getHashedPassword("ADM");   #admin

        $this->assertEquals("ADM", $ret["user"]);
        $this->assertEquals("admin", $ret["role"]);
        $this->assertTrue(password_verify("ciao", $ret["hashedPassword"]));
        $this->assertEquals(0, $ret["sysAdmin"]);

        // parent with first login = 0
        $ret = $db->getHashedPassword("parent@parent.it");

        $this->assertEquals("parent@parent.it", $ret["user"]);
        $this->assertEquals("parent", $ret["role"]);
        $this->assertTrue(password_verify("ciao", $ret["hashedPassword"]));
        $this->assertEquals(0, $ret["firstLogin"]);

        // parent with first login = 1
        $ret = $db->getHashedPassword("cla_9_6@hotmail.it");

        $this->assertEquals("cla_9_6@hotmail.it", $ret["user"]);
        $this->assertEquals("parent", $ret["role"]);
        $this->assertTrue(password_verify("ciao", $ret["hashedPassword"]));
        $this->assertEquals(1, $ret["firstLogin"]);

        // teacher 
        $ret = $db->getHashedPassword("TEA");

        $this->assertEquals("TEA", $ret["user"]);
        $this->assertEquals("teacher", $ret["role"]);
        $this->assertTrue(password_verify("ciao", $ret["hashedPassword"]));
        $this->assertEquals(0, $ret["principal"]);

        // principal
        $ret = $db->getHashedPassword("FLCM");

        $this->assertEquals("FLCM", $ret["user"]);
        $this->assertEquals("teacher", $ret["role"]);
        $this->assertTrue(password_verify("ciao", $ret["hashedPassword"]));
        $this->assertEquals(1, $ret["principal"]);
    }

    /* dbAdmin */

    public function testReadClassCompositions()
    {

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();


        // should return an empty array
        $this->assertEmpty($db->readClassCompositions(6.8));

        // this should work
        $res = $db->readClassCompositions("1B");
        $this->assertEquals(1, count($res));
        $this->assertEquals("MRC", $res[0][0]); #codFisc
        $this->assertEquals("Marco", $res[0][1]); #name
        $this->assertEquals("Cipriano", $res[0][2]); #surname
        $this->assertEquals("1B", $res[0][3]); #classID

    }

    public function testInsertOfficialAccount()
    {

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        // add admins and teachers with privileges or not
        $this->assertTrue($db->insertOfficialAccount("Teachers", "TestA~", "a", "a", "a"));
        $this->assertFalse($db->insertOfficialAccount("Teachers", "TestB~", "a", "a", "a", 1)); // just one principal
        $this->assertFalse($db->insertOfficialAccount("Teachers", "TestA~", "a", "a", "a")); // just one teacher with a given SSN
        $this->assertTrue($db->insertOfficialAccount("Admins", "TestC~", "a", "a", "a"));
        $this->assertTrue($db->insertOfficialAccount("Admins", "TestD~", "a", "a", "a", 1));

        // check if the insertion was good

        // Teacher
        $ret = $db->getHashedPassword("TestA~");
        $this->assertEquals("TestA~", $ret["user"]);
        $this->assertEquals("teacher", $ret["role"]);
        $this->assertEquals("a", $ret["hashedPassword"]);
        $this->assertEquals(0, $ret["principal"]);

        // principal => should not have been inserted
        $this->assertEmpty($db->getHashedPassword("TestB~"));

        // admin
        $ret = $db->getHashedPassword("TestC~");
        $this->assertEquals("TestC~", $ret["user"]);
        $this->assertEquals("admin", $ret["role"]);
        $this->assertEquals("a", $ret["hashedPassword"]);
        $this->assertEquals(0, $ret["sysAdmin"]);

        // sysadmin
        $ret = $db->getHashedPassword("TestD~");
        $this->assertEquals("TestD~", $ret["user"]);
        $this->assertEquals("admin", $ret["role"]);
        $this->assertEquals("a", $ret["hashedPassword"]);
        $this->assertEquals(1, $ret["sysAdmin"]);

        // clean the db
        $db->queryForTesting("DELETE FROM Teachers WHERE codFisc='TestA~'");
        $db->queryForTesting("DELETE FROM Admins WHERE codFisc='TestC~' OR codFisc='TestD~'");
    }

    public function testInsertCommunication()
    {

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        $res = $db->queryForTesting("SELECT MAX(ID) as oldID FROM Announcements");

        if (!$res) $this->fail();

        $res = $res->fetch_assoc();

        $newID = $res['oldID'] + 1;

        $this->assertTrue($db->insertCommunication("title", "text"));

        $q = $db->queryForTesting("SELECT COUNT(*) as n FROM Announcements WHERE ID=$newID");

        if (!$q) $this->fail();

        $q = $q->fetch_assoc();

        if ($q['n'] != 1) $this->fail();

        // clean DB
        $db->queryForTesting("DELETE FROM Announcements WHERE ID=$newID");
    }

    public function testReadAllClassCompositions()
    {
        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        $ret = $db->readAllClassCompositions();
        $this->assertNotEmpty($ret);
        $this->assertCount(3, $ret);
        $this->assertContains("1A", $ret);
        $this->assertContains("1B", $ret);
        $this->assertContains("1C", $ret);

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

    public function testDeleteTeacher(){
        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        //preparing DB
        $db->queryForTesting("INSERT INTO `teachers` (`codFisc`, `hashedPassword`, `name`, `surname`, `principal`) VALUES ('TEST', 'test', 'TestN', 'TestS', '0')");
        $db->queryForTesting("INSERT INTO `teacherclasssubjecttable` (`codFisc`, `classID`, `subject`) VALUES ('TEST', '1A', 'Physics')");

        //there is another teacher that teaches his subjects -> expected true
        $result=$db->deleteTeacher("TEST");
        $this->assertTrue($result);

        //there is no teacher that teaches his subjects in the same class -> expected false
        $result=$db->deleteTeacher("TEA");
        $this->assertFalse($result);


    }

    public function testDeleteSubjectTeachedInAClassByATeacher(){
        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        //No other teacher has such subject in the class -> expected false
        $result=$db->deleteSubjectTeachedInAClassByATeacher("GNV", "1A", "Physics");
        $this->assertFalse($result);

        //teacher has subject -> expected true
        $result=$db->queryForTesting("INSERT INTO `teacherclasssubjecttable` (`codFisc`, `classID`, `subject`) VALUES ('TEA', '1A', 'Physics')");
        $result=$db->deleteSubjectTeachedInAClassByATeacher("GNV", "1A", "Physics");
        $this->assertTrue($result);
        $result=$db->queryForTesting("SELECT COUNT(*) AS CNT FROM Teacherclasssubjecttable WHERE codFisc='TEST' AND classId='1A' AND subject='Physics'");
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $this->assertSame('0', $row["CNT"]);


    }

    public function testUpdateStudentsClass()
    {
        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        $class1A = $db->readClassCompositions("1A"); // already tested

        $studentData = $class1A[0];

        // should set the class of the student
        $db->updateStudentsClass(array($studentData));

        $ret = $db->queryForTesting("SELECT classID FROM Students WHERE codFisc='CLDFLCM'");
        $this->assertNotFalse($ret);
        $this->assertEquals("1A", $ret->fetch_assoc()["classID"]); // student should have an assigned class
        $this->assertEquals(0, $db->queryForTesting("SELECT classID FROM ProposedClasses WHERE classID='1A'")->num_rows); // noone from 1A should be in ProposedClasses

        // restore the state of the db
        $db->queryForTesting("INSERT INTO `ProposedClasses` (`classID`, `codFisc`) VALUES ('1A', 'CLDFLCM')");
        $db->queryForTesting("UPDATE Students SET classID='' WHERE codFisc='CLDFLCM'");
    }

    public function testEnrollStudent()
    {

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        // clean the DB
        $db->queryForTesting("DELETE FROM Parents WHERE codFisc LIKE '%TEST%'");
        $db->queryForTesting("DELETE FROM Students WHERE codFisc LIKE '%TEST%'");

        // add a generic child with two parent
        $this->assertTrue($db->enrollStudent("childNameTEST", "childSurnameTEST", "SSNCHILDTEST", "nameP1TEST", "surnameP1TEST", "SSNP1TEST", "EmailP1TEST", "nameP2TEST", "surnameP2TEST", "SSNP2TEST", "EmailP2TEST"));

        // add a generic child with one parent
        $this->assertTrue($db->enrollStudent("childNameTEST2P", "childSurnameTEST2P", "SSNCHILDTEST2P", "nameP1TEST1P", "surnameP1TEST1P", "SSNP1TEST1P", "EmailP1TEST1P"));

        // add a student that is already in the DB
        $this->assertFalse($db->enrollStudent("Bob", "Silver", "BOB", "shouldNotBeHere", "shouldNotBeHere", "shouldNotBeHere", "shouldNotBeHere"));

        // add a student with a parent that is already in the DB
        $this->assertTrue($db->enrollStudent("TESTexistingParent", "TESTexistingParent", "TESTexistingParent", "nameP1TEST1P", "surnameP1TEST1P", "SSNP1TEST1P", "EmailP1TEST1P"));

        // check the DB
        $ret = $db->queryForTesting("SELECT COUNT(*) AS OK FROM Students WHERE codFisc='SSNCHILDTEST'");
        $this->assertNotFalse($ret);
        $this->assertEquals(1, $ret->fetch_assoc()["OK"]);

        $ret = $db->queryForTesting("SELECT COUNT(*) AS OK FROM Students WHERE codFisc='SSNCHILDTEST2P'");
        $this->assertNotFalse($ret);
        $this->assertEquals(1, $ret->fetch_assoc()["OK"]);

        $ret = $db->queryForTesting("SELECT COUNT(*) AS OK FROM Students WHERE codFisc='TESTexistingParent'");
        $this->assertNotFalse($ret);
        $this->assertEquals(1, $ret->fetch_assoc()["OK"]);

        // clean the DB
        $db->queryForTesting("DELETE FROM Parents WHERE codFisc LIKE '%TEST%'");
        $db->queryForTesting("DELETE FROM Students WHERE codFisc LIKE '%TEST%'");
    }

    public function testRetrieveAllClasses()
    {
        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        //no classes in db -> expetced null
        $result = $db->retrieveAllClasses();
        $this->assertSame($result, null);

        //inserting classes in db -> expected array of classes
        $db->queryForTesting("INSERT INTO `classes` (`classID`, `coordinatorSSN`) VALUES ('1A', 'FRCWTR'), ('1B', 'MRC')");
        $result = $db->retrieveAllClasses();
        $array = array();
        $array[0] = "1A";
        $array[1] = "1B";
        $this->assertSame($result, $array);
        $db->queryForTesting("DELETE FROM `classes` WHERE 1");
    }

    public function testStoreTimetable()
    {
        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "test";
        $db = new dbAdmin();

        //restoring DB
        $db->queryForTesting("DELETE FROM Timetable");

        //no timetables but in 1A "Italian" is not defined

        $class = "1A";
        $timetable = array();
        $timetable[0][0] = "mon";
        $timetable[0][1] = "1";
        $timetable[0][2] = "Italian";
        $timetable[1][0] = "tue";
        $timetable[1][1] = "3";
        $timetable[1][2] = "Maths";
        
        $this->assertSame(0,$db->storeTimetable($class, $timetable));

        //no timetables -> everything should be ok => expected 1
        $class = "1A";
        $timetable = array();
        $timetable[0][0] = "mon";
        $timetable[0][1] = "1";
        $timetable[0][2] = "History";
        $timetable[1][0] = "tue";
        $timetable[1][1] = "3";
        $timetable[1][2] = "Maths";

        $this->assertSame(1, $db->storeTimetable($class, $timetable));

        $result = $db->queryForTesting("SELECT * FROM Timetable");
        if (!$result)
            $this->assertTrue(false);
        $array = array();

        $row = $result->fetch_array(MYSQLI_ASSOC);
        $array["classID"] = "1A";
        $array["day"] = "mon";
        $array["hour"] = "1";
        $array["subject"] = "History";
        $this->assertSame($row, $array);

        $row = $result->fetch_array(MYSQLI_ASSOC);
        $array["classID"] = "1A";
        $array["day"] = "tue";
        $array["hour"] = "3";
        $array["subject"] = "Maths";
        $this->assertSame($row, $array);

        if ($result->fetch_array(MYSQLI_ASSOC))
            $this->assertTrue(false);

        //timetables present -> expected removal and insert
        $timetable[0][1] = "4";
        $timetable[0][2] = "History";

        $result = $db->storeTimetable($class, $timetable);
        $this->assertSame($result, 1);
        $result = $db->queryForTesting("SELECT * FROM Timetable");
        if (!$result)
            $this->assertTrue(false);

        $row = $result->fetch_array(MYSQLI_ASSOC);
        $array["classID"] = "1A";
        $array["day"] = "mon";
        $array["hour"] = "4";
        $array["subject"] = "History";
        $this->assertSame($row, $array);

        $row = $result->fetch_array(MYSQLI_ASSOC);
        $array["classID"] = "1A";
        $array["day"] = "tue";
        $array["hour"] = "3";
        $array["subject"] = "Maths";
        $this->assertSame($row, $array);

        if ($result->fetch_array(MYSQLI_ASSOC))
            $this->assertTrue(false);

        //restoring DB
        $db->queryForTesting("DELETE FROM Timetable");
    }


    /*PARENT*/

    public function testGetChildClass()
    {

        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "test";
        $db = new dbParent();

        //student in DB, expected class ID
        $ssn = "FRCWTR";

        $result = $db->getChildClass($ssn);
        $this->assertSame($result, "1A");

        //The following part of the test is disabled because the function uses the die() function and the tests would stop here.

        //        //student not in DB, expected null
        //        $ssn='wrong';
        //
        //        $result=$db->getChildClass($ssn);
        //        $this->assertSame ($result,null);


    }

    public function testViewChildAssignments()
    {
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "padre@hotmail.it";
        $db = new dbParent();

        //No assignment for the child class (1D) -> expected empty array
        $ssn = "ISAORA";
        $result = $db->viewChildAssignments($ssn);
        $array = array();
        $this->assertSame($array,$result);

        //Assignment for the child class -> expected filled array

        $db->queryForTesting("INSERT INTO `assignments` (`subject`, `date`, `classID`, `textAssignment`,`pathFilename`) VALUES ('History', '2019-12-11', '1D', 'Number one',''), 
                                                                                                                               ('Math', '2019-12-11', '1D', 'Second','path/to/second/assignment/file'), 
                                                                                                                               ('History', '2019-10-11', '1D', 'Another one',''), 
                                                                                                                               ('Math', '2020-02-11', '1D', 'FirstSecondSemesterFile','path/for/second/semester/file'),
                                                                                                                               ('History', '2020-02-11', '1D', 'SecondFile',''),
                                                                                                                               ('History', '2020-05-25', '1D', 'Another one','')");

        $result = $db->viewChildAssignments($ssn);

        $month = intval(date("m"));

        if($month==1 || $month>8){
            $array["2019-12-11"] = "History:View assignments:Number one~Math:View assignments:Second:path/to/second/assignment/file";
            $array["2019-10-11"] = "History:View assignments:Another one";
        } else {
            $array["2020-02-11"] = "Math:View assignments:FirstSecondSemesterFile~History:View Assignments:SecondFile";
            $array["2020-05-25"] = "History:View assignments:Another one";
        }
        $this->assertSame($array, $result);

        //restoring db
        $db->queryForTesting("DELETE FROM `assignments` WHERE `date` = '2019-12-11' and `classID` = '1D'");
        $db->queryForTesting("DELETE FROM `assignments` WHERE `date` = '2019-10-11' and `classID` = '1D'");
        $db->queryForTesting("DELETE FROM `assignments` WHERE `date` = '2020-02-11' and `classID` = '1D'");
        $db->queryForTesting("DELETE FROM `assignments` WHERE `date` = '2020-05-25' and `classID` = '1D'");
    }

    public function testViewChildLectures(){
        
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "padre@hotmail.it";
        $db = new dbParent();

        //No lectures for the child class (1D) -> expected empty array
        $ssn = "ISAORA";
        $result = $db->viewChildLectures($ssn);
        $array = array();
        $this->assertSame($array,$result);
    }


    public function testRetrieveAttendance()
    {
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "padre@hotmail.it";
        $db = new dbParent();

        //Student with no entries
        $CodFisc = 'JAMBCK';

        $result = $db->retrieveAttendance($CodFisc);
        $this->assertSame($result, array());

        //Student with entries
        $_SESSION['user'] = "cla_9_6@hotmail.it";
        $CodFisc = 'ILA';
        $array = array();

        $month = intval(date("m"));

        if($month==1 || $month>8){
            $array["2019-11-25"] = "late and early - 2 - 4";
            $array["2019-11-26"] = "absent";
            $array["2019-11-27"] = "early - 4";
            $array["2019-11-28"] = "late - 3";
            $array["2019-12-02"] = "late and early - 2 - 4";
        }        

        $result = $db->retrieveAttendance($CodFisc);
        $this->assertSame($array,$result);

    }

    public function testViewChildMarks()
    {
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "parent@parent.it";
        $db = new dbParent();

        //child with no grades (expected empty string)
        $ssn = "LILYCO";
        $result = $db->viewChildMarks($ssn);
        $this->assertSame($result, "");

        //child with grades (expected list of grades separated by ; )

        $ssn = "FRCWTR";
        $result = $db->viewChildMarks($ssn);

        $string = "";
        
        $month = intval(date("m"));

        if($month==1 || $month>8){
            $string .= "History,2019-10-10,6;";
            $string .= "Italian,2019-10-15,9;";
            $string .= "Italian,2019-10-14,9/10;";
            $string .= "Italian,2019-10-10,7+;";
            $string .= "Maths,2019-10-11,9-;";
            $string .= "Philosophy,2019-10-10,5/6;";
            $string .= "Physics,2019-10-12,3+"; //The last one doesn't have ; at the end
        }
        

        $this->assertSame($string, $result);
    }

    public function testRetrieveChildren()
    {
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "mrc@gmail.it";
        $db = new dbParent();

        //Parent with child without a class
        $array = array();
        $result = $db->retrieveChildren($_SESSION['user']);
        $this->assertSame($result, $array);

        //Parent with child with class
        $_SESSION['user'] = "gigimarzullo@genitore1.it";
        $result = $db->retrieveChildren($_SESSION['user']);
        $array[0]["codFisc"] = "HRRWHI";
        $array[0]["name"] = "Harry";
        $array[0]["surname"] = "White";
        $array[0]["classID"] = "1D";
        $array[1]["codFisc"] = "JEPPL";
        $array[1]["name"] = "Jessica";
        $array[1]["surname"] = "Purple";
        $array[1]["classID"] = "1D";
        $this->assertSame($result, $array);
    }

    public function testRetrieveChildTimetable()
    {
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "mrc@gmail.it";
        $db = new dbParent();

        $class = "1A";
        $array = array();

        //no timetable -> expected null
        $result = $db->retrieveChildTimetable($class);
        $this->assertSame($result, $array);

        //inserting timetable -> expected array
        $db->queryForTesting("INSERT INTO `timetable` (`classID`, `day`, `hour`, `subject`) VALUES ('1A', 'mon', '1', 'Italian'), ('1A', 'tue', '3', 'Maths')");

        $array[1]["mon"] = "Italian";
        $array[3]["tue"] = "Maths";

        $result = $db->retrieveChildTimetable($class);
        $this->assertSame($array,$result);

        //restoring DB
        $db->queryForTesting("DELETE FROM Timetable");

        
    }

    public function testViewChildFinalGrades()
    {
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "mrc@gmail.it";
        $db = new dbParent();
        $codFisc = "MRC";
        $array = array();

        $db->queryForTesting("DELETE FROM `finalgrades`");
        //no final grade -> expected empty array
        $result = $db->viewChildFinalGrades($codFisc);
        $this->assertSame($result, $array);

        //inserting grades -> expecting filled array
        $db->queryForTesting("INSERT INTO `finalgrades` (`codFisc`, `subject`, `finalTerm`, `finalGrade`) VALUES ('MRC', 'History', '2019-11-19', '4'), ('MRC', 'History', '2020-06-19', '4'), ('MRC', 'Maths', '2019-12-19', '10'), ('MRC', 'Maths', '2020-06-19', '10')");
        $result = $db->viewChildFinalGrades($codFisc);

        $this->assertNotEmpty($result);

        $this->assertSame(count($result), 2);
        $this->assertContains("Maths,10", $result);
        $this->assertContains("History,4", $result);
    }

    public function testViewTeacherSlots(){
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "parent@parent.it";
        $db = new dbParent();

        //no slots -> expected empty array, except for 1996-07-25
        $array=array();
        $array["1996-07-25"] = "teacherMeetings";
        $codFisc="GNV";
        $result=$db->viewTeacherSlots($codFisc);
        $this->assertSame($array, $result);

        //slots inserted -> expected filled array
        $db->queryForTesting("INSERT INTO `parentmeetings` (`teacherCodFisc`, `day`, `slotNb`, `quarter`, `emailParent`) VALUES ('GNV', '2020-01-13', '1', '3', NULL), ('PIPPO', '2020-01-14', '2', '2', NULL)");
        $array["2020-01-13"] = "teacherMeetings";
        $result=$db->viewTeacherSlots($codFisc);
        $this->assertSame($array, $result);
        $db->queryForTesting("DELETE FROM 'ParentMeetings'");
    }

    public function testGetTeachersByChild(){
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "parent@parent.it";
        $db = new dbParent();

        //parent is not authorized -> expected false
        $codFisc="wrong";
        $result=$db->getTeachersByChild($codFisc);
        $this->assertFalse($result);

        //parent is authorized but no teacher -> expected empty array
        $codFisc="CRS";
        $db->queryForTesting("DELETE FROM `teacherclasssubjecttable`");
        $array=array();
        $result=$db->getTeachersByChild($codFisc);
        $this->assertSame($array, $result);

        //parent is authorized and there are teacher associated -> expected filled array
        $db->queryForTesting("INSERT INTO `TeacherClassSubjectTable` (`codFisc`, `classID`, `subject`) VALUES ('FLCM', '1A', 'Philosophy'),('GNV', '1A', 'Physics'),('GNV', '1D', 'Geography'),('TEA', '1A', 'History'),('TEA', '1A', 'Maths'),('TEA', '1B', 'Italian');");
        $array[0]['codFisc']='GNV';
        $array[0]['surname']='genovese';
        $array[0]['name']='simona';
        $result=$db->getTeachersByChild($codFisc);
        $this->assertSame($array, $result);

    }

    public function testGetTeacherSlotsByDay(){
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "parent@parent.it";
        $db = new dbParent();

        //preparing db
        $db->queryForTesting("DELETE FROM parentmeetings");

        $array=array();

        for($i=1; $i<=6; $i++){
            for($j=1; $j<=4; $j++){
                $array[$i][$j]="no";
            }
        }

        $res="";
        for($i=1; $i<=6; $i++){
            for($j=1; $j<=4; $j++){
                $res.=$i . "_" . $j . "_" . $array[$i][$j] . ",";
            }
        }

        //no meetings, ecpexted string in form: 1_1_no, 1_2_no, ...
        $day="2020-01-07";
        $teacher="GNV";
        $result=$db->getTeacherSlotsByDay($teacher, $day, $_SESSION['user']);
        $this->assertSame($res, $result);

        //inserting meetings
        $db->queryForTesting("INSERT INTO `ParentMeetings` (`teacherCodFisc`, `day`, `slotNb`, `quarter`, `emailParent`) VALUES ('GNV', '2020-01-07', '1', '1', 'parent@parent.it'), ('GNV', '2020-01-07', '1', '2', 'wrong'), ('GNV', '2020-01-07', '1', '3', ''), ('GNV', '2020-01-08', '1', '2', ''), ('PIPPO', '2020-01-07', '1', '1', 'parent@parent.it')");


        $array[1][1]="selected";
        $array[1][2]="full";
        $array[1][3]="free";

        $res="";
        for($i=1; $i<=6; $i++){
            for($j=1; $j<=4; $j++){
                $res.=$i . "_" . $j . "_" . $array[$i][$j] . ",";
            }
        }

        //meetings in db, expected string: hour_quarter_status,hour...
        $result=$db->getTeacherSlotsByDay($teacher, $day, $_SESSION['user']);
        $this->assertSame($res, $result);

        //restoring DB
        $db->queryForTesting("DELETE FROM parentmeetings");
    }

    public function testBookSlot(){
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "parent@parent.it";
        $db = new dbParent();

        $teacher="GNV";
        $day="2020-01-13";
        $slot=1;
        $quarter=1;

        //preparing DB
        $db->queryForTesting("DELETE FROM ParentMeetings");

        //No entry -> expected 'error'
        $result=$db->bookSlot($teacher, $_SESSION['user'], $day, $slot, $quarter);
        $this->assertSame('error', $result);

        //entry with no mail -> expected 'yellow'
        $db->queryForTesting("INSERT INTO ParentMeetings (`teacherCodFisc`, `day`, `slotNb`, `quarter`, `emailParent`) VALUES ('GNV', '2020-01-13', '1', '1', '')");
        $result=$db->bookSlot($teacher, $_SESSION['user'], $day, $slot, $quarter);
        $this->assertSame('yellow', $result);

        //entry with own mail -> expected 'lightgreen'
        $result=$db->bookSlot($teacher, $_SESSION['user'], $day, $slot, $quarter);
        $this->assertSame('lightgreen', $result);

        //entry with other mail -> expected 'darkred'
        $db->queryForTesting("UPDATE ParentMeetings SET emailParent='other' WHERE day='$day' AND teacherCodFisc='$teacher' AND slotNb='$slot' AND quarter='$quarter'");
        $result=$db->bookSlot($teacher, $_SESSION['user'], $day, $slot, $quarter);
        $this->assertSame('lightred', $result);

        //restoring DB
        $db->queryForTesting("DELETE FROM ParentMeetings");

    }

    public function testGetTeacherNameSurname(){
        $_SESSION['role'] = "parent";
        $_SESSION['user'] = "parent@parent.it";
        $db = new dbParent();

        //teacher existing, expected "name surname"
        $ssn="GNV";
        $result=$db->getTeacherNameSurname($ssn);
        $this->assertSame("simona genovese", $result);

        //teacher not in db, expected empty string
        $ssn="I'm not here this is not happening";
        $result=$db->getTeacherNameSurname($ssn);
        $this->assertSame("", $result);
    }




    /* TEACHER */
    public function testViewStudentMarks()
    {

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

        $marks = array();
        array_push($marks, "2019-10-15,9,3");
        array_push($marks, "2019-10-14,9/10,2");
        array_push($marks, "2019-10-10,7+,1");

        $this->assertSame($result, $marks);


        //Errore nella query
        $result = $db->viewStudentMarks($subject, $CodFisc);
        $this->assertSame($result, null);
    }

    public function testViewSlotsAlreadyProvided()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $db->queryForTesting("DELETE FROM ParentMeetings;");

        $res = $db->viewSlotsAlreadyProvided("TEA");
        $this->assertSame("teacherMeetings", $res["1996-07-25"]);

        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',1,1,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',1,2,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',1,3,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',1,4,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-12-11',1,0,'')");

        $res = $db->viewSlotsAlreadyProvided("TEA");
        $this->assertSame("teacherMeetings", $res["1996-07-25"]);
        $this->assertSame("teacherMeetings", $res["2019-10-11"]);
        $this->assertSame("teacherMeetings", $res["2019-12-11"]);

        $db->queryForTesting("DELETE FROM ParentMeetings;");
    }

    public function testShowParentMeetingSlotsOfTheDay()
    {

        // TEA TEACHES HISTORY AND MATHS IN 1A

        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $var = explode(",", $db->showParentMeetingSlotsOfTheDay("TEA", "2019-10-11"));

        for ($i = 0; $i < 6; $i++) {
            $this->assertStringContainsString("free", $var[$i]);
        }

        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',1,1,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',1,2,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',1,3,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',1,4,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',2,1,'')");
        $db->queryForTesting("INSERT INTO ParentMeetings VALUES('TEA','2019-10-11',3,3,'')");

        $var = explode(",", $db->showParentMeetingSlotsOfTheDay("TEA", "2019-10-11", 1));

        for ($i = 0; $i < 3; $i++) {
            $this->assertStringContainsString("selected", $var[$i]);
        }

        for ($i = 3; $i < 6; $i++) {
            $this->assertStringContainsString("free", $var[$i]);
        }

        $db->queryForTesting("INSERT INTO Timetable VALUES('1A','fri',5,'History')");
        $db->queryForTesting("INSERT INTO Timetable VALUES('1A','fri',6,'Maths')");

        $var = explode(",", $db->showParentMeetingSlotsOfTheDay("TEA", "2019-10-11", 1));

        for ($i = 0; $i < 3; $i++) {
            $this->assertStringContainsString("selected", $var[$i]);
        }

        $this->assertStringContainsString("free", $var[$i]);

        for ($i = 4; $i < 6; $i++) {
            $this->assertStringContainsString("lesson", $var[$i]);
        }

        $db->queryForTesting("DELETE FROM Timetable WHERE classID='1A' AND day='fri' and hour=5 and subject='History'");
        $db->queryForTesting("DELETE FROM Timetable WHERE classID='1A' AND day='fri' and hour=6 and subject='Maths'");
        $db->queryForTesting("DELETE FROM ParentMeetings;");
    }

    public function testProvideSlot()
    {

        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $this->assertEquals("lightgreen", $db->provideSlot("TEA", "2019-10-11", "1"));
        $this->assertEquals("white", $db->provideSlot("TEA", "2019-10-11", "1"));
    }

    public function testRecordEarlyExit()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $day = '2019-12-11';
        $ssn = 'FRCWTR';
        $hour = '4';

        //Add early exit on new entry (the student was present before, expected true)
        $result = $db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '0');
        $this->assertSame($row['earlyExit'], '4');

        //restoringDB
        $db->queryForTesting("DELETE FROM attendance WHERE codFisc='$ssn' AND date='$day'");


        //Add early exit on a tuple with late entrance and present student (expected true)
        $day = "2019-11-27";
        $result = $db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '2');
        $this->assertSame($row['earlyExit'], '4');

        //restoring DB
        $db->queryForTesting("UPDATE attendance SET absence='0', earlyExit='0' WHERE codFisc='$ssn' AND date='$day'");


        //Add early exit on a tuple with absent student and no early exit (exception should be thrown -> expected false)
        $day = '2019-12-13';
        $db->queryForTesting("INSERT INTO `attendance` (`date`, `codFisc`, `absence`, `lateEntry`, `earlyExit`) VALUES ('2019-12-13', 'FRCWTR', '1', '0', '0')");

        $result = $db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result, false);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '0');
        $this->assertSame($row['earlyExit'], '0');


        //lateEntry > earlyExit (exception should be thrown -> expected false)
        $db->queryForTesting("UPDATE attendance SET absence='1', lateEntry='5' WHERE codFisc='$ssn' AND date='$day'");

        $result = $db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result, false);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '5');
        $this->assertSame($row['earlyExit'], '0');

        //restoring db
        $db->queryForTesting("DELETE FROM attendance WHERE codFisc='$ssn' AND date='$day'");


        //deleting the early extit with hour=0 -> student should become present (expected true, and removal of tuple from table) 
        $hour = 0;
        $day = '2019-10-03';

        $result = $db->recordEarlyExit($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 0);

        //restoring DB
        $db->queryForTesting("INSERT INTO `attendance` (`date`, `codFisc`, `absence`, `lateEntry`, `earlyExit`) VALUES ('$day', 'FRCWTR', '1', '0', '2')");
    }

    public function testRecordLateEntrance()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        //Tuple not present -> exception should be thrown (expected false)
        $day = '2019-12-11';
        $ssn = 'FRCWTR';
        $hour = '3';

        $result = $db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, false);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 0);

        //Tuple present -> expected true
        $day = '2019-11-27';
        $result = $db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '0');
        $this->assertSame($row['lateEntry'], '3');
        $this->assertSame($row['earlyExit'], '0');

        //restoring db
        $db->queryForTesting("UPDATE attendance SET lateEntry='2' WHERE codFisc='$ssn' AND date='$day'");


        //Adding late entrance with hour>early exit (with no late entrance inserted) -> expected false (exception)
        $day = '2019-10-03';
        $result = $db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, false);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '0');
        $this->assertSame($row['earlyExit'], '2');


        //Same as before but with hour<early exit -> true expected
        $hour = 1;
        $day = '2019-10-03';
        $result = $db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '1');
        $this->assertSame($row['lateEntry'], '1');
        $this->assertSame($row['earlyExit'], '2');

        //restoring db
        $db->queryForTesting("UPDATE attendance SET lateEntry='0' WHERE codFisc='$ssn' AND date='$day'");

        //Adding late entrance to absent student -> expected true, now the student should be present
        $day = '2019-10-09';
        $result = $db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 1);
        $row = $result->fetch_assoc();
        $this->assertSame($row['absence'], '0');
        $this->assertSame($row['lateEntry'], '1');
        $this->assertSame($row['earlyExit'], '0');

        //restoring db
        $db->queryForTesting("UPDATE attendance SET absence='1', lateEntry='0' WHERE codFisc='$ssn' AND date='$day'");


        //Adding late entrance=0 to an absent student -> true expected (with deleting of tuple)
        $hour = 0;
        $result = $db->recordLateEntrance($day, $ssn, $hour);
        $this->assertSame($result, true);

        $result = $db->selectAttendanceStudent($day, $ssn);
        $this->assertSame($result->num_rows, 0);
        $row = $result->fetch_assoc();

        //restoring db
        $db->queryForTesting("INSERT INTO `attendance` (`date`, `codFisc`, `absence`, `lateEntry`, `earlyExit`) VALUES ('$day', 'FRCWTR', '1', '0', '0')");
    }


    public function testCheckAbsenceEarlyExitLateEntrance()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $ssn = "FRCWTR";
        $day = "2019-12-06";
        $array = array();


        //No entry in db (should return an empty array)

        $array[0] = null;
        $array[1] = null;
        $array[2] = null;
        $array[3] = null;
        $array[4] = null;

        $result = $db->checkAbsenceEarlyExitLateEntrance($ssn, $day);
        $this->assertSame($result, $array);


        //Entry in db (should return an array with results)
        $day = "2019-09-20";
        $array[0] = $day;
        $array[1] = $ssn;
        $array[2] = 1;
        $array[3] = 1;
        $array[4] = 5;

        $result = $db->checkAbsenceEarlyExitLateEntrance($ssn, $day);
        $this->assertSame($result, $array);
    }

    public function testUpdateAttendance()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        $ssn = "FRCWTR";
        $day = "2019-12-08";

        //New entry -> the student must be absent after the insert (result should be true)
        $result = $db->updateAttendance($ssn, $day);
        $this->assertSame($result, true);

        //there should be one row with absence set to 1
        $result = $db->selectAttendanceStudent($day, $ssn);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows, 1);

        $array = $result->fetch_assoc();
        $this->assertSame($array['absence'], '1');


        //Entry is present without late or early -> entry must be deleted after the update
        $result = $db->updateAttendance($ssn, $day);
        $this->assertSame($result, true);

        //there should be no rows beecause the entry should have been deleted
        $result = $db->selectAttendanceStudent($day, $ssn);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows, 0);


        //Entry is present with both late and early -> exception thrown (result should be false)
        $day = "2020-01-12";
        $result = $db->updateAttendance($ssn, $day);
        $this->assertSame($result, false);

        //No changes should be made to the DB
        $result = $db->selectAttendanceStudent($day, $ssn);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows, 1);

        $array = $result->fetch_assoc();
        $this->assertSame($array['absence'], '1');
        $this->assertSame($array['lateEntry'], '2');
        $this->assertSame($array['earlyExit'], '4');


        //Entry is asbent only with late entry -> exception thrown
        $day = "2019-12-03";

        $result = $db->updateAttendance($ssn, $day);
        $this->assertSame($result, false);

        //No changes should be made to the DB
        $result = $db->selectAttendanceStudent($day, $ssn);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame($result->num_rows, 1);
        $array = $result->fetch_assoc();

        $this->assertSame($array['absence'], '0');
        $this->assertSame($array['lateEntry'], '3');
        $this->assertSame($array['earlyExit'], '0');
    }

    public function testGetAssignmentsByClassAndSubject(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //Teacher has not the subject in the class -> expected null
        $class="1A";
        $subject="Math";
        $beginSemester="2019-09-01";
        $endSemester="2020-01-31";
        
        $result=$db->getAssignmentsByClassAndSubject($_SESSION['user'], $class, $subject, $beginSemester, $endSemester);
        $this->assertSame(null, $result);

        //assignments in db and correct class, subject -> expected filled array
        $subject="Physics";
        $array=array();
        $array[0]="2019-11-27,Vectors,";
        $result=$db->getAssignmentsByClassAndSubject($_SESSION['user'], $class, $subject, $beginSemester, $endSemester);
        $this->assertSame($array, $result);
    }

    public function testGetStudentsByClass2()
    {

        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "test";
        $db = new dbTeacher();

        //Existing class (should return 5 entries, the data cannot be tested because the query is not ordered)
        $class = "1B";
        $result = $db->getStudentsByClass2($class);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 5);

        //Unexisting class (return should be null)
        $class = "wrong";
        $result = $db->getStudentsByClass2($class);
        $this->assertSame($result, null);
    }


    public function testGetAssignments()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //Existing teacher with subjects and assignments (should return one assignment)
        $result = $db->getAssignments($_SESSION['user']);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        //testing the value returned
        $this->assertSame($result[0], "1A,Physics,2019-11-27,Vectors");

        //Non existing teacher (result should be null)
        $result = $db->getAssignments("iAmNotHere");
        $this->assertSame($result, null);
    }

    public function testGetAssignmentsByClassAndDate()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();
        $class = "1A";
        $date = "2019-12-03";
        $array = array();

        //teacher with no assignment, expected null
        $result = $db->getAssignmentsByClassAndDate($_SESSION['user'], $class, $date);
        $this->assertSame($result, null);

        //teacher with assignment, expected filled array
        $_SESSION['user'] = "TEA";
        $array[0] = "History,WWII,";
        $result = $db->getAssignmentsByClassAndDate($_SESSION['user'], $class, $date);
        $this->assertNotNull($result);
        $this->assertSame($array,$result);
    }

    public function testGetLecturesByTeacher()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "TEA";
        $db = new dbTeacher();

        //Existing lectures
        $result = $db->getLecturesByTeacher($_SESSION['user']);
        if (!$result)
            $this->fail();
        $this->assertSame(count($result), 4);
        $this->assertSame($result[0], "1A,History,2019-11-11,1,arg0");
        $this->assertSame($result[1], "1A,Italian,2019-11-05,3,arg2");
        $this->assertSame($result[2], "1A,Maths,2019-11-05,2,arg1");
        $this->assertSame($result[3], "1A,History,2019-11-05,1,arg0");


        //Non existing lectures (should return null)
        $result = $db->getLecturesByTeacher("wrong");
        $this->assertSame($result, null);
    }

    public function testGetSubjectsByTeacherAndClass2()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Existing subject
        $class = "1A";

        $result = $db->getSubjectsByTeacherAndClass2($_SESSION['user'], $class);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "Philosophy");

        //Non existing subject or teacher (should return null)
        $class = "wrong";
        $result = $db->getSubjectsByTeacherAndClass2("Wrong", $class);
        $this->assertSame($result, null);
    }

    public function testGetClassesByTeacher2()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Existing class
        $result = $db->getClassesByTeacher2($_SESSION['user']);
        $this->assertSame(1, count($result));
        $this->assertSame("1A",$result[0]);

        //Non existing class (should return empty array)
        $array=array();
        $result = $db->getClassesByTeacher2("wrong");
        $this->assertSame($array, $result);
    }

    public function testInsertDailyLesson()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //New entry -> insert
        $date = "2019-12-06";
        $hour = "1";
        $class = "1A";
        $teacher = "FLCM";
        $subject = "Philosophy";
        $topics = "Some nice topics";

        $result = $db->insertDailyLesson($date, $hour, $class, $teacher, $subject, $topics);
        $this->assertSame($result, null);

        $result = $db->getLecturesByTeacher($_SESSION['user']);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,Philosophy,2019-12-06,1,Some nice topics");

        //Entry already in db -> insert fail (should return -1 and nothing should change in the DB)
        $topics = "Some WRONG topics";
        $result = $db->insertDailyLesson($date, $hour, $class, $teacher, $subject, $topics);
        $this->assertSame($result, -1);

        $result = $db->getLecturesByTeacher($_SESSION['user']);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,Philosophy,2019-12-06,1,Some nice topics");
    }

    public function testUpdateDailyLesson()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Entry in DB
        $date = "2019-12-06";
        $hour = "1";
        $class = "1A";
        $teacher = "FLCM";
        $subject = "Philosophy";
        $topics = "Some updated topics";
        //the function always return null, both in case of success and in case of failure
        $result = $db->updateDailyLesson($date, $hour, $class, $subject, $topics);
        $this->assertSame($result, null);

        $result = $db->getLecturesByTeacher($_SESSION['user']);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,Philosophy,2019-12-06,1,Some updated topics");

        //Entry not in DB (nothing should change in the DB)
        $date2 = "2019-12-17";
        $topics2 = "Some WRONG topics";
        $result = $db->updateDailyLesson($date, $hour, $class, $subject, $topics);
        $this->assertSame($result, null);

        $result = $db->getLecturesByTeacher($_SESSION['user']);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,Philosophy,2019-12-06,1,Some updated topics");
    }


    public function testDeleteDailyLesson()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Entry not in DB -> nothing should change
        $date = "2019-12-07";
        $hour = "1";
        $class = "1A";

        //this function always returns null
        $result = $db->deleteDailyLesson($date, $hour, $class);
        $this->assertSame($result, null);

        $result = $db->getLecturesByTeacher($_SESSION['user']);
        if (!$result)
            $this->assertTrue(false);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,Philosophy,2019-12-06,1,Some updated topics");


        //Entry in DB -> should delete it (getLectures should return null)
        $date = "2019-12-06";

        $result = $db->deleteDailyLesson($date, $hour, $class);
        $this->assertSame($result, null);

        $result = $db->getLecturesByTeacher($_SESSION['user']);
        $this->assertSame($result, null);
    }

    public function testGetStudentsName()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "FLCM";
        $db = new dbTeacher();

        //Student in DB (should return "Surname Name" of the student)
        $codfisc = "MRC";
        $result = $db->getStudentsName($codfisc);
        $this->assertSame($result, "Cipriano Marco");

        //Student not in DB (Should return "")
        $codfisc = "WRNG";
        $result = $db->getStudentsName($codfisc);
        $this->assertSame($result, "");
    }

    public function testGetGradesByTeacher()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //Teacher in DB with marks inserted (there is 1 mark for that teacher);
        $codfisc = "GNV";
        $result = $db->getGradesByTeacher($codfisc);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");

        //Teacher not in DB or without marks (should return null);
        $codfisc = "WRNG";
        $result = $db->getGradesByTeacher($codfisc);
        $this->assertSame($result, null);
    }

    public function testInsertGrade()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //New entry->insert successful (should return null)
        $date = "2019-12-12";
        $hour = 2;
        $student = "FRCWTR";
        $subject = "Physics";
        $grade = "5/6";
        $codfisc = "GNV";

        $result = $db->insertGrade($date, $hour, $student, $subject, $grade);
        $this->assertSame($result, null);
        //controls on data
        $result = $db->getGradesByTeacher($codfisc);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-12-12,2,5/6");
        $this->assertSame($result[1], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");

        //Existing entry (should return -1 and make no changes to DB)
        $result = $db->insertGrade($date, $hour, $student, $subject, $grade);
        $this->assertSame($result, -1);

        $result = $db->getGradesByTeacher($codfisc);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-12-12,2,5/6");
        $this->assertSame($result[1], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");
    }

    public function testUpdateMark()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        $codFiscTeacher = "GNV";

        //Mark is present -> update should be successful
        $date = "2019-12-12";
        $hour = 2;
        $codFisc = "FRCWTR";
        $subject = "Physics";
        $grade = 10;

        $result = $db->updateMark($codFisc, $subject, $date, $hour, $grade);
        //this function always return null
        $this->assertSame($result, null);
        //data controls
        $result = $db->getGradesByTeacher($codFiscTeacher);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-12-12,2,10");
        $this->assertSame($result[1], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");

        //Mark is not present -> update should fail
        $date = "wrong";
        $result = $db->updateMark($codFisc, $subject, $date, $hour, $grade);
        $this->assertSame($result, null);

        $result = $db->getGradesByTeacher($codFiscTeacher);
        $this->assertSame(count($result), 2);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-12-12,2,10");
        $this->assertSame($result[1], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");
    }

    public function testDeleteMark()
    {
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //Existing mark (this function always returns 0)
        $date = "2019-12-12";
        $hour = 2;
        $codFisc = "FRCWTR";
        $subject = "Physics";
        $result = $db->deleteMark($codFisc, $date, $hour, $subject);
        $this->assertSame($result, 0);
        //data control
        $codfiscTeacher = "GNV";
        $result = $db->getGradesByTeacher($codfiscTeacher);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");

        //Non existing mark (no changes to the DB should occur)
        $result = $db->deleteMark($codFisc, $date, $hour, $subject);
        $this->assertSame($result, 0);
        //data control
        $codfiscTeacher = "GNV";
        $result = $db->getGradesByTeacher($codfiscTeacher);
        $this->assertSame(count($result), 1);
        $this->assertSame($result[0], "1A,FRCWTR,Forcignano,Walter,Physics,2019-10-12,1,3+");
    }

    function testRetrieveEmailAddresses(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        $result = $db->retrieveEmailAddresses("notATeacher",'2019-10-11',1); // not a teacher => no email addresses
        $this->assertSame("error",$result);

        $db->queryForTesting("INSERT INTO `ParentMeetings` (`teacherCodFisc`, `day`, `slotNb`, `quarter`, `emailParent`) VALUES
                                                                  ('GNV', '2019-10-11', 1, 1, \"first@parent.it\"),
                                                                  ('GNV', '2019-10-11', 1, 2, \"second@parent.it\"),
                                                                  ('GNV', '2019-10-11', 1, 3, \"third@parent.it\"),
                                                                  ('GNV', '2019-12-11', 4, 1, \"another@parent.it\");");

        $result = $db->retrieveEmailAddresses($_SESSION['user'],"2019-10-11",1); // teacher-data with 3 appointments
        $emails = explode("_",$result);
        $this->assertSame(4,count($emails)); // the last _ is left here

        $this->assertSame("first@parent.it",$emails[0]);
        $this->assertSame("second@parent.it",$emails[1]);
        $this->assertSame("third@parent.it",$emails[2]);
        $this->assertSame("",$emails[3]);

        $result = $db->retrieveEmailAddresses($_SESSION['user'],"2019-12-11",4); // teacher-data with 1 appointment
        $emails = explode("_",$result);
        $this->assertSame(2,count($emails));
        $this->assertSame("another@parent.it",$emails[0]);
        $this->assertSame("",$emails[1]);

        $db->queryForTesting("DELETE FROM `ParentMeetings`;");
    }

    function testIsCoordinator(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        $class="1A";

        //teacher is not a coordinator -> expected false
        $result=$db->isCoordinator($_SESSION['user'], $class);
        $this->assertSame(false, $result);

        $db->queryForTesting("INSERT INTO `classes` (`classID`, `coordinatorSSN`) VALUES ('1A', 'GNV')");

        //teacher is a coordinator -> expected true
        $result=$db->isCoordinator($_SESSION['user'], $class);
        $this->assertSame(true, $result);

        $db->queryForTesting("DELETE FROM `classes` WHERE `classes`.`classID` = '1A';");

    }

    function testRetrieveTimetableOfAClass(){
            $_SESSION['role'] = "teacher";
            $_SESSION['user'] = "GNV";
            $db = new dbTeacher();
            //preparing DB
            $db->queryForTesting("DELETE FROM Timetable");
            $class="1A";
            $array=array();
            
            //class has no timetable -> expected empty array
            $result=$db->retrieveTimetableOfAClass($class, $_SESSION['user']);
            $this->assertSame($array, $result);
            //teacher has no right to see class timetable -> expected empty array
            $class="1B";
            $result=$db->retrieveTimetableOfAClass($class, $_SESSION['user']);
            $this->assertSame($array, $result);
            $db->queryForTesting("INSERT INTO `timetable` (`classID`, `day`, `hour`, `subject`) VALUES ('1A', 'Mon', '1', 'Physics'), ('1B', 'Mon', '2', 'History')");
            //class has timetable and teacher can see it -> expected filled array in the format [hour][day]="subject"
            $class="1A";
            $array[1]["Mon"]="Physics";
            $result=$db->retrieveTimetableOfAClass($class, $_SESSION['user']);
            $this->assertSame($array, $result);
            //restoring DB
            $db->queryForTesting("DELETE FROM Timetable");
            
    }

    function testRetrieveTimetableTeacher(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //preparing DB
        $db->queryForTesting("DELETE FROM Timetable");

        $array=array();
        
        //teacher does not exists in db -> expected empty array
        $result=$db->retrieveTimetableTeacher("wrong");
        $this->assertSame($array, $result);

        //teacher has no timetable -> expected array filled with '-'
        for($i=1; $i<=6; $i++){
            $array[$i]["mon"]='-';
            $array[$i]["tue"]='-';
            $array[$i]["wed"]='-';
            $array[$i]["thu"]='-';
            $array[$i]["fri"]='-';
        }
        $result=$db->retrieveTimetableTeacher($_SESSION['user']);
        $this->assertSame($array, $result);

        //teacher has timetable -> expected filled array in the format [hour][day]="*subject* in class *class*"
        $db->queryForTesting("INSERT INTO `timetable` (`classID`, `day`, `hour`, `subject`) VALUES ('1A', 'mon', '1', 'Physics'), ('1B', 'Mon', '2', 'History')");
        $array[1]["mon"]="Physics in class 1A";
        $result=$db->retrieveTimetableTeacher($_SESSION['user']);
        $this->assertSame($array, $result);

        //restoring DB
        $db->queryForTesting("DELETE FROM Timetable");
        
    }

    function testCheckIfExists(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();

        //teache not in db -> expected false
        $result=$db->checkIfExist("False");
        $this->assertFalse($result);

        //teacher in db -> expected true
        $result=$db->checkIfExist("GNV");
        $this->assertTrue($result);
    }
    
    function testCheckIfAuthorized(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "GNV";
        $db = new dbTeacher();
        
        $class="1B";

        //not authorized -> expected false
        $result=$db->checkIfAuthorized($class, $_SESSION['user']);
        $this->assertSame(false, $result);

        $class="1A";
        //authorized -> expected true
        $result=$db->checkIfAuthorized($class, $_SESSION['user']);
        $this->assertSame(true, $result);
    }

    function testGetLecturesByTeacherClassAndSubject(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "TEA";
        $db = new dbTeacher();

        //no lectures -> expected null
        $class="1A";
        $subject="Wrong";
        $beginning="2019-09-01";
        $end="2020-01-31";
        $result=$db->getLecturesByTeacherClassAndSubject($_SESSION['user'], $class, $subject, $beginning, $end);
        $this->assertSame(array(), $result);

        //teacher has lectures -> expected filled array
        $subject="History";
        $array=array();
        $array[0]="2019-11-11,1,arg0";
        $array[1]="2019-11-05,1,arg0";
        $result=$db->getLecturesByTeacherClassAndSubject($_SESSION['user'], $class, $subject, $beginning, $end);
        $this->assertSame($array, $result);
    }

    function testGetLecturesByTeacherClassSubjectAndDate(){
        $_SESSION['role'] = "teacher";
        $_SESSION['user'] = "TEA";
        $db = new dbTeacher();

        //no lectures -> expected null
        $class="1A";
        $subject="Wrong";
        $date = '2019-10-11';
        $result=$db->getLecturesByTeacherClassSubjectAndDate($_SESSION['user'], $class, $subject, $date);
        $this->assertSame(array(), $result);

        $db->queryForTesting("INSERT INTO `Lectures` (`date`, `hour`, `classID`, `codFiscTeacher`, `subject`, `topic`) VALUES
                                                            ('2019-12-10', 1, '1A', 'TEA', 'History', 'arg0'),
                                                            ('2019-12-10', 2, '1A', 'TEA', 'History', 'arg1')");

        //teacher has lectures -> expected filled array
        $subject="History";
        $date = '2019-12-10';
        $array=array();
        $array[0]="1,arg0";
        $array[1]="2,arg1";
        $result=$db->getLecturesByTeacherClassSubjectAndDate($_SESSION['user'], $class, $subject, $date);
        $this->assertSame($array, $result);

        $db->queryForTesting("DELETE FROM Lectures WHERE date='2019-12-10'");
    }
}
