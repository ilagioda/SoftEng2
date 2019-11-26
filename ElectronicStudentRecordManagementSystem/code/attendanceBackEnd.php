<?php

require_once("classTeacher.php");

if (!isset($_SESSION))
    session_start();
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    if (isset($_REQUEST["event"])) {

        if (($_REQUEST["event"] == "presence") && isset($_SESSION['students']) && isset($_REQUEST['i'])) {
            $students = $_SESSION['students'];
            $index = $_REQUEST['i'];
            $tuple = explode(",", $students[$index]);
            $ssn = $tuple[2];
            /* echo $ssn; */

            //require_once("classTeacher.php");

            $teacher = new Teacher();

            $day = date('Y-m-j');

            $text = $teacher->updateAttendance($ssn, $day);

            if ($text) {
                echo "Event recorded.";
            } else {
                echo "Something has gone wrong.";
            }
        } else if (($_REQUEST["event"] == "entrance") && isset($_REQUEST["ssn"]) && isset($_REQUEST["hour"])) {
            /* LATE ENTRANCE */
            $ssn = $_REQUEST["ssn"];
            $hour = $_REQUEST["hour"];
            $day = date('Y-m-j');

            $teacher = new Teacher();
            $ret = $teacher->recordLateEntrance($day, $ssn, $hour);
            if ($ret) {
                echo "$hour";
            } else {
                echo "0";
            }
        } else if (($_REQUEST["event"] == "exit") && isset($_REQUEST["ssn"]) && isset($_REQUEST["hour"])) {
            /* EARLY EXIT */
            $ssn = $_REQUEST["ssn"];
            $hour = $_REQUEST["hour"];
            $day = date('Y-m-j');

            $teacher = new Teacher();
            $ret = $teacher->recordEarlyExit($day, $ssn, $hour);
            if ($ret) {
                echo "$hour";
            } else {
                echo "0";
            }
        }
    }
} else {
    die("You are not supposed to be here.");
}
