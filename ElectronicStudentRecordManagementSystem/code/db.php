<?php

require_once("basicChecks.php");

checkIfLogged();

class db{

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

    protected function query($queryToBeExecuted){
        return $this->conn->query(sanitizeString($queryToBeExecuted));
    }
}

class dbAdmin extends db
{

    function __construct()
    {
        if($_SESSION['role']!="admin") throw new Exception("Creating dbAdmin object for an user who is NOT logged in as an admin");
        parent::__construct();

    }
    function readClassCompositions($class)
    { }

    function readAllClasses()
    {

        $sql = "SELECT classID FROM ProposedClasses";
        $resultQuery = $this->conn->query($sql);

        if ($resultQuery->num_rows > 0) {
            $resultArray = array();
            // output data of each row
            while ($row = $resultQuery->fetch_assoc()) {
                array_push($resultArray,$row["classID"]);
            }
            return $resultArray;
        }
    }
}



class dbParent extends db
{

    function __construct()
    {
        if($_SESSION['role']!="parent") throw new Exception("Creating DbParent object for an user who is NOT logged in as a parent");
        parent::__construct();

    }

    public function viewChildMarks($CodFisc){
        /* 
        This function receives the fiscal code of a child, 
        verifies if the child is actually a child of that parent
        and then returns the marks.

        */

        $result = queryMysql("SELECT * FROM Students WHERE codFisc=$CodFisc ;");
        
        if (!$result) 
            die("Unable to select student $CodFisc");

        if(($row = $result->fetch_array(MYSQLI_ASSOC)) == NULL){
            die("No student with ID $CodFisc ");
        }

        $parent1 = $row['emailP1'];
        $parent2 = $row['emailP2'];

        if($_SESSION['user']!=$parent1 && $_SESSION['user']!=$parent2)
            die("You are not authorised to see this information.");
        
        $result = queryMysql("SELECT subject,date,hour FROM Marks WHERE codFisc=$CodFisc ;");

        $marks = "";

        while (($row = $result->fetch_array(MYSQLI_ASSOC)) != NULL){
            $marks = $marks . $row['subject'] . ',' . $row['date'] . "," . $row['hour'] . ";";
        }
        
        if($marks!="") $marks = substr($marks, 0, -1); // to remove the last ;

        return $marks;
    }
}

class dbTeacher extends db{

    function __construct()
    {
        if($_SESSION['role']!="teacher") throw new Exception("Creating DbTeacher object for an user who is NOT logged in as a teacher");
        parent::__construct();

    }
}

?>