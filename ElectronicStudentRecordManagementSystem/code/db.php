<?php

 class db{
    $servername = "localhost";
    $username = "username";
    $password = "password";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->close();

    function __construct()
    {
        $servername = "localhost";
        $username = "username";
        $password = "password";
        $dbname = "school";

        // Create connection
        $this->conn = new mysqli($servername, $username, $password,$dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
            $conn->close();
        }
    }
}

class dbAdmin extends db{

}

class dbParent extends db{
   



}



?>