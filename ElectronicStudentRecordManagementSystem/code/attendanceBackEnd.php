<?php
if (isset($_POST["event"])) {

    if ($_POST["event"] == "presence") {
        if (isset($_POST["ssn"])) {

            echo $_POST["ssn"];
            /*  require_once("classTeacher.php");

            $teacher new Teacher();
            $day = date('j-m-y'); 

            $teacher->updateAttendance($ssn,$day);
  */




            
        }
    }
}
