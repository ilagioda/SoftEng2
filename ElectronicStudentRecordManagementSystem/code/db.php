<?php

require_once("basicChecks.php");

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

    function getHashedPassword($user){
        $sql = "SELECT email, hashedPassword FROM Parents WHERE email='$user'";
        $sql2 = "SELECT codFisc, hashedPassword FROM Teachers WHERE codFisc='$user'";
        $sql3 = "SELECT codFisc, hashedPassword FROM Principals WHERE codFisc='$user'";
        $sql4 = "SELECT codFisc, hashedPassword FROM Admins WHERE codFisc='$user'";
        $ret_value = array();

        if(preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $user) ){
            //it's a mail so I only check in parents table.
            $result = $this->query($sql);
            if(!$result || $result->num_rows==0) return false;
            else{
                //it's a parent
                $result = $result->fetch_array(MYSQLI_ASSOC);
                $ret_value["user"] = $result["email"];
                $ret_value["role"] = "parent";
                $ret_value["hashedPassword"] = $result["hashedPassword"];
            }
        }
        else{
            //check if it's a teacher, a principal or an admin
            $result = $this->query($sql2);
            
            if(!$result) return false;
            if($result->num_rows==0){
                $result = $this->query($sql3);
                
                if(!$result) return false;
                if($result->num_rows==0){
                    $result = $this->query($sql4);
                    
                    if(!$result) return false;
                    if($result->num_rows==0){
                        return false; //neither a parent, nor a teacher, nor a principal, nor an admin
                    }
                    else{
                        //it's an admin
                        $result = $result->fetch_array(MYSQLI_ASSOC);
                        $ret_value["user"] = $result["codFisc"];
                        $ret_value["role"] = "admin";
                        $ret_value["hashedPassword"] = $result["hashedPassword"];

                    }
                }
                else{
                    //it's a principal
                    $result = $result->fetch_array(MYSQLI_ASSOC);
                    $ret_value["user"] = $result["codFisc"];
                    $ret_value["role"] = "principal";
                    $ret_value["hashedPassword"] = $result["hashedPassword"];
                }
            }
            else{
                //it's a teacher
                $result = $result->fetch_array(MYSQLI_ASSOC);
                $ret_value["user"] = $result["codFisc"];
                $ret_value["role"] = "teacher";
                $ret_value["hashedPassword"] = $result["hashedPassword"];
            }
        }
        return $ret_value;
    }

    function getAnnouncements(){
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

    function insertOfficialAccount($who, $SSN, $hashedPw, $name, $surname){
        $sql = "INSERT INTO $who VALUES('$SSN', '$hashedPw', '$name', '$surname')";
        $res = $this->query($sql);
        return $res;
    }

    function SearchInParents($user, $pass){
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

    
    public function TakeParentsMail(){
        $result = $this->query("SELECT email, hashedPassword, firstLogin FROM Parents ORDER BY hashedPassword, email");
        return $result;
    }
    public function ChangePassword($to_address, $hashed_pw){
        $this->query("UPDATE Parents SET hashedPassword = '$hashed_pw' WHERE email='$to_address'");
    }

    public function insertStudent($name, $surname, $SSN, $email1, $email2){
        return $this->query("INSERT INTO Students(codFisc, name, surname, emailP1, emailP2, classID) VALUES ('$SSN','$name','$surname','$email1','$email2', '')");
    }
    
    public function insertParent($name, $surname, $SSN, $email){
        return $this->query("INSERT INTO Parents(email, hashedPassword, name, surname, codFisc, firstLogin) VALUES ('$email', '','$name','$surname','$SSN', 1)");
    }
    
    public function insertLectures($date, $hour, $classID, $codFiscTeacher, $subject, $topic) {
		return $this->query("INSERT INTO Lectures(date, hour, classID, codFiscTeacher, subject, topic) 
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
        if(!$result){
            $this->rollback();
            return 0;
        }
            
        
        /* Insert parent 1 into the DB */
        $result = $this->insertParent($name1, $surname1, $SSN1, $email1);
        // if(!$result){
        //     $this->rollback();    
        //     return 0;
        // }

        if(!empty($email2)){
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
}



class dbParent extends db
{

    function __construct()
    {
        if ($_SESSION['role'] != "parent") throw new Exception("Creating DbParent object for an user who is NOT logged in as a parent");
        parent::__construct();
    }

    public function retrieveChildren($email){

        /**
         * This function receives the email of a parent and returns the list of children of that parent
         */

        $email=$this->sanitizeString($email);

        $result = $this->query("SELECT codFisc,name,surname,classID FROM Students 
                                WHERE (classID != '') AND (emailP1='$email' OR emailP2 = '$email') 
                                ORDER BY name,surname;");
    
        if (!$result)
            die("Unable to select children for $email");

        $children = array();

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            array_push($children,$row);
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

    function getStudentsName($codfisc){

        $codfisc = $this -> sanitizeString($codfisc);
        
        $result = $this->query("SELECT * FROM Students WHERE codFisc='$codfisc'");
        
        if (!$result) 
            die("Unable to select student.");

        $student="";
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if($row!=NULL)
            $student=$row['surname'] . " " . $row['name'];

        return $student;

    }

    function insertGrade($date, $hour, $student, $subject, $grade){

        $student = $this -> sanitizeString($student);
		$subject = $this -> sanitizeString($subject);
		$date = $this -> sanitizeString($date);
		$hour = $this -> sanitizeString($hour);
		$grade = $this -> sanitizeString($grade);
		
		$result = $this->query("SELECT * FROM marks WHERE (codFisc='$student' AND hour='$hour' AND date='$date')");
	
		if($result->num_rows > 0) {
			return -1;
		}

		$result = $this->query("INSERT INTO marks(codFisc, subject, date, hour, mark) 
								VALUES ('$student', '$subject', '$date', '$hour', '$grade')");
		
		if($result == FALSE) {
			die("ERROR: Mark not inserted.");
		}
    }


    function getGradesByTeacher($codfisc){
        $codfisc= $this->sanitizeString($codfisc);

        $result = $this -> query("SELECT s.classId, s.codFisc, s.name, s.surname, m.subject, m.date, m.hour, m.mark 
        FROM Marks m, TeacherClassSubjectTable tcs, Students s 
        WHERE tcs.codFisc = '$codfisc' AND tcs.subject = m.subject AND s.codFisc = m.codFisc AND tcs.classID = s.classID ORDER BY m.date DESC");

        if(!$result)
            die("Unable to load marks.");

        if($result->num_rows > 0) {
            $marks = array();
			while ($row = $result->fetch_assoc()) { 
				array_push($marks,  "".$row['classId'].",".$row['codFisc'].",".$row['surname'].",".$row['name'].",".$row['subject'].",".$row['date'].",".$row['hour'].",".$row['mark']."");
			}
			return $marks;
		}

    }

    function updateMark($codFisc, $subject, $date, $hour, $grade){
        $codFisc = $this -> sanitizeString($codFisc);
		$subject = $this -> sanitizeString($subject);
		$date = $this -> sanitizeString($date);
		$hour = $this -> sanitizeString($hour);
        $grade = $this -> sanitizeString($grade);
        
        $result = $this->query("UPDATE marks SET subject='$subject', mark='$grade' 
							WHERE date='$date' AND hour='$hour' AND codFisc='$codFisc'");
		
		if(!$result) {
			die("ERROR: Mark not updated.");
		}

    }


    function deleteMark($codFisc, $date, $hour){
        $codFisc = $this -> sanitizeString($codFisc);
		$date = $this -> sanitizeString($date);
        $hour = $this -> sanitizeString($hour);
        
        $result = $this->query("DELETE FROM marks WHERE date='$date' AND hour='$hour' AND codFisc='$codFisc'");

        if(!$result) {
			die("ERROR: Mark not deleted.");
		}
    }

    function insertDailyLesson($date, $hour, $class, $codTeacher, $subject, $topics) {

		$class = $this -> sanitizeString($class);
		$subject = $this -> sanitizeString($subject);
		$date = $this -> sanitizeString($date);
		$hour = $this -> sanitizeString($hour);
		$topics = $this -> sanitizeString($topics);
	    $codTeacher = $this -> sanitizeString($codTeacher);
		
		$result = $this->query("SELECT * FROM Lectures WHERE (classID='$class' AND hour='$hour' AND date='$date')");
	
		if($result->num_rows > 0) {
			return -1;
		}

		$result = $this->query("INSERT INTO Lectures(date, hour, classID, codFiscTeacher, subject, topic) 
								VALUES ('$date', '$hour', '$class', '$codTeacher', '$subject', '$topics')");
		
		if($result == FALSE) {
			die("ERROR: Lecture not inserted.");
		}
		
	}
	
	function updateDailyLesson($date, $hour, $class, $subject, $topics) {

		$class = $this -> sanitizeString($class);
		$subject = $this -> sanitizeString($subject);
		$date = $this -> sanitizeString($date);
		$hour = $this -> sanitizeString($hour);
		$topics = $this -> sanitizeString($topics);

		
		$result = $this->query("UPDATE Lectures SET subject='$subject', topic='$topics' 
							WHERE date='$date' AND hour='$hour' AND classID='$class'");
		
		if($result == FALSE) {
			die("ERROR: Lecture not updated.");
		}

	}
	
	function deleteDailyLesson($date, $hour, $class) {
		$date = $this -> sanitizeString($date);
		$hour = $this -> sanitizeString($hour);
		$class = $this -> sanitizeString($class);
		
		$result = $this->query("DELETE FROM Lectures WHERE date='$date' AND hour='$hour' AND classID='$class'");
		
		if($result == FALSE) {
			die("ERROR: Lecture not deleted.");
		}
		
	}
		
	function getClassesByTeacher2($codTeacher){
        $codTeacher = $this -> sanitizeString($codTeacher);
        $result = $this->query("SELECT DISTINCT classID FROM TeacherClassSubjectTable WHERE codFisc='$codTeacher'");

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
	
	function getLecturesByTeacher($codTeacher) {
		$codTeacher = $this -> sanitizeString($codTeacher);

		$result = $this->query("SELECT * FROM Lectures WHERE codFiscTeacher='$codTeacher' ORDER BY date DESC");
	
		if (!$result) 
            		die("Unable to select lectures.");
		
        	if ($result->num_rows > 0) {
            		$lectures = array();
			while ($row = $result->fetch_assoc()) { 
				array_push($lectures,  "".$row['classID'].",".$row['subject'].",".$row['date'].",".$row['hour'].",".$row['topic']."");
			}
			return $lectures;
		}
	}
	
	function getAssignmentsByTeacher($codTeacher) {
		$codTeacher = $this -> sanitizeString($codTeacher);
		
		$result = $this->query("SELECT * FROM Assignments WHERE codFiscTeacher='$codTeacher' ORDER BY date DESC");
		
		if (!$result) 
        	die("Unable to select assignments.");
		
        if ($result->num_rows > 0) {
         	$assignments = array();
			while ($row = $result->fetch_assoc()) { 
				array_push($assignments,  "".$row['classID'].",".$row['subject'].",".$row['date'].",".$row['textAssignment']."");
			}
			return $assignments;
		}		
	}
	
	function insertNewAssignments($date, $class, $codTeacher, $subject, $assignments) {
		
		
		$class = $this -> sanitizeString($class);
		$subject = $this -> sanitizeString($subject);
		$date = $this -> sanitizeString($date);
		$assignments = $this -> sanitizeString($assignments);
	    $codTeacher = $this -> sanitizeString($codTeacher);
		
		$result = $this->query("SELECT * FROM Assignments WHERE (classID='$class' AND subject='$subject' AND date='$date')");
	
		if($result->num_rows > 0) {
			return -1;
		}

		$result = $this->query("INSERT INTO Assignments(subject, date, classID, textAssignment, codFiscTeacher) 
								VALUES ('$subject', '$date', '$class', '$assignments', '$codTeacher')");
		
		if($result == FALSE) {
			die("ERROR: Assignments not inserted.");
		}
		
	} 	
	
	function updateAssignments($date, $class, $subject, $assignments) { 
	
		$class = $this -> sanitizeString($class);
		$subject = $this -> sanitizeString($subject);
		$date = $this -> sanitizeString($date);
		$assignments = $this -> sanitizeString($assignments);
		
		$result = $this->query("UPDATE Assignments SET textAssignment='$assignments'
							WHERE date='$date' AND subject='$subject' AND classID='$class'");
		
		if($result == FALSE) {
			die("ERROR: Assignments not updated.");
		}
	}
	
	function deleteAssignments($date, $subject, $class) {
		
		$class = $this -> sanitizeString($class);
		$subject = $this -> sanitizeString($subject);
		$date = $this -> sanitizeString($date);
		
		$result = $this->query("DELETE FROM Assignments WHERE date='$date' AND subject='$subject' AND classID='$class'");
		
		if($result == FALSE) {
			die("ERROR: Assignments not deleted.");
		}
		
	}
	
	function getStudentsByClass2($class){

        $class = $this -> sanitizeString($class);
        
        $result = $this->query("SELECT * FROM Students WHERE classID='$class'");
        
        if (!$result) 
            die("Unable to select students");

   	    if ($result->num_rows > 0) {

            $students = array();
            while ($row = $result->fetch_assoc()) {
               	array_push($students, "".$row['name'].",".$row['surname'].",".$row['codFisc']."");
            }
            return $students;
        }
	
    }
	
	function insertAbsenceNote($date, $hour, $student, $class, $codTeacher, $subject, $absencenote) {
		
		$class = $this -> sanitizeString($class);
		$subject = $this -> sanitizeString($subject);
		$student = $this -> sanitizeString($student);
		$codTeacher = $this -> sanitizeString($codTeacher);
		$date = $this -> sanitizeString($date);
		$hour = $this -> sanitizeString($hour);
		$absencenote = $this -> sanitizeString($absencenote);

		$result = $this->query("SELECT * FROM Assignments WHERE (classID='$class' AND subject='$subject' AND date='$date')");
	
		if($result->num_rows > 0) {
			return -1;
		}

		$result = $this->query("INSERT INTO Assignments(subject, date, classID, textAssignment, codFiscTeacher) 
								VALUES ('$subject', '$date', '$class', '$assignments', '$codTeacher')");
		
		if($result == FALSE) {
			die("ERROR: Assignments not inserted.");
		}
		
		
	}
}
