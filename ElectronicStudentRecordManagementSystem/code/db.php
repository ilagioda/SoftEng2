<?php

public class db{
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



}

public class dbAdmin extends db{

}

public class dbParent extends db{
   



}



?>