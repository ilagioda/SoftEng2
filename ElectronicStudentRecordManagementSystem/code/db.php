<?php
class db
{

    private $conn;

    function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "school";

        // Create connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
            $this->conn->close();
        }
    }

    /**
     * Generic methods
     */


    protected function query($queryToBeExecuted)
    {
        return $this->conn->query($queryToBeExecuted);
    }

    protected function prepareStatement($preparedStatement)
    {
        if (!$stmt = $this->conn->prepare($preparedStatement))
            die("Prepare phase Failed in the Transaction.");
        return $stmt;
    }

    /**
     * Transaction oriented commands
     */

    protected function begin_transaction()
    {
        $this->conn->begin_transaction();
    }

    protected function commit()
    {
        return $this->conn->commit();
    }

    protected function rollback()
    {
        return $this->conn->rollback();
    }

    /**
     * Problem specific methods
     */

    public function getSubjectTaughtInClass($class)
    {

        $class = $this->sanitizeString($class);

        $year = intval(substr($class, 0, 1));

        if ($year < 1 || $year > 5) {
            // year non valid
            throw new Exception("Inserted non valid year: " . $year);
        }

        $result = $this->query("SELECT name FROM Subjects WHERE year='$year';");

        $subjects = array();

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            array_push($subjects, $row['name']);
        }

        return $subjects;
    }

    function getHashedPassword($user)
    {
        $sql = "SELECT * FROM Parents WHERE email='$user'";
        $sql2 = "SELECT * FROM Teachers WHERE codFisc='$user'";
        $sql3 = "SELECT * FROM Principals WHERE codFisc='$user'";
        $sql4 = "SELECT * FROM Admins WHERE codFisc='$user'";
        $ret_value = array();

        if (preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $user)) {
            //it's a mail so I only check in parents table.
            $result = $this->query($sql);
            if (!$result || $result->num_rows == 0) return false;
            else {
                //it's a parent
                $result = $result->fetch_array(MYSQLI_ASSOC);
                $ret_value["user"] = $result["email"];
                $ret_value["role"] = "parent";
                $ret_value["hashedPassword"] = $result["hashedPassword"];
                $ret_value["firstLogin"] = $result["firstLogin"];
            }
        } else {
            //check if it's a teacher, a principal or an admin
            $result = $this->query($sql2);

            if (!$result) return false;
            if ($result->num_rows == 0) {
                $result = $this->query($sql3);

                if (!$result) return false;
                if ($result->num_rows == 0) {
                    $result = $this->query($sql4);

                    if (!$result) return false;
                    if ($result->num_rows == 0) {
                        return false; //neither a parent, nor a teacher, nor a principal, nor an admin
                    } else {
                        //it's an admin
                        $result = $result->fetch_array(MYSQLI_ASSOC);
                        $ret_value["user"] = $result["codFisc"];
                        $ret_value["role"] = "admin";
                        $ret_value["hashedPassword"] = $result["hashedPassword"];
                        $ret_value["sysAdmin"] = $result["sysAdmin"];
                    }
                } else {
                    //it's a principal //will not exist anymore
                    $result = $result->fetch_array(MYSQLI_ASSOC);
                    $ret_value["user"] = $result["codFisc"];
                    $ret_value["role"] = "principal";
                    $ret_value["hashedPassword"] = $result["hashedPassword"];
                }
            } else {
                //it's a teacher
                $result = $result->fetch_array(MYSQLI_ASSOC);
                $ret_value["user"] = $result["codFisc"];
                $ret_value["role"] = "teacher";
                $ret_value["hashedPassword"] = $result["hashedPassword"];
                $ret_value["principal"] = $result["principal"];
            }
        }
        return $ret_value;
    }
    public function ChangePassword($user, $hashed_pw, $table, $first_time = false)
    {
        if ($first_time) return $this->query("UPDATE $table SET hashedPassword = '$hashed_pw', firstLogin=0 WHERE email='$user'");

        return $this->query("UPDATE $table SET hashedPassword = '$hashed_pw' WHERE email='$user'");
    }

    function getAnnouncements()
    {
        $sql = "SELECT * FROM Announcements ORDER BY Timestamp DESC";
        $res = $this->query($sql);
        return $res;
    }

    /**
     * Utilities
     */
    function sanitizeString($var)
    {
        $var = strip_tags($var);
        $var = htmlentities($var);
        if (get_magic_quotes_gpc())
            $var = stripslashes($var);
        return $this->conn->real_escape_string($var);
    }

    function __destruct()
    {
        $this->conn->close();
    }
}

class dbAdmin extends db
{

    function __construct()
    {
        if ($_SESSION['role'] != "admin") throw new Exception("Creating dbAdmin object for an user who is NOT logged in as an admin");
        parent::__construct();
    }

    function readClassCompositions($classID)
    {
        $codFisc = "";
        $name = "";
        $surname = "";
        $class = "";

        $compositionVector = array();
        // a prepared statement needs to be configured
        $stmt = $this->prepareStatement(
            "SELECT s.`codFisc`,`name`,`surname`,p.`classID` 
        FROM `Students` as s , `ProposedClasses` as p 
        WHERE s.codFisc = p.codFisc AND p.classID = ? ORDER BY 'CLASSID'"
        );
        // the parameter is bound to the first '?' in the upper query
        if (!$stmt->bind_param("s", $classID))
            die("Binding Failed in the Transaction.");

        // the result is characterized by four coloumns and needs to be bounded to corresponding variables
        if (!$stmt->bind_result($codFisc, $name, $surname, $class))
            die("Binding result Failed in the Transaction.");

        try {
            // The statement is excecuted but the variables do not contain the results
            if (!$stmt->execute())
                throw new Exception("Select Failed.");
            $i = 0;
            //Once the statement is excecuted in the variables will be saved the corrisponding values only after the fetch()  
            while ($row = $stmt->fetch()) {
                $codFiscnameSurnameClass = array($codFisc, $name, $surname, $class);
                $compositionVector[$i] = $codFiscnameSurnameClass;
                $i++;
            }

            /* close statement */
            $stmt->close();
            //the result is saved in a vector of vectors.
            //Each row in the table corresponds to a vector in compositionVector
            return $compositionVector;
        } catch (Exception $e) {
            // Any exception will produce an error message.
            echo "Error: " . $e->getMessage();
        }
    }

    function insertOfficialAccount($who, $SSN, $hashedPw, $name, $surname, $rights = 0)
    {
        $this->begin_transaction();

        if ($who == "Teachers" && $rights == 1) {
            $sel = $this->query("SELECT COUNT(*) as PRIN_NUM FROM $who WHERE principal=1");
            $sel = $sel->fetch_assoc();
            if (!$sel || $sel["PRIN_NUM"] != 0) {
                $this->rollback();
                return false;
            }
        }


        $sql = "INSERT INTO $who VALUES('$SSN', '$hashedPw', '$name', '$surname', '$rights')";
        $res = $this->query($sql);
        return $this->commit();
    }


    function insertCommunication($title, $text)
    {
        $res = $this->query("SELECT MAX(ID) as oldID FROM Announcements");

        if (!$res) return false;

        $res = $res->fetch_assoc();

        $newID = $res['oldID'] + 1;
        return $this->query("INSERT INTO Announcements VALUES('$newID', CURRENT_TIMESTAMP, '$title', '$text')");
    }

    function SearchInParents($user, $pass)
    {
        $sql = "SELECT email,hashedPassword FROM Parents /* Parents, Principals, Teachers, Admins*/
        WHERE email='$user' AND hashedPassword='$pass'";
        $resultQuery = $this->query($sql);
        return $resultQuery;
    }

    function readAllClasses()
    {

        $sql = "SELECT DISTINCT classID FROM ProposedClasses";
        $resultQuery = $this->query($sql);

        if ($resultQuery->num_rows > 0) {
            $resultArray = array();
            // output data of each row
            while ($row = $resultQuery->fetch_assoc()) {
                array_push($resultArray, $row["classID"]);
            }
            return $resultArray;
        }
    }

    function updateStudentsClass($vectorCodFiscNameSurnameClass)
    {
        $stmt = $this->prepareStatement("UPDATE `Students` SET `classID`= ? WHERE `codFisc` = ? ");
        foreach ($vectorCodFiscNameSurnameClass as $value) {
            $codFisc = $value[0];
            $classID = $value[3];

            if (!$stmt->bind_param("ss", $classID, $codFisc))
                die("Binding Failed the update statement.");

            if (!$stmt->execute())
                die("Update Failed.");

            if (($stmt->affected_rows) != 1) {
                die("Update Failed.");
            }
        }
        /* close statement */
        $stmt->close();

        $stmt = $this->prepareStatement("DELETE FROM `ProposedClasses` WHERE `classID` = ?");
        if (!$stmt->bind_param("s", $classID))
            die("Binding Failed the delete statement.");

        if (!$stmt->execute())
            die("Delete Failed.");

        if (($stmt->affected_rows) == 0) {
            die("Delete Failed.");
        }
    }


    public function TakeParentsMail()
    {
        $result = $this->query("SELECT email, hashedPassword, firstLogin FROM Parents ORDER BY hashedPassword, email");
        return $result;
    }

    public function insertStudent($name, $surname, $SSN, $email1, $email2)
    {
        return $this->query("INSERT INTO Students(codFisc, name, surname, emailP1, emailP2, classID) VALUES ('$SSN','$name','$surname','$email1','$email2', '')");
    }

    public function insertParent($name, $surname, $SSN, $email)
    {
        return $this->query("INSERT INTO Parents(email, hashedPassword, name, surname, codFisc, firstLogin) VALUES ('$email', '','$name','$surname','$SSN', 1)");
    }

    public function insertLectures($date, $hour, $classID, $codFiscTeacher, $subject, $topic)
    {
        return $this->query("INSERT INTO Lectures(date, hour, classID, codFiscTeacher, subject, topic) 
								VALUES('$date', '$hour', '$classID', '$codFiscTeacher', '$subject', '$topic')");
    }

    public function enrollStudent($name, $surname, $SSN, $name1, $surname1, $SSN1, $email1, $name2, $surname2, $SSN2, $email2)
    {

        /* Sanitize parameters before inserting them into the DB */
        $name = $this->sanitizeString($name);
        $surname = $this->sanitizeString($surname);
        $SSN = $this->sanitizeString($SSN);
        $name1 = $this->sanitizeString($name1);
        $surname1 = $this->sanitizeString($surname1);
        $SSN1 = $this->sanitizeString($SSN1);
        $email1 = $this->sanitizeString($email1);
        $name2 = $this->sanitizeString($name2);
        $surname2 = $this->sanitizeString($surname2);
        $SSN2 = $this->sanitizeString($SSN2);
        $email2 = $this->sanitizeString($email2);

        $this->begin_transaction();

        /* Insert student into the DB */
        $result = $this->insertStudent($name, $surname, $SSN, $email1, $email2);
        if (!$result) {
            $this->rollback();
            return 0;
        }


        /* Insert parent 1 into the DB */
        $result = $this->insertParent($name1, $surname1, $SSN1, $email1);
        // if(!$result){
        //     $this->rollback();    
        //     return 0;
        // }

        if (!empty($email2)) {
            /* Insert parent 2 into the DB */
            $result = $this->insertParent($name2, $surname2, $SSN2, $email2);
            // if(!$result){
            //     $this->rollback();
            //     return 0;
            // }
        }

        $this->commit();
        return 1;
    }

    public function retrieveAllClasses()
    {
        $sql = "SELECT classID FROM Classes";
        $resultQuery = $this->query($sql);
        if ($resultQuery->num_rows > 0) {
            $resultArray = array();
            while ($row = $resultQuery->fetch_assoc()) {
                array_push($resultArray, $row["classID"]);
            }
            return $resultArray;
        }
    }

    public function storeTimetable($class, $timetable)
    {

        $class = $this->sanitizeString($class);

        $this->begin_transaction();

        // Ckeck if a timetable for the chosen class is already present in the DB
        $result = $this->query("SELECT * FROM Timetable WHERE class='$class'");
        if (!$result) {
            $this->rollback();
            return 0;
        }
        if ($result->num_rows > 0) {
            // There's already a timetable for the chosen class in the DB --> delete the old one in order to insert the new one
            $result = $this->query("DELETE FROM Timetable WHERE class='$class'");
            if (!$result) {
                $this->rollback();
                return 0;
            }
        }

        foreach ($timetable as $line) {

            // Retrieve the fields
            $day = $line[0];
            $hour = $line[1];
            $subject = $line[2];

            // Sanitize
            $day = $this->sanitizeString($day);
            $hour = $this->sanitizeString($hour);
            $subject = $this->sanitizeString($subject);

            $result = $this->query("INSERT INTO Timetable(`class`, `day`, `hour`, `subject`) VALUES ('$class','$day','$hour','$subject')");
            if (!$result) {
                $this->rollback();
                return 0;
            }
        }

        $this->commit();
        return 1;
    }
}



class dbParent extends db
{

    function __construct()
    {
        if ($_SESSION['role'] != "parent") throw new Exception("Creating DbParent object for an user who is NOT logged in as a parent");
        parent::__construct();
    }

    protected function checkIfAuthorisedForChild($CodFisc)
    {


        /**
         * 
         * IMPORTANT: TO BE USED INSIDE A TRANSACTION
         * 
         * Verify if the user logged in is actually allowed to see the marks of the requested child
         * @return error String if the users is not authorised, true otherwise
         */
        $CodFisc = $this->sanitizeString($CodFisc);

        $result = $this->query("SELECT * FROM Students WHERE codFisc='$CodFisc';");

        if (!$result) {
            return "Unable to select student $CodFisc";
        }

        if (($row = $result->fetch_array(MYSQLI_ASSOC)) == NULL) {
            return "No student with ID $CodFisc ";
        }

        $parent1 = $row['emailP1'];
        $parent2 = $row['emailP2'];

        if ($_SESSION['user'] != $parent1 && $_SESSION['user'] != $parent2) {
            return "You are not authorised to see this information.";
        }

        return true;
    }

    public function retrieveChildren($email)
    {

        /**
         * This function receives the email of a parent and returns the list of children of that parent
         */

        $email = $this->sanitizeString($email);

        $result = $this->query("SELECT codFisc,name,surname,classID FROM Students 
                                WHERE (classID != '') AND (emailP1='$email' OR emailP2 = '$email') 
                                ORDER BY name,surname;");

        if (!$result)
            die("Unable to select children for $email");

        $children = array();

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            array_push($children, $row);
        }

        return $children;
    }

    public function viewChildMarks($CodFisc)
    {
        /** 
         * This function receives the fiscal code of a child, 
         * verifies if the child is actually a child of that parent
         * and then returns the marks.    
         * Format: Subject,Date,Mark;Subject,Date..Mark;
         */

        $CodFisc = $this->sanitizeString($CodFisc);

        $this->begin_transaction();

        $authorised = $this->checkIfAuthorisedForChild($CodFisc);

        if ($authorised !== true) {
            // not authorised to see the child
            $this->rollback();
            die($authorised);
        }

        /* The user can see the marks => retrieve the marks of the current year */

        $boundaries = getCurrentSemester();

        $beginningDate = $boundaries[0];
        $endingDate = $boundaries[1];

        $result = $this->query("SELECT subject,date,mark FROM Marks WHERE codFisc='$CodFisc' AND date > '$beginningDate' AND date< '$endingDate' ORDER BY subject ASC,date DESC,hour DESC;");

        if (!$result) {
            $this->rollback();
            die("Unable to select marks for student $CodFisc");
        }

        $marks = "";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            $marks = $marks . $row['subject'] . ',' . $row['date'] . "," . $row['mark'] . ";";
        }

        if ($marks != "") $marks = substr($marks, 0, -1); // to remove the last ;

        $this->commit();

        return $marks;
    }

    public function getMaterials($class, $subject)
    {
        return $this->query("SELECT * FROM supportMaterials WHERE Class='$class' and Subject='$subject'");
    }

    public function retrieveAttendance($CodFisc)
    {

        /**
         * Retrieve the attendance of a given student in the current semester.
         * @param $codFisc (String) CodFisc of the searched student, e.g. 2015.
         * @return (Array) The calendar's html.
         */

        $this->begin_transaction();

        $CodFisc = $this->sanitizeString($CodFisc);

        $authorised = $this->checkIfAuthorisedForChild($CodFisc);

        if ($authorised !== true) {
            // not authorised to see the child
            $this->rollback();
            die($authorised);
        }

        $boundaries = getCurrentSemester();

        $beginningDate = $boundaries[0];
        $endingDate = $boundaries[1];

        $result = $this->query("SELECT * FROM Attendance 
            WHERE codFisc='$CodFisc' AND date > '$beginningDate' AND date< '$endingDate'");

        if (!$result) {
            $this->rollback();
            die("Unable to select attendance for student $CodFisc");
        }

        $attendance = array();

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {

            /**
             * Modify data to simplify them
             * Produces an array as
             * "YYYY-MM-DD" => "absent" | "early - hh:mm" | "late - hh:mm"
             * */

            if ($row["absence"] == 1 && $row["lateEntry"] == 0 && $row["earlyExit"] == 0) {
                // the student was absent that day
                $value = "Absent";
            } elseif ($row["lateEntry"] != 0 && $row["earlyExit"] != 0) {
                // student both entered late and exited early
                $value = "late - Entered: " . strval($row["lateEntry"]) . "° hour Exited: " . strval($row["earlyExit"]) . "° hour";
            } elseif ($row["lateEntry"] != 0) {
                //entered late
                $value = "late - Entered: " . strval($row["lateEntry"]) . "° hour";
            } else {
                //no late entry,neither absence => exited early
                $value = "early - Exited: " . strval($row["earlyExit"]) . "° hour";
            }
            $attendance[$row["date"]] = $value;
        }

        $this->commit();

        return $attendance;
    }



    public function viewChildAssignments($codFisc)
    {
        /*Retrieves all assignment of the selected child*/

        $CodFisc = $this->sanitizeString($codFisc);

        $this->begin_transaction();


        /* Verify if the user logged in is actually allowed to see the assignments of the requested child */
        $result = $this->query("SELECT * FROM Students WHERE codFisc='$codFisc';");

        if (!$result)
            die("Unable to select student $codFisc");

        if (($row = $result->fetch_array(MYSQLI_ASSOC)) == NULL) {
            die("No student with ID $CodFisc ");
        }

        $class = $this->getChildClass($codFisc);

        $parent1 = $row['emailP1'];
        $parent2 = $row['emailP2'];

        if ($_SESSION['user'] != $parent1 && $_SESSION['user'] != $parent2)
            die("You are not authorised to see this information.");


        $year = intval(date("Y"));
        $month = intval(date("m"));

        if ($month <= 7) {
            // second semester
            $year = $year - 1;
        }

        $beginningDate = $year . "-08-01";

        $year = $year + 1;
        $endingDate = $year . "-07-31";

        $result = $this->query("SELECT subject,date,textAssignment FROM Assignments WHERE classID='$class' AND date > '$beginningDate' AND date< '$endingDate' ORDER BY subject ASC, date DESC;");

        if (!$result) {
            $this->rollback();
            die("Unable to select assignments for student $CodFisc");
        }

        $assignments = array();

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {

            /**
             * Modify data to simplify them
             * Produces an array as
             * "YYYY-MM-DD" => "" | "View assignments"
             * */

            $value = "View assignments:" . $row["textAssignment"];
            if (array_key_exists($row["date"], $assignments))
                $assignments[$row["date"]] = $assignments[$row["date"]] . "~" .  $row["subject"] . ":" . $value;
            else
                $assignments[$row["date"]] = $row["subject"] . ":" . $value;
        }

        $this->commit();

        return $assignments;
    }



    public function getChildClass($codFisc)
    {
        //returns the classId of a student

        $CodFisc = $this->sanitizeString($codFisc);

        $result = $this->query("SELECT classID FROM Students WHERE codFisc='$codFisc';");
        if (!$result)
            die("Unable to select class of $codFisc");

        if (($row = $result->fetch_array(MYSQLI_ASSOC)) == NULL) {
            die("No class for student with ID $codFisc ");
        } else
            return $row['classID'];
    }

    public function retrieveChildTimetable($class)
    {

        // returns the timetable of a certain class in the form | hour, mon, tue, wed, thu, fri |

        $class = $this->sanitizeString($class);
        $timetableToReturn = array();

        $result = $this->query("SELECT * FROM Timetable WHERE class='$class'");

        if (!$result)
            die("Unable to select timetable for class $class");

        if ($result->num_rows > 0) {
            while ($lecture = $result->fetch_assoc()) {
                // Store the row with the format: $timetableToReturn[1]["mon"] = "Math"
                // $lecture[0] = class
                $day = $lecture["day"];
                $hour = $lecture["hour"];
                $subject = $lecture["subject"];
                $timetableToReturn[$hour][$day] = $subject;
            }
        }

        return $timetableToReturn;
    }
}

class dbTeacher extends db
{

    function __construct()
    {
        if ($_SESSION['role'] != "teacher") throw new Exception("Creating DbTeacher object for an user who is NOT logged in as a teacher");
        parent::__construct();
    }

    function getStudentsName($codfisc)
    {

        $codfisc = $this->sanitizeString($codfisc);

        $result = $this->query("SELECT * FROM Students WHERE codFisc='$codfisc'");

        if (!$result)
            die("Unable to select student.");

        $student = "";
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($row != NULL)
            $student = $row['surname'] . " " . $row['name'];

        return $student;
    }

    function insertSupportMaterial($title, $filename, $dimension, $class, $subject)
    {
        return $this->query("INSERT INTO supportmaterials VALUES(CURRENT_TIMESTAMP, '$title', '$filename', '$dimension', '$class', '$subject')");
    }

    function insertGrade($date, $hour, $student, $subject, $grade)
    {

        $student = $this->sanitizeString($student);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $hour = $this->sanitizeString($hour);
        $grade = $this->sanitizeString($grade);

        $result = $this->query("SELECT * FROM Marks WHERE (codFisc='$student' AND hour='$hour' AND date='$date')");

        if ($result->num_rows > 0) {
            return -1;
        }

        $result = $this->query("INSERT INTO Marks(codFisc, subject, date, hour, mark) 
								VALUES ('$student', '$subject', '$date', '$hour', '$grade')");

        if (!$result) {
            die("ERROR: Mark not inserted.");
        }
    }


    function getGradesByTeacher($codfisc)
    {
        $codfisc = $this->sanitizeString($codfisc);

        $result = $this->query("SELECT s.classId, s.codFisc, s.name, s.surname, m.subject, m.date, m.hour, m.mark 
        FROM Marks m, TeacherClassSubjectTable tcs, Students s 
        WHERE tcs.codFisc = '$codfisc' AND tcs.subject = m.subject AND s.codFisc = m.codFisc AND tcs.classID = s.classID ORDER BY m.date DESC");

        if (!$result)
            die("Unable to load marks.");

        if ($result->num_rows > 0) {
            $marks = array();
            while ($row = $result->fetch_assoc()) {
                array_push($marks,  "" . $row['classId'] . "," . $row['codFisc'] . "," . $row['surname'] . "," . $row['name'] . "," . $row['subject'] . "," . $row['date'] . "," . $row['hour'] . "," . $row['mark'] . "");
            }
            return $marks;
        }
    }

    function updateMark($codFisc, $subject, $date, $hour, $grade)
    {
        $codFisc = $this->sanitizeString($codFisc);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $hour = $this->sanitizeString($hour);
        $grade = $this->sanitizeString($grade);

        $result = $this->query("UPDATE marks SET mark='$grade' 
							WHERE subject='$subject' AND date='$date' AND hour='$hour' AND codFisc='$codFisc'");

        if (!$result) {
            die("ERROR: Mark not updated.");
        }
    }


    function deleteMark($codFisc, $date, $hour, $subject)
    {
        $codFisc = $this->sanitizeString($codFisc);
        $date = $this->sanitizeString($date);
        $hour = $this->sanitizeString($hour);
        $subject = $this->sanitizeString($subject);

        $result = $this->query("DELETE FROM Marks WHERE date='$date' AND hour='$hour' AND codFisc='$codFisc' AND subject='$subject'");

        if (!$result) {
            die("ERROR: Mark not deleted.");
        }

        return 0;
    }

    function insertDailyLesson($date, $hour, $class, $codTeacher, $subject, $topics)
    {

        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $hour = $this->sanitizeString($hour);
        $topics = $this->sanitizeString($topics);
        $codTeacher = $this->sanitizeString($codTeacher);

        $result = $this->query("SELECT * FROM Lectures WHERE (classID='$class' AND hour='$hour' AND date='$date')");

        if ($result->num_rows > 0) {
            return -1;
        }

        $result = $this->query("INSERT INTO Lectures(date, hour, classID, codFiscTeacher, subject, topic) 
								VALUES ('$date', '$hour', '$class', '$codTeacher', '$subject', '$topics')");

        if ($result == FALSE) {
            die("ERROR: Lecture not inserted.");
        }
    }

    function updateDailyLesson($date, $hour, $class, $subject, $topics)
    {

        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $hour = $this->sanitizeString($hour);
        $topics = $this->sanitizeString($topics);


        $result = $this->query("UPDATE Lectures SET subject='$subject', topic='$topics' 
							WHERE date='$date' AND hour='$hour' AND classID='$class'");

        if ($result == FALSE) {
            die("ERROR: Lecture not updated.");
        }
    }

    function deleteDailyLesson($date, $hour, $class)
    {
        $date = $this->sanitizeString($date);
        $hour = $this->sanitizeString($hour);
        $class = $this->sanitizeString($class);

        $result = $this->query("DELETE FROM Lectures WHERE date='$date' AND hour='$hour' AND classID='$class'");

        if ($result == FALSE) {
            die("ERROR: Lecture not deleted.");
        }
    }

    function getClassesByTeacher2($codTeacher)
    {
        $codTeacher = $this->sanitizeString($codTeacher);
        $result = $this->query("SELECT DISTINCT classID FROM TeacherClassSubjectTable WHERE codFisc='$codTeacher' ORDER BY classID");

        if (!$result)
            die("Unable to select classes.");


        if ($result->num_rows > 0) {

            $classes = array();
            while ($row = $result->fetch_assoc()) {
                array_push($classes, $row["classID"]);
            }
            return $classes;
        }
    }

    //tested
    function getSubjectsByTeacherAndClass2($codTeacher, $class)
    {
        $codTeacher = $this->sanitizeString($codTeacher);
        $class = $this->sanitizeString($class);

        $result = $this->query("SELECT DISTINCT subject FROM TeacherClassSubjectTable WHERE (classID='$class' AND codFisc='$codTeacher')");

        if (!$result)
            die("Unable to select subjects.");

        if ($result->num_rows > 0) {
            $subjects = array();
            while ($row = $result->fetch_assoc()) {
                array_push($subjects, $row["subject"]);
            }
            return $subjects;
        }
    }

    //tested
    function getLecturesByTeacher($codTeacher)
    {
        $codTeacher = $this->sanitizeString($codTeacher);

        $result = $this->query("SELECT * FROM Lectures WHERE codFiscTeacher='$codTeacher' ORDER BY date DESC");

        if (!$result)
            die("Unable to select lectures.");

        if ($result->num_rows > 0) {
            $lectures = array();
            while ($row = $result->fetch_assoc()) {
                array_push($lectures,  "" . $row['classID'] . "," . $row['subject'] . "," . $row['date'] . "," . $row['hour'] . "," . $row['topic'] . "");
            }
            return $lectures;
        }
    }

    //tested
    function getAssignments($codTeacher)
    {

        $codTeacher = $this->sanitizeString($codTeacher);

        $result = $this->query("SELECT * FROM Assignments a, TeacherClassSubjectTable t WHERE t.codFisc='$codTeacher' AND t.subject = a.subject ORDER BY date DESC");

        if (!$result)
            die("Unable to select assignments.");

        if ($result->num_rows > 0) {
            $assignments = array();
            while ($row = $result->fetch_assoc()) {
                array_push($assignments,  "" . $row['classID'] . "," . $row['subject'] . "," . $row['date'] . "," . $row['textAssignment'] . "");
            }
            return $assignments;
        }
    }

    //tested
    function insertNewAssignments($date, $class, $codTeacher, $subject, $assignments)
    {
        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $assignments = $this->sanitizeString($assignments);
        $codTeacher = $this->sanitizeString($codTeacher);

        $result = $this->query("SELECT * FROM Assignments WHERE (classID='$class' AND subject='$subject' AND date='$date')");

        if ($result->num_rows > 0) {
            return -1;
        }

        $result = $this->query("INSERT INTO Assignments(subject, date, classID, textAssignment) 
								VALUES ('$subject', '$date', '$class', '$assignments')");

        if ($result == FALSE) {
            die("ERROR: Assignments not inserted.");
        }
    }

    //tested
    function updateAssignments($date, $class, $subject, $assignments)
    {

        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $assignments = $this->sanitizeString($assignments);

        $result = $this->query("UPDATE Assignments SET textAssignment='$assignments'
							WHERE date='$date' AND subject='$subject' AND classID='$class'");

        if ($result == FALSE) {
            die("ERROR: Assignments not updated.");
        }
    }

    //tested
    function deleteAssignments($date, $subject, $class)
    {

        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);

        $result = $this->query("DELETE FROM Assignments WHERE date='$date' AND subject='$subject' AND classID='$class'");

        if ($result == FALSE) {
            die("ERROR: Assignments not deleted.");
        }
    }

    function retrieveAssignments($codFisc)
    { }

    //tested
    function getStudentsByClass2($class)
    {

        $class = $this->sanitizeString($class);

        $result = $this->query("SELECT * FROM Students WHERE classID='$class'");

        if (!$result)
            die("Unable to select students");

        if ($result->num_rows > 0) {

            $students = array();
            while ($row = $result->fetch_assoc()) {
                array_push($students, "" . $row['name'] . "," . $row['surname'] . "," . $row['codFisc'] . "");
            }
            return $students;
        }
    }

    /**
     * This function makes a student absent for a particular day
     */
    //tested
    function updateAttendance($ssn, $day)
    {
        $this->begin_transaction();
        //$ssn1 = $ssn;
        $count = -1;
        $absence = -1;
        $lateEntry = -1;
        $earlyExit = -1;

        $stmt = $this->prepareStatement("SELECT COUNT(*),`absence`,`lateEntry`,`earlyExit` FROM `Attendance` WHERE `codFisc`= ? AND `date`= ?");
        try {
            // the parameter is bound to the first '?' in the upper query
            if (!$stmt->bind_param("ss", $ssn, $day))
                throw new Exception("Binding Failed in the Transaction.");
            // the result is characterized by four coloumns and needs to be bounded to corresponding variables
            if (!$stmt->bind_result($count, $absence, $lateEntry, $earlyExit))
                throw new Exception("Binding result Failed in the Transaction.");

            // The statement is excecuted but the variables do not contain the results
            if (!$stmt->execute())
                throw new Exception("Select Failed.");

            $row = $stmt->fetch();

            if ($count == 0) {
                // THE STUDENT HAS NO RECORD IN THE TABLE, IT MEANS THAT WAS CONSIDERED PRESENT UNTIL THAT MOMENT.
                // SO AN INSERT SHOULD BE DONE
                //close the previous statement
                $stmt->close();
                $absence = 1;
                $earlyExit = 0;
                $lateEntry = 0;
                $stmt = $this->prepareStatement("INSERT INTO `Attendance`(`date`,`codFisc`, `absence`, `earlyExit`, `lateEntry`) VALUES (?,?,?,?,?)");

                if (!$stmt->bind_param("ssiii", $day, $ssn, $absence, $earlyExit, $lateEntry))
                    die("Binding Failed in the Transaction.");

                // The statement is excecuted but the variables do not contain the results
                if (!$stmt->execute())
                    throw new Exception("Insert Attendance Failed.");
                $stmt->close();
                $this->commit();
                return true;
            } else {
                // THE STUDENT HAS ALREADY A RECORD, IT MEANS THAT THE STUDENT HAS ALREADY TRIGGERED THE SYSTEM WITH A LATE ENTRANCE OR AN EARLY EXIT
                // SO AN UPDATE SHOULD BE DONE OR THE PROF WOULD LIKE TO REMOVE THE ABSENCE

                //IF THE ABSENCE SHOULD BE REMOVED THEN 'absence' should be already set to 1.
                //ELSE THE ABSENCE SHOULD BE ADDED SO THE QUERY UPDATED


                if ($absence == 1 && $lateEntry == 0 && $earlyExit == 0) {
                    //THE ABSENCE SHOULD BE REMOVED
                    //close the previous statement
                    $stmt->close();

                    $stmt = $this->prepareStatement("DELETE FROM `Attendance` WHERE `codFisc`= ? AND `date`=?");

                    if (!$stmt->bind_param("ss", $ssn, $day))
                        die("Binding Failed in the Transaction.");

                    // The statement is excecuted but the variables do not contain the results
                    if (!$stmt->execute())
                        throw new Exception("Delete Attendance Failed.");

                    if ($stmt->affected_rows == 0)
                        throw new Exception("Delete Attendance Failed.");
                    $stmt->close();
                } else {
                    //THE ABSENCE SHOULD BE ADDED SO THE QUERY UPDATED
                    //close the previous statement
                    $stmt->close();

                    if (!($lateEntry == 0 && $earlyExit == 0))
                        throw new Exception("The student has some information recorded so this field should not be updated anymore");
                }
            }
            $this->commit();
            return true;
        } catch (Exception $e) {
            //echo $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    //tested except exceptions
    function checkAbsenceEarlyExitLateEntrance($ssn, $day)
    {
        $date = "";
        $codFisc = "";
        $absence = -1;
        $earlyExit = -1;
        $lateEntry = -1;

        $ssn = $this->sanitizeString($ssn);
        $day = $this->sanitizeString($day);

        $stmt = $this->prepareStatement("SELECT `date`, `codFisc`, `absence`, `earlyExit`, `lateEntry` FROM `Attendance` WHERE `codFisc` = ? AND `date` = ?");

        if (!$stmt->bind_param("ss", $ssn, $day))
            throw new Exception("Binding Failed.");

        if (!$stmt->bind_result($date, $codFisc, $absence, $earlyExit, $lateEntry))
            throw new Exception("Binding Failed.");

        try {
            if (!$stmt->execute())
                throw new Exception("Select Failed.");

            $row = $stmt->fetch();
            $result = array($date, $codFisc, $absence, $lateEntry, $earlyExit);

            $stmt->close();

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    function recordLateEntryQUERY($date, $ssn, $hour)
    {
        return $this->query("UPDATE `Attendance` SET `absence`=0, `lateEntry`='$hour' WHERE `date`='$date' AND `codFisc`='$ssn'");
    }

    function recordLateEntryQUERYWithoutChangingAbsence($date, $ssn, $hour)
    {
        return $this->query("UPDATE `Attendance` SET `lateEntry`='$hour' WHERE `date`='$date' AND `codFisc`='$ssn'");
    }

    function recordEarlyExitQUERY($date, $ssn, $hour)
    {
        return $this->query("INSERT INTO `Attendance`(`date`,`codFisc`, `absence`, `earlyExit`, `lateEntry`) VALUES ('$date', '$ssn', 1, '$hour', 0)");
    }

    function recordEarlyExitHavingAlreadyLateEntryQUERY($date, $absence, $ssn, $hour)
    {
        return $this->query("UPDATE `Attendance` SET `absence`=$absence, `earlyExit`='$hour' WHERE `date`='$date' AND `codFisc`='$ssn'");
    }

    function selectAttendanceStudent($date, $ssn)
    {
        return $this->query("SELECT * FROM `Attendance` WHERE `date`='$date' AND `codFisc`='$ssn'");
    }
    function deleteUselessRowAttendance($date, $ssn)
    {
        return $this->query("DELETE FROM `Attendance` WHERE `date`= '$date' AND `codFisc` = '$ssn'");
    }


    function recordLateEntrance($day, $ssn, $hour)
    {

        // CONTROLLO SE L'ALUNNO C'E' GIA' NEL DB NELLA TABELLA attendance (tramite SELECT)
        // C'E' => TUTTO OK, CONTINUO
        // NON C'E' => VUOL DIRE CHE E' UNO DEGLI ALUNNI PRESENTI => ERRORE, return false

        // VISTO CHE HO TROVATO LA RIGA NEL DB, CONTROLLO SE IL CAMPO absence = 1
        // = 1 => TUTTO OK, CONTINUO
        // = 0 => HO GIA' SEGNATO L'ENTRATA => ERRORE, return false

        // FACCIO UPDATE DELLA ENTRY DELLA TABELLA attendance E METTO absence=0, lateEntry='hour'
        // return true

        $day = $this->sanitizeString($day);
        $ssn = $this->sanitizeString($ssn);
        $hour = $this->sanitizeString($hour);

        $this->begin_transaction();
        try {
            //Verifico se ci sono tuple relative allo studente
            $result = $this->selectAttendanceStudent($day, $ssn);

            if (!$result) {
                throw new Exception("Problem with select query.");
            }

            //Lo studente deve essere assente
            if ($result->num_rows != 1)
                throw new Exception("Student absence is not recorded.");

            $attendance = $result->fetch_assoc();

            $absence = $attendance['absence'];

            // if ($absence != 1)
            //     throw new Exception("The student should be absent.");

            //Se è stata settata un uscita e l'ora di uscita < ora entrata
            if ($attendance['earlyExit'] != 0 && $attendance['earlyExit'] < $hour && $hour != 0)
                throw new Exception("Integrity violeted.");

            if ($attendance["earlyExit"] != 0) {
                //UN Uscita è stata registrata
                // Stai registrando un entrata che sarà per forza minore dell'uscita 
                $result1 = $this->recordLateEntryQUERYWithoutChangingAbsence($day, $ssn, $hour);
                if (!$result1)
                    throw new Exception();
            } else {
                //Nessuna uscita è stata registrata
                $result2 = $this->recordLateEntryQUERY($day, $ssn, $hour);
                if (!$result2)
                    throw new Exception();
            }

            //check se la tupla non ha informazioni utili allora deve essere rimossa 
            $check = $this->selectAttendanceStudent($day, $ssn);

            $row = $check->fetch_assoc();

            if ($row['absence'] == 0 && $row['earlyExit'] == 0 && $row['lateEntry'] == 0) {
                $result3 = $this->deleteUselessRowAttendance($day, $ssn);
            }


            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }

    function recordEarlyExit($day, $ssn, $hour)
    {

        // CONTROLLO SE L'ALUNNO C'E' GIA' NEL DB NELLA TABELLA attendance (tramite SELECT)
        // C'E' => CHECK SU absence => absence = 0  => STUDENTE PRESENTE, MA CHE E' ENTRATO IN RITARDO 
        //                                          => TUTTO OK, FACCIO UPDATE METTENDO absence=1 e earlyExit='hour'
        //                                          => return true
        //                             absence = 1  => ERRORE, return false
        // NON C'E' => VUOL DIRE CHE L'ALUNNO E' PRESENTE => TUTTO OK, FACCIO INSERT CON absence=1 e earlyExit='hour'
        // return true


        $day = $this->sanitizeString($day);
        $ssn = $this->sanitizeString($ssn);
        $hour = $this->sanitizeString($hour);


        $this->begin_transaction();


        try {
            $result = $this->selectAttendanceStudent($day, $ssn);

            if (!$result)
                throw new Exception();

            if ($result->num_rows == 1) {
                //se esiste allora ha gia' un evento registrato early entrance ad esempio
                $row = $result->fetch_assoc();

                $absence = $row['absence'];
                $lateEntry = $row["lateEntry"];
                $earlyExit = $row["earlyExit"];

                // //lo studente deve risultare presente
                if ($absence != 0 && $earlyExit == 0)
                    throw new Exception("Student should be present.");

                if ($row['lateEntry'] != 0 && $row['lateEntry'] > $hour && $hour != 0)
                    throw new Exception("Integrity violeted.");


                if ($hour != 0) {
                    $absence = 1;
                    $result1 = $this->recordEarlyExitHavingAlreadyLateEntryQUERY($day, $absence, $ssn, $hour);
                    if (!$result1)
                        throw new Exception();
                } else {
                    //HOUR = 0 => non è più uscito => presente
                    $absence = 0;
                    $result1 = $this->recordEarlyExitHavingAlreadyLateEntryQUERY($day, $absence, $ssn, $hour);
                    if (!$result1)
                        throw new Exception();
                }
            } else if ($result->num_rows == 0) {
                //non è stato registrato alcun evento quindi lo studente è presente
                $result2 = $this->recordEarlyExitQUERY($day, $ssn, $hour);

                if (!$result2)
                    throw new Exception("recordEarlyExitQUERY failed.");
            } else {
                throw new Exception("Integrity violeted.");
            }


            //check se la tupla non ha informazioni utili allora deve essere rimossa 
            $check = $this->selectAttendanceStudent($day, $ssn);

            $row = $check->fetch_assoc();

            if ($row['absence'] == 0 && $row['earlyExit'] == 0 && $row['lateEntry'] == 0) {
                $result3 = $this->deleteUselessRowAttendance($day, $ssn);
            }


            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }

    //tested
    public function viewStudentMarks($CodFisc, $subject)
    {

        $CodFisc = $this->sanitizeString($CodFisc);
        $subject = $this->sanitizeString($subject);


        $result = $this->query("SELECT * FROM Marks WHERE codFisc='$CodFisc' AND subject='$subject' ORDER BY date DESC;");

        if (!$result) {
            die("Unable to select marks for student $CodFisc");
        }

        if ($result->num_rows > 0) {
            $marks = array();
            while ($row = $result->fetch_assoc()) {
                array_push($marks,  "" . $row['date'] . "," . $row['mark'] . "," . $row['hour'] . "");
            }
            return $marks;
        }
    }
}
