<?php
require_once("basicChecks.php");

//var_dump($_SESSION);

if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {  
    require_once("db.php");
    $db = new dbAdmin();

    if(isset($_POST["event"]) && $_POST["event"]==="delete"){

        if(isset($_POST["ssn"])){
            $ssn = $_POST["ssn"];

           if($db->deleteTeacher($ssn))
            return true;
            else return false;



        }



    }

}



?>