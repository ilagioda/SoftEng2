<?php

class db{

    function __construct()
    {
        $servername = "localhost";
        $username = "username";
        $password = "password";
        $dbname = "school";

        // Create connection
        $this->conn = new mysqli($servername, $username, $password,$dbname);

        // Check connection
        if ($this->conn->connect_error){
            die("Connection failed: " . $this->conn->connect_error);
            $this->conn->close();
        }
    }
}

class dbAdmin extends db{

}

class dbParent extends db{

    public function viewChildMarks($CodFisc){
        /* This function receives the fiscal code of a child*/
    }
}

class dbTeacher extends db{

}

?>