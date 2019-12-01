<?php

require_once("classTeacher.php");

if (!isset($_SESSION))
    session_start();
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    if (isset($_REQUEST["event"])) {

        if (isset($_REQUEST['date'])) {
            // $day = date('Y-m-j');
            $minWeek =  date("Y-m-d", strtotime('monday this week'));
            $maxWeek = date("Y-m-d", strtotime('friday this week'));

            if ($_REQUEST['date'] >= $minWeek) {
                $greaterMin = true;
            }
            if ($_REQUEST['date'] <= $maxWeek) {
                $lessMax = true;
            }
            if (!($greaterMin && $lessMax)) {
                echo "false";
                exit;
            }
        }

        if (($_REQUEST["event"] == "presence") && isset($_SESSION['students']) && isset($_REQUEST['i']) && isset($_REQUEST['date'])) {
            $students = $_SESSION['students'];
            $index = $_REQUEST['i'];
            $tuple = explode(",", $students[$index]);
            $ssn = $tuple[2];
            /* echo $ssn; */

            //require_once("classTeacher.php");

            $teacher = new Teacher();

            $day = $_REQUEST['date'];

            $text = $teacher->updateAttendance($ssn, $day);

            if (!$text) {
                echo "false";
            }
        } else if (($_REQUEST["event"] == "entrance") && isset($_REQUEST["ssn"]) && isset($_REQUEST["hour"]) && isset($_REQUEST['date'])) {
            /* LATE ENTRANCE */
            $ssn = $_REQUEST["ssn"];
            $hour = $_REQUEST["hour"];
            // $day = date('Y-m-j');
            $day = $_REQUEST['date'];

            $teacher = new Teacher();
            $ret = $teacher->recordLateEntrance($day, $ssn, $hour);
            if ($ret) {
                echo "$hour";
            } else {
                echo "false";
            }
        } else if (($_REQUEST["event"] == "exit") && isset($_REQUEST["ssn"]) && isset($_REQUEST["hour"]) && isset($_REQUEST['date'])) {
            /* EARLY EXIT */
            $ssn = $_REQUEST["ssn"];
            $hour = $_REQUEST["hour"];
            // $day = date('Y-m-j');
            $day = $_REQUEST['date'];
            $teacher = new Teacher();
            $ret = $teacher->recordEarlyExit($day, $ssn, $hour);
            if ($ret) {
                echo "$hour";
            } else {
                echo "false";
            }
        }
    }
} else {
    die("You are not supposed to be here.");
}
