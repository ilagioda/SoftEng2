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


    protected function sanitizeString($var)
    {
        $var = strip_tags($var);
        $var = htmlentities($var);
        if (get_magic_quotes_gpc())
            $var = stripslashes($var);
        return $this->conn->real_escape_string($var);
    }

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
        $result = $this->query("SELECT email, hashedPassword, firstLogin FROM Parents");
        return $result;
    }
    public function ChangePassword($to_address, $hashed_pw){
        $this->query("UPDATE Parents SET hashedPassword = '$hashed_pw' WHERE email='$to_address'");
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
    { }

    function getStudentsByClass($class){

        $class = $this -> sanitizeString($class);
        
        $result = $this->query("SELECT * FROM students WHERE classId='$class'");
        
        if (!$result) 
            die("Unable to select students from $class.");

        $students="";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL){
                $students = $students . "<tr><td>" . $row['surname'] . "</td><td>" . $row['name'] . "</td><td><form method=\"post\" action=\"studentMarks.php\"> <input type=\"hidden\" name=\"codStudent\" value=" . $row['codFisc'] . "><input type=\"submit\", id=\"" . $row['codFisc'] . "\" value=\"Add Grade\"></form></td></tr><br>";
        }
        
        return $students;

    }
}
