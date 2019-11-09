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

private function sanitizeString($var)
{
    $var = strip_tags($var);
    $var = htmlentities($var);
    if (get_magic_quotes_gpc())
        $var = stripslashes($var);
        return $this->conn->real_escape_string($var);
}

    protected function query($queryToBeExecuted)
    {
        return $this->conn->query($this->sanitizeString($queryToBeExecuted));
    }

    protected function prepareStatement($preparedStatement)
    {
        if (!$stmt = $this->conn->prepare($preparedStatement))
            die("Prepare phase Failed in the Transaction.");
        return $stmt;
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
        $name = "";
        $surname = "";
        $class = "";

        $compositionVector = array();

        $stmt = $this->prepareStatement(
        "SELECT `name`,`surname`,p.`classID` 
        FROM `Students` as s , `ProposedClasses` as p 
        WHERE s.codFisc = p.codFisc AND p.classID = ?");

        if (!$stmt->bind_param("s", $classID))
            die("Binding Failed in the Transaction.");


        if (!$stmt->bind_result($name, $surname, $class))
            die("Binding result Failed in the Transaction.");

        try {
            if (!$stmt->execute())
                throw new Exception("Select Failed.");

            while ($row = $stmt->fetch()) {
                $nameSurname = array($name, $surname);
                $compositionVector[$class] = $nameSurname;
            }

            /* close statement */
            $stmt->close();

            return $compositionVector;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            $$this->conn->rollback();
        }
    }

    function readAllClasses()
    {

        $sql = "SELECT classID FROM ProposedClasses";
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
        /* 
        This function receives the fiscal code of a child, 
        verifies if the child is actually a child of that parent
        and then returns the marks.

        */

        $result = queryMysql("SELECT * FROM Students WHERE codFisc=$CodFisc ;");

        if (!$result)
            die("Unable to select student $CodFisc");

        if (($row = $result->fetch_array(MYSQLI_ASSOC)) == NULL) {
            die("No student with ID $CodFisc ");
        }

        $parent1 = $row['emailP1'];
        $parent2 = $row['emailP2'];

        if ($_SESSION['user'] != $parent1 && $_SESSION['user'] != $parent2)
            die("You are not authorised to see this information.");

        $result = queryMysql("SELECT subject,date,hour FROM Marks WHERE codFisc=$CodFisc ;");

        $marks = "";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL) {
            $marks = $marks . $row['subject'] . ',' . $row['date'] . "," . $row['hour'] . ";";
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

    function insertMark($codStudent, $subject, $date, $hour, $mark){
        

    }
}
