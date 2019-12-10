<?php
if (!isset($_SESSION))
    session_start();


if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {

    require_once("classTeacher.php");

    $teacher = new Teacher();

    if (isset($_POST["event"])) {


        if ($_POST["event"] === "recordNote") {
            //Aggiungi nota allo studente
            if (isset($_POST["codFisc"]) && isset($_POST["classID"]) && isset($_POST["note"]) && isset($_POST["hour"])) {
                /* 
                  $.post("writeStudentNoteBackEnd.php", {
                    event: "recordNote",
                    codFisc: ssn,
                    classID: classID,
                    note: note,
                    hour: hour
                    }
                */

                $ssn = $_POST["ssn"];
                $hour = $_POST["hour"];
                $note = $_POST["note"];
                $classID = $_POST["classID"];

                //manca subject devi vedere come ricavarla o vedere se è utile averla nella tabella.

                echo $result = $teacher->recordStudentNote($ssn, $subject, $note, $date, $hour);
                exit;
            }
        } else {
            return false;
        }
    }
}
