<?php
if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {


    require_once("db.php");

    $db = new dbAdmin();



    if (isset($_POST["event"])) {

        if ($_POST["event"] == "delete") {
            //Delete a teacher
            if (isset($_POST["codFisc"])) {
                $ssn = $_POST["codFisc"];
                if ($db->deleteTeacher($ssn))
                    echo 1;
                else echo 0;
                exit;
            }
        } elseif ($_POST["event"] == "deleteClassSubjectForATeacher") {
            //Delete a subject that is teached from a teacher in a particular class
            /*
               codFisc: ssn,
                classID: classID,
                subject: subject
            */
            if (isset($_POST["codFisc"]) && isset($_POST["classID"]) && isset($_POST["subject"])) {

                $ssn = $_POST["codFisc"];
                $classID = $_POST["classID"];
                $subject = $_POST["subject"];

                if ($db->deleteSubjectTeachedInAClassByATeacher($ssn, $classID, $subject))
                    echo 1;
                else echo 0;
                exit;
            }
        }
    }
}
