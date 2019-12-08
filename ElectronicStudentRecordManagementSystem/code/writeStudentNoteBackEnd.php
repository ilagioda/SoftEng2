<?php
require_once("classTeacher.php");

if (!isset($_SESSION))
    session_start();


if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {


    $teacher = new Teacher();


    if (isset($_POST["event"])) {


        if ($_POST["event"] === "recordNote") {
            //Aggiungi nota allo studente
            if (isset($_POST["codFisc"]) && isset($_POST["ssn"]) && isset($_POST["hour"]) && isset($_POST["note"]) && isset($_POST["classID"])) {
                /* 
                event: "recordNote",
                codFisc: ssn,
                hour: hour,
                note: note,
                classID: classID
                */

                $ssn = $_POST["ssn"];
                $hour = $_POST["hour"];
                $note = $_POST["note"];
                $classID = $_POST["classID"];

                //manca subject devi vedere come ricavarla o vedere se è utile averla nella tabella.

                $teacher->recordStudentNote($ssn, $subject, $note, $date, $hour);
            }
        }
    }
}

?>
