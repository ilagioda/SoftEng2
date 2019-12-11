<?php
if (!isset($_SESSION))
    session_start();


if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {

    require_once("classTeacher.php");

    $teacher = new Teacher();

    if (isset($_POST["event"])) {

        if ($_POST["event"] === "recordNote") {
            //Aggiungi nota allo studente
            if (isset($_POST["codFisc"]) && isset($_POST["classID"]) && isset($_POST["note"]) && isset($_POST["hour"]) && isset($_POST["subject"])) {
                /* 
              $.post("writeStudentNoteBackEnd.php", {
                    event: "recordNote",
                    codFisc: ssn,
                    classID: classID,
                    note: note,
                    hour: hour,
                    subject: selectedSubject
                    
                }
                */

                

                $ssnStudent = $_POST["codFisc"];
                $ssnTeacher = $_SESSION['user'];
                $note = $_POST["note"];
                $date =  date("Y-m-d");
                $hour = $_POST["hour"];
                $classID = $_POST["classID"];
                $subject = $_POST["subject"];

                echo $teacher->recordStudentNote($ssnStudent, $ssnTeacher, $subject, $note, $date, $hour);
                exit;
            } else {
                return false;
            }
        } elseif ($_POST["event"] === "removeNote") {

            if (isset($_POST["codFisc"]) && isset($_POST["subject"])) {
                /*
                  $.post("writeStudentNoteBackEnd.php", {
                    event: "removeNote",
                    codFisc: ssn,
                    subject: selectedSubject
                    }
                */

                $ssnStudent = $_POST["codFisc"];
                $ssnTeacher = $_SESSION['user'];
                $date =  date("Y-m-d");
                $subject = $_POST["subject"];
                echo $teacher->removeStudentNote($ssnStudent, $ssnTeacher, $date, $subject);
                exit;
            } else {
                return false;
            }
        }
    }
}
