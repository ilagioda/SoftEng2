<?php

require_once("functions.php");

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
        try {
            $this->conn = new mysqli($servername, $username, $password, $dbname);
        } catch (Exception $e) {
            $servername = "127.0.0.1";
            $this->conn = new mysqli($servername, $username, $password, $dbname);
        }

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
     * Test methods
     */

    public function queryForTesting($query)
    {
        return $this->query($query);
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
        // tested

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
        // TESTED

        /**
         * Retrieves the password of a certain user, if any.
         * @param $user (String) email of a parent or CodFisc in case of Teacher (Principal or not) or Admin (sysAdmin or not)
         * @return (Array) Returns associative array corresponding to the user found, if any.
         */

        $sql = "SELECT * FROM Parents WHERE email='$user'";
        $sql2 = "SELECT * FROM Teachers WHERE codFisc='$user'";
        $sql3 = "SELECT * FROM Admins WHERE codFisc='$user'";
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
        /**
         * Changes the password of a given user.
         * @param $user (String) email of a parent or CodFisc in case of Teacher (Principal or not) or Admin (sysAdmin or not)
         * @param $hashedPassword (String) hashed password of a certain user 
         * @param $table (String) table in which user password should be updated
         * @param $first_time (bool) value used to know if the password is being changed by the "sendmail.php"(false) file or when logging in for the first time (true)
         * @return (bool) 
         */

        $user = $this->sanitizeString($user);

        if ($first_time)
            return $this->query("UPDATE $table SET hashedPassword = '$hashed_pw', firstLogin=0 WHERE email='$user'");

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
        // tested

        /**
         * Read class composition for a given classID
         * @param String $classID: the classID on which we are interested in
         * @return Array $codFisc, $name, $surname, $class
         */

        $codFisc = "";
        $name = "";
        $surname = "";
        $class = "";

        $compositionVector = array();
        // a prepared statement needs to be configured
        $stmt = $this->prepareStatement(
            "SELECT s.`codFisc`,`name`,`surname`,p.`classID` 
        FROM `Students` as s , `ProposedClasses` as p 
        WHERE s.codFisc = p.codFisc AND p.classID = ?"
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
            while ($stmt->fetch()) {
                $compositionVector[$i++] = array($codFisc, $name, $surname, $class);
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
        // TESTED

        /**
         * Creates entry in db of an official account (Teacher, Principal, Admin or SysAdmin), checking if a Principal is already present in it.
         * @param $who (String) Table in which the entry must be inserted (role + "s"). Supports only Teachers and Admins
         * @param $SSN (String) SSN of Teacher (Principal or not) or Admin (sysAdmin or not)
         * @param $hashedPw (String) hashed password of a certain user 
         * @param $name (String) Name of the user
         * @param $surname (String) Surname of the user
         * @param $rights (bool) tells wheter the user has the rights to be a SysAdmin(1) or a Principal(1) or if he/she is just an Admin(0) or a Teacher(0)
         * @return (bool) 
         */

        if ($who !== "Teachers" && $who !== "Admins") return false;
        if ($rights !== 0 && $rights !== 1) return false;

        $SSN = $this->sanitizeString($SSN);
        $name = $this->sanitizeString($name);
        $surname = $this->sanitizeString($surname);

        $this->begin_transaction();

        if ($who == "Teachers" && $rights == 1) {
            // If the admin wants to insert a principal, check that there is not another one
            // If there is already a principal, rollback and return false;

            $sel = $this->query("SELECT COUNT(*) as PRIN_NUM FROM $who WHERE principal=1");
            $sel = $sel->fetch_assoc();
            if (!$sel || $sel["PRIN_NUM"] != 0) {
                $this->rollback();
                return false;
            }
        }


        $sql = "INSERT INTO $who VALUES('$SSN', '$hashedPw', '$name', '$surname', '$rights')";
        $this->query($sql);
        return $this->commit();
    }


    function insertCommunication($title, $text)
    {
        //TESTED 

        /**
         * Inserts an official communication incrementing the ID.
         * @param $title(String) Title of the communication
         * @param $text (String) Text of the communication
         * @return (bool) 
         */

        $title = $this->sanitizeString($title);
        $text = $this->sanitizeString($text);

        $res = $this->query("SELECT MAX(ID) as oldID FROM Announcements");

        if (!$res) return false;

        $res = $res->fetch_assoc();

        $newID = $res['oldID'] + 1;
        return $this->query("INSERT INTO Announcements VALUES('$newID', CURRENT_TIMESTAMP, '$title', '$text')");
    }

    function readAllClassCompositions()
    {
        // TESTED 

        $sql = "SELECT DISTINCT classID FROM ProposedClasses";

        $resultQuery = $this->query($sql);

        $resultArray = array();

        if ($resultQuery->num_rows > 0) {
            // output data of each row
            while ($row = $resultQuery->fetch_assoc()) {
                array_push($resultArray, $row["classID"]);
            }
        }
        return $resultArray;
    }

    public function getClasses()
    {
        return $this->query("SELECT classID FROM Classes ORDER BY classID");
    }

    public function insertInternalCommunication($class, $title, $text)
    {

        $res = $this->query("SELECT MAX(ID) as oldID FROM internalCommunications");

        if (!$res) {
            return false;
        }

        $res = $res->fetch_assoc();

        $newID = $res['oldID'] + 1;

        $class = $this->sanitizeString($class);
        $title = $this->sanitizeString($title);
        $text = $this->sanitizeString($text);

        return $this->query("INSERT INTO internalCommunications VALUES('$newID', '$class', CURRENT_TIMESTAMP, '$title', '$text') ");
    }
    //NEEDS TO BE TESTED
    /**
     * This function has the aim of removing the information about a teacher in tables Teachers and TeacherClassSubjectTable
     * The remove is possible if:
     *  1) He is not a coordinator
     *  2) There is another professor that can substitute the one that is removed in the same class for the same subject.
     *  3) 
     * 
     */
    public function deleteTeacher($ssn)
    {

        $ssn = $this->sanitizeString($ssn);

        //WE NEED TO CANCEL THE INFORMATION ABOUT A TEACHER IF AND ONLY IF:
        /*
        1) He is not a coordinator
        2) There is another professor that can substitute the one that is removed in the same class for the same subject.
        3) 
        */
        // Check if he is a coordinator
        //$coordinatorCheck = $this->query("SELECT * FROM Classes WHERE coordinatorSSN = '$ssn'");
        //$coordinatorCheck = $coordinatorCheck->num_rows;
        //if ($coordinatorCheck == 0) {
        // The teacher is not a coordinator
        // Check if he is the only one to teach that particular subject in that particular class
        // First you have to retrieve all the subjects teached by that particular teacher from TeacherClassSubjectTable
        $this->begin_transaction();

        $list_class_subject = $this->query("SELECT `classID`, `subject` FROM `TeacherClassSubjectTable` WHERE `codFisc` = '$ssn' ");


        foreach ($list_class_subject as $class_subject) {

            // For each class and subject we have to check if there is at least another prof that teaches that subject
            $numberOfTeacherThatTeachesSubjectInClass = $this->query("SELECT * FROM `TeacherClassSubjectTable` WHERE `codFisc` <> '$ssn' AND `classID` = '$class_subject[classID]' AND `subject` = '$class_subject[subject]'");
            $numberOfTeacherThatTeachesSubjectInClass = $numberOfTeacherThatTeachesSubjectInClass->num_rows;
            if ($numberOfTeacherThatTeachesSubjectInClass == '0') {
                // canBeRemoved = false;
                $this->rollback();
                return false;
            }
        }

        // The teacher can be removed: Teachers , TeacherClassSubjectTable, 

        if ($this->query("DELETE FROM `TeacherClassSubjectTable` WHERE `codFisc` = '$ssn'") && $this->query("DELETE FROM `Teachers` WHERE `codFisc` = '$ssn'") &&  $this->query("DELETE FROM `Classes` WHERE `coordinatorSSN` = '$ssn'")) {
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
        /*         } else {
            $this->rollback();
            return false;
        } */
    }

    //NEEDS TO BE TESTED
    /**
     * The aim of deleteSubjectTeachedInAClassByATeacher is to remove the information about the subject that is teached by a teacher in a particular class
     * 
     */
    public function deleteSubjectTeachedInAClassByATeacher($ssn, $classID, $subject)
    {

        $ssn = $this->sanitizeString($ssn);
        $classID = $this->sanitizeString($classID);
        $subject = $this->sanitizeString($subject);

        $this->begin_transaction();

        $result = $this->query("SELECT COUNT(*) as CNT FROM `TeacherClassSubjectTable` WHERE `codFisc`<> '$ssn' AND `classID` = '$classID' AND `subject` = '$subject'");

        $row = mysqli_fetch_assoc($result);

        if ($row["CNT"] == "0") {
            $this->rollback();
            return false;
        } else {
            //Remove the subject and the class to the professor
            if ($this->query("DELETE FROM `TeacherClassSubjectTable` WHERE `codFisc` = '$ssn'  AND `classID` = '$classID' AND `subject` = '$subject'")) {
                $this->commit();
                return true;
            }else{
                $this->rollback();
                return false;
            }
        }
    }
    //NEEDS TO BE TESTED
    public function getTeachers()
    {
        return $this->query('SELECT * FROM Teachers');
    }
    //NEEDS TO BE TESTED
    public function getClassSubject($ssn)
    {
        $ssn = $this->sanitizeString($ssn);
        $query = "SELECT * FROM TeacherClassSubjectTable WHERE codFisc='$ssn'";
        return $this->query($query);
    }
    //NEEDS TO BE TESTED
    public function getSubjects()
    {
        return $this->query("SELECT name FROM Subjects");
    }


    /**
     * This function has the aim to update the class of the students for which the class composition has been accepted.
     * @param $vectorCodFiscNameSurnameClass vector in which two important values are recorded:
     *  $codFisc = $value[0];
     *  $classID = $value[3];
     * 
     * @return true if successed else @return false;
     * 
     */
    function updateStudentsClass($vectorCodFiscNameSurnameClass)
    {

        // TESTED 
        $this->begin_transaction();

        $stmt = $this->prepareStatement("UPDATE `Students` SET `classID`= ? WHERE `codFisc` = ? ");
        foreach ($vectorCodFiscNameSurnameClass as $value) {
            $codFisc = $value[0];
            $classID = $value[3];

            if (!$stmt->bind_param("ss", $classID, $codFisc)) {
                $this->rollback();
                return false;
            }

            if (!$stmt->execute()) {
                $this->rollback();
                return false;
            }

            if (($stmt->affected_rows) != 1) {
                $this->rollback();
                return false;
            }
        }
        /* close statement */
        $stmt->close();

        $stmt = $this->prepareStatement("DELETE FROM `ProposedClasses` WHERE `classID` = ?");
        if (!$stmt->bind_param("s", $classID)) {
            $this->rollback();
            return false;
        }

        if (!$stmt->execute()) {
            $this->rollback();
            return false;
        }

        if (($stmt->affected_rows) == 0) {
            $this->rollback();
            return false;
        }

        $this->commit();

        return true;
    }


    public function TakeParentsMail()
    {
        // NO NEED TO BE TESTED
        return $this->query("SELECT email, hashedPassword, firstLogin FROM Parents ORDER BY hashedPassword, email");
    }

    public function insertStudent($name, $surname, $SSN, $email1, $email2)
    {
        // NO NEED TO BE TESTED
        $name = $this->sanitizeString($name);
        $surname = $this->sanitizeString($surname);
        $SSN = $this->sanitizeString($SSN);
        $email1 = $this->sanitizeString($email1);
        $email2 = $this->sanitizeString($email2);

        return $this->query("INSERT INTO Students(codFisc, name, surname, emailP1, emailP2, classID) VALUES ('$SSN','$name','$surname','$email1','$email2', '')");
    }

    public function insertParent($name, $surname, $SSN, $email)
    {
        // NO NEED TO BE TESTED

        $name = $this->sanitizeString($name);
        $surname = $this->sanitizeString($surname);
        $SSN = $this->sanitizeString($SSN);
        $email = $this->sanitizeString($email);

        return $this->query("INSERT INTO Parents(email, hashedPassword, name, surname, codFisc, firstLogin) VALUES ('$email', '','$name','$surname','$SSN', 1)");
    }

    public function insertLectures($date, $hour, $classID, $codFiscTeacher, $subject, $topic)
    {
        // NO NEED TO BE TESTED

        $date = $this->sanitizeString($date);
        $hour = $this->sanitizeString($hour);
        $classID = $this->sanitizeString($classID);
        $codFiscTeacher = $this->sanitizeString($codFiscTeacher);
        $subject = $this->sanitizeString($subject);
        $topic = $this->sanitizeString($topic);

        return $this->query("INSERT INTO Lectures(date, hour, classID, codFiscTeacher, subject, topic) 
								VALUES('$date', '$hour', '$classID', '$codFiscTeacher', '$subject', '$topic')");
    }

    //tested
    public function enrollStudent($name, $surname, $SSN, $name1, $surname1, $SSN1, $email1, $name2 = '', $surname2 = '', $SSN2 = '', $email2 = '')
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
            return false;
        }


        /* Insert parent 1 into the DB */
        $result = $this->insertParent($name1, $surname1, $SSN1, $email1);
        // if(!$result){
        //     $this->rollback();    
        //     return 0;
        // }

        if (!empty($name2) && !empty($surname2) && !empty($SSN2) && !empty($email2)) {
            /* Insert parent 2 into the DB */
            $result = $this->insertParent($name2, $surname2, $SSN2, $email2);
            // if(!$result){
            //     $this->rollback();
            //     return 0;
            // }
        }

        return $this->commit();
    }

    //TESTED
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

    //TESTED
    public function storeTimetable($class, $timetable)
    {
        $class = $this->sanitizeString($class);

        $this->begin_transaction();

        // Check inconsistencies (e.g. the timetable has at least a lesson with a teacher that shouldn't be there because
        // he/she has already another lesson in another class at that time)
        foreach ($timetable as $line) {

            // Retrieve the fields
            $day = $line[0];
            $hour = $line[1];
            $subject = $line[2];

            if ($subject === "-")
                continue;

            // Sanitize
            $day = $this->sanitizeString($day);
            $hour = $this->sanitizeString($hour);
            $subject = $this->sanitizeString($subject);

            $checkResult = $this->checkIfTeacherHasLesson($day, $hour, $subject, $class);
            if (!$checkResult) {
                $this->rollback();
                return 0;
            }
        }

        // Ckeck if a timetable for the chosen class is already present in the DB
        $result = $this->query("SELECT * FROM Timetable WHERE classID='$class'");
        if (!$result) {
            $this->rollback();
            return 0;
        }
        if ($result->num_rows > 0) {
            // There's already a timetable for the chosen class in the DB --> delete the old one in order to insert the new one
            $result = $this->query("DELETE FROM Timetable WHERE classID='$class'");
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

            $result = $this->query("INSERT INTO Timetable(`classID`, `day`, `hour`, `subject`) VALUES ('$class','$day','$hour','$subject')");
            if (!$result) {
                $this->rollback();
                return 0;
            }
        }

        $this->commit();
        return 1;
    }

    //TESTED
    public function checkIfTeacherHasLesson($day, $hour, $subject, $classID)
    {
        /*
        *  Funzione che controlla se una teacher ha già lezione in una classe ad una certa ora di un certo giorno della settimana
        *  Ritorno: false ---> la teacher ha già lezione in una certa classe (quindi non può tenere due lezioni in due posti diversi contemporaneamente)
        *           true ---> la teacher è libera in quello slot temporale
        */

        // Retrieve the teacher SSN
        $query0 = "SELECT * FROM TeacherClassSubjectTable WHERE classID='$classID' AND `subject`='$subject'";
        $result0 = $this->query($query0);
        if (!$result0) {
            // error
            return false;
        }
        if ($result0->num_rows == 1) {
            while (($row0 = $result0->fetch_array(MYSQLI_ASSOC)) != NULL) {
                $codFisc = $row0["codFisc"];
            }

            // Retrieve the teacher's subjects
            $query1 = "SELECT * FROM TeacherClassSubjectTable WHERE codFisc='$codFisc'";
            $result1 = $this->query($query1);
            if (!$result1) {
                // error
                return false;
            }
            if ($result1->num_rows > 0) {
                // For each (classID, subject)... 
                while (($row1 = $result1->fetch_array(MYSQLI_ASSOC)) != NULL) {
                    $currentClass = $row1["classID"];
                    $currentSubject = $row1["subject"];
                    if ($currentClass == $classID && $currentSubject == $subject) {
                        continue;
                    }

                    // ... check if (classID, subject) is present in the Timetable table at time $day and $hour
                    // If there's a row => return false
                    // No rows => return true
                    $query2 = "SELECT * FROM `Timetable` WHERE `classID`='$currentClass' AND `day`='$day' AND `hour`='$hour' AND `subject`='$currentSubject'";
                    $result2 = $this->query($query2);
                    if (!$result2) {
                        return false;
                    }
                    if ($result2->num_rows != 0) {
                        // The teacher has already a lesson in another class!
                        return false;
                    }
                }
            }
        } else {
            // error
            return false;
        }

        return true;
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

    //tested
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
            return array();

        $children = array();

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            array_push($children, $row);
        }

        return $children;
    }


    public function getInternalAnnouncements($classID)
    {
        $sql = "SELECT * FROM internalCommunications WHERE classID='$classID' ORDER BY Timestamp DESC";
        $res = $this->query($sql);
        return $res;
    }

    //tested
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


    //changed -- NEED TO BE TESTED
    public function retrieveAttendance($CodFisc)
    {

        /**
         * Retrieve the attendance of a given student in the current semester.
         * @param $codFisc (String) CodFisc of the searched student, e.g. 2015.
         * @return (Array) The attendance to the lectures of the given student in the form: 
         * "YYYY-MM-DD" => "absent" | "early - x" | "late - x" | "late and early - x - y" 
         * 
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
             * Produces an array as
             * "YYYY-MM-DD" => "Absent" | "early - x" | "late - x" | "late and early - x - y"
             * */

            if ($row["absence"] == 1 && $row["lateEntry"] == 0 && $row["earlyExit"] == 0) {
                // the student was absent that day
                $value = "absent";
            } elseif ($row["lateEntry"] != 0 && $row["earlyExit"] != 0) {
                // student both entered late and exited early
                $value = "late and early - " . strval($row["lateEntry"]) . " - " . strval($row["earlyExit"]);
            } elseif ($row["lateEntry"] != 0) {
                //entered late
                $value = "late - " . strval($row["lateEntry"]);
            } else {
                //no late entry,neither absence => exited early
                $value = "early - " . strval($row["earlyExit"]);
            }
            $attendance[$row["date"]] = $value;
        }

        $this->commit();

        return $attendance;
    }


    //tested
    public function viewChildAssignments($codFisc)
    {
        /*Retrieves all assignment of the selected child
        @param $codFisc (String): CodFisc of the selected student
        @return (Array): An array containing the requested info, in a format usable by the calendar functions:
                         $array['date'] = 'subject' : "View assignments:" 'assignment text' ~ 'subject' : "View assignments:" 'assignment text' ...
        */

        $codFisc = $this->sanitizeString($codFisc);
        $this->begin_transaction();

        /* Verify if the user logged in is actually allowed to see the assignments of the requested child */
        $result = $this->query("SELECT * FROM Students WHERE codFisc='$codFisc';");
        if (!$result)
            die("Unable to select student $codFisc");
        if (($row = $result->fetch_array(MYSQLI_ASSOC)) == NULL) {
            die("No student with ID $codFisc ");
        }
        $class = $this->getChildClass($codFisc);
        $parent1 = $row['emailP1'];
        $parent2 = $row['emailP2'];
        if ($_SESSION['user'] != $parent1 && $_SESSION['user'] != $parent2)
            die("You are not authorised to see this information.");


        /* find the current semester in order to show only the required dates*/
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
            die("Unable to select assignments for student $codFisc");
        }

        $assignments = array();

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {

            /*
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


    //tested
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

    //TESTED
    public function retrieveChildTimetable($class)
    {
        // returns the timetable of a certain class in the form | hour, mon, tue, wed, thu, fri |

        $class = $this->sanitizeString($class);
        $timetableToReturn = array();

        $result = $this->query("SELECT * FROM Timetable WHERE classID='$class'");

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

    public function viewChildFinalGrades($codFisc)
    {

        //TESTED
        $codFisc = $this->sanitizeString($codFisc);

        $this->begin_transaction();

        $authorised = $this->checkIfAuthorisedForChild($codFisc);

        if ($authorised !== true) {
            // not authorised to see the child
            $this->rollback();
            die($authorised);
        }

        $boundaries = getCurrentSemester();

        $beginningDate = $boundaries[0];
        $endingDate = $boundaries[1];

        $result = $this->query("SELECT * FROM FinalGrades WHERE codFisc='$codFisc' AND finalTerm > '$beginningDate' AND finalTerm< '$endingDate';");

        if (!$result) {
            $this->rollback();
            die("Unable to select final grades for student $codFisc");
        }

        $finalGrades = array();


        while ($row = $result->fetch_assoc()) {

            array_push($finalGrades, $row['subject'] . "," . $row['finalGrade']);
        }

        return $finalGrades;
    }


    /**
     * This function has the aim of retrieving the disciplinar notes of a particular student
     * @param $ssnStudent 
     * @return $notes: associative vector containing for each row all the information of a note. 
     * 
     */
    function retrieveStudentNotes($ssnStudent)
    {
        // NO NEED TO BE TESTED

        $ssnStudent = $this->sanitizeString($ssnStudent);
        return $result = $this->query("SELECT * FROM `StudentNotes` WHERE `codFiscStudent` = '$ssnStudent'");
    }

    //NEW 
    public function viewTeacherSlots($CodFisc)
    {
        /**
         * This function returns an array as "YYYY-MM-DD" => "teacherMeetings", in order to be used in the calendar functions
         *  @param $CodFisc: the SSN of the teacher
         */

        $CodFisc = $this->sanitizeString($CodFisc);

        $query = "SELECT DISTINCT `day` FROM `ParentMeetings` WHERE teacherCodFisc='$CodFisc'";
        $result = $this->query($query);
        if (!$result)
            return false;

        $ret = array();
        $ret["1996-07-25"] = "teacherMeetings";
        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            $ret[$row["day"]] = "teacherMeetings";
        }

        return $ret;
    }

    //NEW
    public function getTeachersByChild($codFisc)
    {
        //This functions returns array of teacher who teach in a given child class

        $codFisc = $this->sanitizeString($codFisc);

        $authorised = $this->checkIfAuthorisedForChild($codFisc);

        if ($authorised !== true) {
            // not authorised to see the child
            return false;
        }

        $class = $this->getChildClass($codFisc);


        $query = "SELECT DISTINCT t.codFisc, t.surname, t.name FROM teacherclasssubjecttable AS s, teachers AS t WHERE s.classID='$class' AND s.codFisc=t.codFisc";
        $result = $this->query($query);
        if (!$result)
            return false;

        $ret = array();
        $i = 0;
        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            $ret[$i]["codFisc"] = $row["codFisc"];
            $ret[$i]["surname"] = $row["surname"];
            $ret[$i]["name"] = $row["name"];
            $i++;
        }

        return $ret;
    }

    //TESTED
    public function getTeacherSlotsByDay($teacher, $day, $parentMail)
    {
        //This function gets the status of meeting slots of a given teacher for a given day, from the perspective of a given parent.
        //It returns a string in the format slot_quarter_status,slot_quarter_status...
        //where status may be "no" if there is no slot set, "free" if the quarter is free, "selected" if it is booked
        //by the parent or "full" if it is booked from another parent

        $teacher = $this->sanitizeString($teacher);
        $day = $this->sanitizeString($day);
        $parentMail = $this->sanitizeString($parentMail);


        $array = array();

        for ($i = 1; $i <= 6; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $array[$i][$j] = "no";
            }
        }

        $result = $this->query("SELECT * from ParentMeetings WHERE day='$day' AND teacherCodFisc='$teacher' ORDER BY slotNb ASC, quarter ASC");
        if (!$result)
            return null;

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if (empty($row['emailParent']))
                $array[$row['slotNb']][$row['quarter']] = "free";
            elseif ($row['emailParent'] == $parentMail)
                $array[$row['slotNb']][$row['quarter']] = "selected";
            else
                $array[$row['slotNb']][$row['quarter']] = "full";
        }

        $res = "";

        for ($i = 1; $i <= 6; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $res .= $i . "_" . $j . "_" . $array[$i][$j] . ",";
            }
        }

        return $res;
    }
}

class dbTeacher extends db
{

    function __construct()
    {
        if ($_SESSION['role'] != "teacher") throw new Exception("Creating DbTeacher object for an user who is NOT logged in as a teacher");
        parent::__construct();
    }

    //tested
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
        $title = $this->sanitizeString($title);
        $filename = $this->sanitizeString($filename);
        $dimension = $this->sanitizeString($dimension);
        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);

        return $this->query("INSERT INTO supportmaterials VALUES(CURRENT_TIMESTAMP, '$title', '$filename', '$dimension', '$class', '$subject')");
    }

    //tested
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

    //tested
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

    //tested
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

    //tested
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

    //tested
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

    //tested
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

    //tested
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

    //tested
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

    //TESTED
    public function getAssignmentsByClassAndDate($codTeacher, $class, $date)
    {

        $codTeacher = $this->sanitizeString($codTeacher);
        $class = $this->sanitizeString($class);
        $date = $this->sanitizeString($date);


        $result = $this->query("SELECT * FROM Assignments a, TeacherClassSubjectTable t WHERE t.codFisc='$codTeacher' AND t.subject = a.subject AND a.classID = '$class' AND a.date='$date' ORDER BY date DESC");

        if (!$result)
            die("Unable to select assignments.");

        if ($result->num_rows > 0) {
            $assignments = array();
            while ($row = $result->fetch_assoc()) {
                array_push($assignments,  $row['subject'] . "," . $row['textAssignment'] . "," . $row['pathFilename']);
            }
            return $assignments;
        }
    }


    function insertNewAssignments($date, $class, $subject, $assignments)
    {
        // NO NEED TO BE TESTED
        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $assignments = $this->sanitizeString($assignments);

        $result = $this->query("SELECT * FROM Assignments WHERE (classID='$class' AND subject='$subject' AND date='$date')");

        if ($result->num_rows > 0) {
            return -1;
        }

        $result = $this->query("INSERT INTO Assignments(subject, date, classID, textAssignment, pathFilename) 
								VALUES ('$subject', '$date', '$class', '$assignments', '')");

        if (!$result) {
            die("ERROR: Assignments not inserted.");
        }
    }

    //NO NEED TO BE TESTED
    function updateAssignments($date, $class, $subject, $assignments)
    {

        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $assignments = $this->sanitizeString($assignments);

        $result = $this->query("UPDATE Assignments SET textAssignment='$assignments'
							WHERE date='$date' AND subject='$subject' AND classID='$class'");

        if (!$result) {
            die("ERROR: Assignments not updated.");
        }
    }

    //NO NEED TO BE TESTED
    function deleteAssignments($date, $subject, $class)
    {

        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);

        $result = $this->query("DELETE FROM Assignments WHERE date='$date' AND subject='$subject' AND classID='$class'");

        if (!$result) {
            die("ERROR: Assignments not deleted.");
        }
    }


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
        $ssn = $this->sanitizeString($ssn);
        $day = $this->sanitizeString($day);

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
                    throw new Exception("Binding Failed in the Transaction.");

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
                        throw new Exception("Binding Failed in the Transaction.");

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

    //tested
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

    //tested
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

    //tested
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

    /**
     * This function has the aim to introduce in the DB a record about a discplinar note of a student;
     * @param $ssn: ssn of the student
     * @param $subject: subject in which the note has been recorded
     * @param $note: the discplinar note
     * @param $date: date in which the note has been recorded. (In theory it should be the same day that it happened)
     * @param $hour: hour of the day in which it happened
     * @return True if the insert has gone well, @return false otherwise.     
     */
    function recordStudentNote($ssnStudent, $ssnTeacher, $subject, $note, $date, $hour)
    {
        // query to record the note
        $ssnStudent = $this->sanitizeString($ssnStudent);
        $ssnTeacher = $this->sanitizeString($ssnTeacher);
        $subject = $this->sanitizeString($subject);
        $note = $this->sanitizeString($note);
        $date = $this->sanitizeString($date);
        $hour =  $this->sanitizeString($hour);

        return $this->query("INSERT INTO `StudentNotes`(`codFiscStudent`, `codFiscTeacher`, `date`, `hour`, `subject`, `Note`) VALUES ('$ssnStudent','$ssnTeacher','$date',$hour,'$subject','$note')");
    }

    /**
     * This function has the aim of removing all the notes of a student in a subject that have been recorded by the teacher in a particular day 
     * 
     * @param $ssnStudent
     * @param $ssnTeacher
     * @param $date
     * @param $subject
     * 
     * @return true if succeed
     * @return false otherwise.
     */
    function removeStudentNote($ssnStudent, $ssnTeacher, $date, $subject)
    {
        // query to record the note
        $ssnStudent = $this->sanitizeString($ssnStudent);
        $ssnTeacher = $this->sanitizeString($ssnTeacher);
        $date = $this->sanitizeString($date);
        $subject = $this->sanitizeString($subject);

        return $this->query("DELETE FROM `StudentNotes` WHERE `codFiscStudent`='$ssnStudent' AND`codFiscTeacher`='$ssnTeacher' AND`date`= '$date' AND `subject`='$subject'");
    }


    public function getFinalGrade($ssn, $subject, $date)
    {
        // no need to be tested
        $ssn = $this->sanitizeString($ssn);
        $subject = $this->sanitizeString($subject);
        $date = $this->sanitizeString($date);
        $result = $this->query("SELECT finalGrade FROM FinalGrades WHERE (codFisc='$ssn' AND subject='$subject' AND finalTerm='$date')");

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['finalGrade'];
        }

        return -1;
    }

    public function insertFinalGrade($ssn, $subject, $finalGrade, $date)
    {
        // no need to be tested

        /* This function allows to insert the final grade of the term of the student */

        $ssn = $this->sanitizeString($ssn);
        $subject = $this->sanitizeString($subject);
        $finalGrade = $this->sanitizeString($finalGrade);
        $date = $this->sanitizeString($date);

        $result = $this->query("SELECT finalGrade FROM FinalGrades WHERE (codFisc='$ssn' AND subject='$subject' AND finalTerm='$date')");

        if ($result->num_rows > 0) {
            return -1;
        }

        $result = $this->query("INSERT INTO FinalGrades(codFisc, subject, finalTerm, finalGrade) 
								VALUES ('$ssn', '$subject', '$date', '$finalGrade')");

        if (!$result) {
            die("ERROR: Final grade not inserted.");
        }

        return 0;
    }

    // NO NEED TO BE TESTED
    function insertAssignmentsMaterial($date, $filename, $class, $subject, $text)
    {
        return $this->query("INSERT INTO Assignments VALUES('$subject', '$date', '$class', '$text', '$filename')");
    }

    //TESTED
    public function viewSlotsAlreadyProvided($CodFisc)
    {
        /**
         * Retrieve the days in which a teacher has already provided at least one time slot for parent meetings in the current semester.
         * @param $CodFisc (String) CodFisc of the teacher.
         * @return (Array) The days in which a teacher has already provided at least one time slot for parent meetings in the form: 
         * "YYYY-MM-DD" => "" | "teacherMeetings" 
         */

        $CodFisc = $this->sanitizeString($CodFisc);

        $query = "SELECT DISTINCT `day` FROM `ParentMeetings` WHERE teacherCodFisc='$CodFisc'";
        $result = $this->query($query);
        if (!$result)
            die("Unable to execute the query!");

        $ret = array();
        $ret["1996-07-25"] = "teacherMeetings";
        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            /**
             * Produces an array as
             * "YYYY-MM-DD" => "teacherMeetings" 
             * */
            $ret[$row["day"]] = "teacherMeetings";
        }

        return $ret;
    }

    //TESTED
    public function showParentMeetingSlotsOfTheDay($codFisc, $day)
    {
        /**
         * Retrieve the slots and their availability of a certain date and of a certain teacher.
         * @param $codFisc (String) CodFisc of the teacher
         * @param $day (String in the format "YYYY-MM-DD") 
         * @return (String) Slots availability in the form: 
         * "1_lesson,2_free,3_free,4_selected,5_selected,6_lesson"
         * where the numbers 1..6 correspond to the time slots 8:00-9:00 .. 13:00-14:00
         * and 
         * "free" --> time slot available for meetings
         * "lesson" --> time slot in which the teacher has a lecture in a certain class
         * "selected" --> time slot already selected for meetings
         * In case of error: ""
         */

        $codFisc = $this->sanitizeString($codFisc);
        $day = $this->sanitizeString($day);

        // Initialization of the array which will contain the available/occupied slots
        $slots = array();
        for ($i = 1; $i <= 6; $i++) {
            $slots[$i] = "free";
        }

        $this->begin_transaction();

        // Retrieve the lectures of the teacher in the specified day
        // Recupero le coppie (classe, materia) che mostrano quali materie la teacher insegna nelle diverse classi
        $query0 = "SELECT * FROM TeacherClassSubjectTable WHERE codFisc='$codFisc'";
        $result0 = $this->query($query0);
        if (!$result0) {
            $this->rollback();
            return "";
        }

        if ($result0->num_rows > 0) {
            // Per ogni coppia (classe, materia)...
            while (($row0 = $result0->fetch_array(MYSQLI_ASSOC)) != NULL) {
                $currentClass = $row0["classID"];
                $currentSubject = $row0["subject"];
                // ... controllo se nel giorno d'interesse ($day) e' presente una qualche lezione tenuta dalla teacher
                // Retrieve which day of the week is the date under consideration
                // Convert the date string into a unix timestamp
                $unixTimestamp = strtotime($day);
                // Get the day of the week using PHP's date function
                $dayOfWeek = date("l", $unixTimestamp);
                // Convert in the form of the DB
                switch ($dayOfWeek) {
                    case "Monday":
                        $dayOfTheWeekDB = "mon";
                        break;
                    case "Tuesday":
                        $dayOfTheWeekDB = "tue";
                        break;
                    case "Wednesday":
                        $dayOfTheWeekDB = "wed";
                        break;
                    case "Thursday":
                        $dayOfTheWeekDB = "thu";
                        break;
                    case "Friday":
                        $dayOfTheWeekDB = "fri";
                        break;
                    case "Saturday":
                        $dayOfTheWeekDB = "sat";
                        break;
                    case "Sunday":
                        $dayOfTheWeekDB = "sun";
                        break;
                }
                $query1 = "SELECT * FROM `Timetable` WHERE `classID`='$currentClass' AND `day`='$dayOfTheWeekDB' AND `subject`='$currentSubject'";
                $result1 = $this->query($query1);
                if (!$result1) {
                    $this->rollback();
                    return "";
                }
                if ($result1->num_rows > 0) {
                    // La teacher ha almeno una lezione nel giorno d'interesse => mi salvo gli slot occupati
                    while (($row1 = $result1->fetch_array(MYSQLI_ASSOC)) != NULL) {
                        $slots[$row1["hour"]] = "lesson";
                    }
                }
            }
        }

        // Retrieve the parent meetings time slots already provided by the teacher in the specified day
        $query = "SELECT slotNb FROM `ParentMeetings` WHERE teacherCodFisc='$codFisc' AND `day`='$day'";
        $result = $this->query($query);
        if (!$result) {
            $this->rollback();
            return "";
        }

        if ($result->num_rows > 0) {
            while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
                $slots[$row["slotNb"]] = "selected";
            }
        }

        // Prepare the string that has to be returned
        $str = "";
        for ($i = 1; $i <= 6; $i++) {
            $str = $str . $i . "_" . $slots[$i] . ",";
        }

        $r = $this->commit();
        if (!$r) {
            // Error during the commit => return the empty string
            return "";
        }

        return $str;
    }

    //TESTED
    public function provideSlot($codFisc, $day, $slotNb)
    {
        /**
         * Retrieve the slots and their availability of a certain date and of a certain teacher.
         * @param $codFisc (String) CodFisc of the teacher
         * @param $day (String in the format "YYYY-MM-DD") 
         * @param $slotNb (String, which is actually an int) Target time slot for parent meetings
         * @return (String) "white" if ($codFisc, $day, $slotNb) was already present in the DB, so the teacher has clicked that 
         *                          slot in order to cancel the availability for parent meetings (then the slot has to be colored in white)
         *                   "#lightgreen" if ($codFisc, $day, $slotNb) wasn't in the DB, so the teacher has clicked that 
         *                            slot in order to make it available for parent meetings (then the slot has to be colored in green)
         *                   "error" if some error occured during the execution of this function
         */

        $codFisc = $this->sanitizeString($codFisc);
        $day = $this->sanitizeString($day);
        $slotNb = $this->sanitizeString($slotNb);
        $nb = intval($slotNb);

        // Initialization of the return value
        $color = "white";

        $this->begin_transaction();

        // Check if the ($codFisc, $day, $slotNb) is already in the DB
        $query = "SELECT * FROM `ParentMeetings` WHERE teacherCodFisc='$codFisc' AND `day`='$day' AND slotNb=$nb";
        $result = $this->query($query);
        if (!$result) {
            $this->rollback();
            return "error";
        }

        if ($result->num_rows > 0) {
            // The ($codFisc, $day, $slotNb) is present in the DB => delete the row => "white" has to be returned
            $query1 = "DELETE FROM `ParentMeetings` WHERE teacherCodFisc='$codFisc' AND `day`='$day' AND slotNb=$nb";
            $result1 = $this->query($query1);
            if (!$result1) {
                $this->rollback();
                return "error";
            }
        } else {
            // The ($codFisc, $day, $slotNb) is not in the DB => insert the row => "#b3ffcc" has to be returned
            for ($i = 1; $i <= 4; $i++) {
                $query2 = "INSERT INTO `ParentMeetings`(`teacherCodFisc`, `day`, `slotNb`, `quarter`, `emailParent`) VALUES('$codFisc','$day',$nb,$i,'')";
                $result2 = $this->query($query2);
                if (!$result2) {
                    $this->rollback();
                    return "error";
                }
            }

            $color = "lightgreen";
        }

        $result = $this->commit();
        if (!$result) {
            // Error during the commit
            return "error";
        }

        return $color;
    }

    //TESTED
    function isCoordinator($codTeacher, $class)
    {

        $codTeacher = $this->sanitizeString($codTeacher);
        $class = $this->sanitizeString($class);

        $result = $this->query("SELECT * FROM Classes WHERE classID='$class' AND coordinatorSSN='$codTeacher'");

        if (!$result)
            die("Unable to select coordinator");

        if ($result->num_rows > 0) {
            return true;
        }

        return false;
    }

    //TESTED
    public function retrieveTimetableOfAClass($class, $teacherSSN)
    {

        // Returns the timetable of a certain class in the form | hour, mon, tue, wed, thu, fri |

        $class = $this->sanitizeString($class);
        $teacherSSN = $this->sanitizeString($teacherSSN);
        $timetableToReturn = array();
        $authorized = true;

        // Check if the teacher is authorized to see the timetable of the requested class
        $authorized = $this->checkIfAuthorized($class, $teacherSSN);
        if ($authorized) {

            $result = $this->query("SELECT * FROM Timetable WHERE classID='$class'");

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
        }

        return $timetableToReturn;
    }

    //TESTED
    public function checkIfAuthorized($class, $teacherSSN)
    {
        // Check if a certain teacher teaches in a specified class
        // Return true if the teacher has lessons in that class
        //        false if the teacher has no lessons in that class

        $class = $this->sanitizeString($class);
        $teacherSSN = $this->sanitizeString($teacherSSN);

        $result = $this->query("SELECT * FROM TeacherClassSubjectTable WHERE codFisc='$teacherSSN' AND classID='$class'");

        if (!$result)
            die("Unable to execute the query in checkIfAuthorized function");

        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    //TESTED
    public function retrieveTimetableTeacher($teacherSSN)
    {

        // Returns the timetable of a certain teacher in the form | hour, mon, tue, wed, thu, fri |
        // NOTE: if the teacher has no lessons at all the timetable will be filled with '-'

        $teacherSSN = $this->sanitizeString($teacherSSN);
        $timetableToReturn = array();

        // Initialization of the timetable: filling it with all '-'
        $days = array("mon", "tue", "wed", "thu", "fri");
        for ($i = 1; $i <= 6; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $timetableToReturn[$i][$days[$j]] = "-";
            }
        }

        // Check if the teacher exists in the DB
        $exist = $this->checkIfExist($teacherSSN);

        if ($exist) {

            // Select the subjects teached by the teacher 
            $result0 = $this->query("SELECT * FROM TeacherClassSubjectTable WHERE codFisc='$teacherSSN'");
            if (!$result0)
                die("Unable to select timetable for teacher $teacherSSN");

            if ($result0->num_rows > 0) {
                while ($teachedSubject = $result0->fetch_assoc()) {
                    $class = $teachedSubject["classID"];
                    $subject = $teachedSubject["subject"];

                    $result1 = $this->query("SELECT * FROM Timetable WHERE classID='$class' AND `subject`='$subject'");
                    if (!$result1)
                        die("Unable to select the timetable for the teacher $teacherSSN");

                    if ($result1->num_rows > 0) {
                        while ($lecture = $result1->fetch_assoc()) {
                            // Store the row with the format: $timetableToReturn[1]["mon"] = "Math in class 1A"
                            $day = $lecture["day"];
                            $hour = $lecture["hour"];
                            $subject = $lecture["subject"];
                            $timetableToReturn[$hour][$day] = "$subject in class $class";
                        }
                    }
                }
            }
        } else {
            // Empty the timetable to report an error
            $timetableToReturn = array();
        }

        return $timetableToReturn;
    }

    //TESTED
    public function checkIfExist($teacherSSN)
    {
        // Check if a certain teacher exists in the DB
        // Return true if the teacher exists in the DB
        //        false if the teacher is not present in the DB

        $teacherSSN = $this->sanitizeString($teacherSSN);

        $result = $this->query("SELECT * FROM Teachers WHERE codFisc='$teacherSSN'");

        if (!$result)
            die("Unable to execute the query in checkIfExist function");

        if ($result->num_rows == 1) {
            return true;
        } else {
            return false;
        }
    }

    //TESTED
    function getLecturesByTeacherClassAndSubject($codTeacher, $class, $subject)
    {
        $codTeacher = $this->sanitizeString($codTeacher);
        $class = $this->sanitizeString($class);
        $subject = $this->sanitizeString($subject);


        $result = $this->query("SELECT * FROM Lectures WHERE codFiscTeacher='$codTeacher' AND classID='$class' AND subject='$subject' ORDER BY date DESC");

        if (!$result)
            die("Unable to select lectures.");

        if ($result->num_rows > 0) {
            $lectures = array();
            while ($row = $result->fetch_assoc()) {
                array_push($lectures, $row['date'] . "," . $row['hour'] . "," . $row['topic']);
            }
            return $lectures;
        }
    }
}
