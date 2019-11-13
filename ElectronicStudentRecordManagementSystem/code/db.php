<?php

require_once("basicChecks.php");

checkIfLogged();

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
    
    protected function begin_transaction(){
        $this->conn->begin_transaction();
    }

    protected function commit(){
        return $this->conn->commit();
    }

    protected function rollback(){
        return $this->conn->rollback();
    }

    /**
     * Problem specific methods
     */

    public function getSubjectTaughtInClass($class){

        $class = $this->sanitizeString($class);

        $year = intval(substr($class,0,1));

        if($year<1 || $year>5){
            // year non valid
            throw new Exception("Inserted non valid year: " . $year);
        }
        
        $result = $this->query("SELECT name FROM Subjects WHERE year='$year';");

        $subjects = array();

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            array_push($subjects,$row['name']);
        }

        return $subjects;
    }

    /**
     * Utilities
     */
    protected function sanitizeString($var)
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

        $stmt = $this->prepareStatement(
            "SELECT s.`codFisc`,`name`,`surname`,p.`classID` 
        FROM `Students` as s , `ProposedClasses` as p 
        WHERE s.codFisc = p.codFisc AND p.classID = ?"
        );

        if (!$stmt->bind_param("s", $classID))
            die("Binding Failed in the Transaction.");


        if (!$stmt->bind_result($codFisc, $name, $surname, $class))
            die("Binding result Failed in the Transaction.");

        try {
            if (!$stmt->execute())
                throw new Exception("Select Failed.");
            $i = 0;
            while ($row = $stmt->fetch()) {
                $codFiscnameSurnameClass = array($codFisc, $name, $surname, $class);
                $compositionVector[$i] = $codFiscnameSurnameClass;
                $i++;
            }

            /* close statement */
            $stmt->close();

            return $compositionVector;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
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
    public function TakeParentsMail(){
        $result = $this->query("SELECT email, hashedPassword, firstLogin FROM Parents ORDER BY hashedPassword, email");
        return $result;
    }
    public function ChangePassword($to_address, $hashed_pw){
        $this->query("UPDATE Parents SET hashedPassword = '$hashed_pw' WHERE email='$to_address'");
    }

    public function insertStudent($name, $surname, $SSN, $email1, $email2){
        return $this->query("INSERT INTO students(codFisc, name, surname, emailP1, emailP2, classID) VALUES ('$SSN','$name','$surname','$email1','$email2', '')");
    }
    
    public function insertParent($name, $surname, $SSN, $email){
        return $this->query("INSERT INTO parents(email, hashedPassword, name, surname, codFisc, firstLogin) VALUES ('$email', '','$name','$surname','$SSN', 1)");
    }
    
    public function insertLectures($date, $hour, $classID, $codFiscTeacher, $subject, $topic) {
		return $this->query("INSERT INTO lectures(date, hour, classID, codFiscTeacher, subject, topic) 
								VALUES('$date', '$hour', '$classID', '$codFiscTeacher', '$subject', '$topic')");
	}
    
    public function enrollStudent($name, $surname, $SSN, $name1, $surname1, $SSN1, $email1, $name2, $surname2, $SSN2, $email2){
                
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
        if($result == FALSE){
            $this->conn->rollback();
            return 0;
        }
            
        
        /* Insert parent 1 into the DB */
        $result = $this->insertParent($name1, $surname1, $SSN1, $email1);
        if($result == FALSE){
            $this->conn->rollback();    // TOFIX ------------------------------------------------------
            return 0;
        }

        if(!empty($email2)){
            /* Insert parent 2 into the DB */
            $result = $this->insertParent($name2, $surname2, $SSN2, $email2);
            if($result == FALSE){
                $this->conn->rollback();
                return 0;
            }
        }
        
        $this->conn->commit();
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

    public function viewChildMarks($CodFisc)
    {
        /** 
         * This function receives the fiscal code of a child, 
         * verifies if the child is actually a child of that parent
         * and then returns the marks.    
         * Format: Subject,Date,Mark;Subject,Date..Mark;
         */

        $CodFisc = $this->sanitizeString($CodFisc);

        /* Verify if the user logged in is actually allowed to see the marks of the requested child */
        $result = $this->query("SELECT * FROM Students WHERE codFisc='$CodFisc';");

        if (!$result)
            die("Unable to select student $CodFisc");

        if (($row = $result->fetch_array(MYSQLI_ASSOC)) == NULL) {
            die("No student with ID $CodFisc ");
        }

        $parent1 = $row['emailP1'];
        $parent2 = $row['emailP2'];

        if ($_SESSION['user'] != $parent1 && $_SESSION['user'] != $parent2)
            die("You are not authorised to see this information.");

        /* The user can see the marks => retrieve the marks of the current year */

        $year = intval(date("Y"));
        $month = intval(date("m"));

        if($month <= 7){
            // second semester
            $year=$year-1;
        }

        $beginningDate = $year."-08-01";

        $year=$year+1;
        $endingDate = $year."-07-31";

        $result = $this->query("SELECT subject,date,hour,mark FROM Marks WHERE codFisc='$CodFisc' AND date > '$beginningDate' AND date< '$endingDate' ORDER BY subject ASC,date DESC,hour DESC;");

        $marks = "";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            $marks = $marks . $row['subject'] . ',' . $row['date'] . "," . $row['hour'] ."° hour,".$row['mark'] . ";";
        }

        if ($marks != "") $marks = substr($marks, 0, -1); // to remove the last ;

        return $marks;
    }
}

class dbTeacher extends db
{

    function __construct()
    {
        if ($_SESSION['role'] != "teacher") throw new Exception("Creating DbTeacher object for an user who is NOT logged in as a teacher");
        parent::__construct();
    }

    function insertMark($codStudent, $subject, $date, $hour, $mark)
    { 

    }

    function getStudentsByClass($class){

        $class = $this -> sanitizeString($class);
        
        $result = $this->query("SELECT * FROM students WHERE classId='$class'");
        
        if (!$result) 
            die("Unable to select students from $class.");

        $students="";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL){
                $students = $students . "<form method='post' action='studentMarks.php'><input type='hidden' name='student' value='" . $row['codFisc'] . "'><input type='submit', value='" . $row['name'] . " " . $row['surname'] . "'></form>";
        }
        
        return $students;

    }

    function getClassesByTeacher($codTeacher){
        $codTeacher = $this -> sanitizeString($codTeacher);
        
        $result = $this->query("SELECT * FROM students WHERE classId='1A'");
        
        if (!$result) 
            die("Unable to select classes from Teacher Id.");

        $classes="";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL){
                $classes = $classes . "<tr><td>" . $row['surname'] . "</td><td>" . $row['name'] . "</td><td><form method=\"post\" action=\"studentMarks.php\"> <input type=\"hidden\" name=\"codStudent\" value=" . $row['codFisc'] . "><input type=\"submit\", id=\"" . $row['codFisc'] . "\" value=\"Add Grade\"></form></td></tr><br>";
        }
        
        //TODO DA RIMUOVERE (E IMPLEMENTARE LA FUNZIONE CORRETA UNA VOLTA CHE C'E' LA TABELLA/E ADEGUATA
        $classes = "<form method='post' action='selectSubjectForMarks.php'><input type='submit' name='class' value='1A'></form><form method='post' action='selectSubjectForMarks.php'><input type='hidden' name='class' value='1B'><input type='submit' value='Non mandare questo'></form>";

        return $classes;

    }

    function getSubjectsByTeacherAndClass($codTeacher, $class){
        $codTeacher = $this -> sanitizeString($codTeacher);
        $class = $this -> sanitizeString($class);
        
        $result = $this->query("SELECT * FROM students WHERE classId='1A'");
        
        if (!$result) 
            die("Unable to select subjects.");

        $classes="";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL){
                $classes = $classes . "<tr><td>" . $row['surname'] . "</td><td>" . $row['name'] . "</td><td><form method=\"post\" action=\"studentMarks.php\"> <input type=\"hidden\" name=\"codStudent\" value=" . $row['codFisc'] . "><input type=\"submit\", id=\"" . $row['codFisc'] . "\" value=\"Add Grade\"></form></td></tr><br>";
        }
        
        //TODO DA RIMUOVERE (E IMPLEMENTARE LA FUNZIONE CORRETA UNA VOLTA CHE C'E' LA TABELLA/E ADEGUATA
        $classes = "<form method='post' action='submitMark.php'><input type='submit' name='subject' value='Math'></form><form method='post' action='submitMark.php'><input type='hidden' name='subject' value='Science'><input type='submit' value='Science'></form>";

        return $classes;
    }

    function getStudentSubjectMarks($student, $subject){
        $subject = $this -> sanitizeString($subject);
        $student = $this -> sanitizeString($student);

        $result = $this->query("SELECT * FROM Marks WHERE codFisc='$student' AND subject='$subject'");
        
        if (!$result) 
            die("Student has no marks for this subject.");

        $marks="<tr><th>Date</th><th>Hour</th><th>Grade</th></tr>";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL){
                $marks = $marks . "<tr><td>" . $row['date'] . "</td><td>" . $row['hour'] . "</td><td>" . $row['mark'] ."</td></tr><br>";
        }
        

        return $marks;
    }
    
    function insertDailyLesson($class, $subject, $date, $hour, $topics, $codFisc) {
		$class = $this -> sanitizeString($class);
		$subject = $this -> sanitizeString($subject);
		$date = $this -> sanitizeString($date);
		$hour = $this -> sanitizeString($hour);
		$topics = $this -> sanitizeString($topics);
	
		$result = $this->insertLectures($date, $hour, $class, $codFisc, $subject, $topics);
		if($result == FALSE) {
			die("ERROR: Lecture not inserted.");
		}
	}
	
	function updateDailyLesson($class, $subject, $date, $hour, $topics, $codFisc) {
		
	}
	
		
	function getClassesByTeacher2($codTeacher){
        $codTeacher = $this -> sanitizeString($codTeacher);
        $result = $this->query("SELECT classID FROM teacherclasssubjecttable WHERE codFisc='$codTeacher'");

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
	
	function getSubjectsByTeacherAndClass2($codTeacher, $class){
		$codTeacher = $this -> sanitizeString($codTeacher);
		$class = $this -> sanitizeString($class);
        
        $result = $this->query("SELECT subject FROM teacherclasssubjecttable WHERE (classID='$class' AND codFisc='$codTeacher')");

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
	
	function getLecturesByTeacher($codTeacher) {
		$codTeacher = $this -> sanitizeString($codTeacher);

		$result = $this->query("SELECT * FROM lectures WHERE codFiscTeacher='$codTeacher'");
	
		if (!$result) 
            die("Unable to select lectures.");
		
        if ($result->num_rows > 0) {
            $lectures = array();

			while ($row = $result->fetch_assoc()) { 
				array_push($lectures,  "<tr><td><label>Class: </label></td><td>". $row['classID']."</td></tr>
										<tr><td><label>Subject: </label></td><td>". $row['subject']."</td></tr>
										<tr><td><label>Date: </label></td><td>". $row['date'] ."</td></tr>
										<tr><td><label>Hour: </label></td><td>".$row['hour'] ."</td></tr>
										<tr><td><label>Topics: </label></td><td>".$row['topic']."</td></tr>");
			}
				return $lectures;

		}
			
	}
}
