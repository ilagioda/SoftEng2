<?php

session_start();

if (!(isset($_SESSION['user']) && $_SESSION['role'] == "parent")) {
    // not logged in
    exit;
}


if ( empty($_SERVER['HTTPS']) || ($_SERVER['HTTPS'] === 'off')){
    // request not on https
    echo "not on https";
    exit;
}

require_once("functions.php");
require_once("db.php");

// Define constants
$TEACHER_SSN = 'codFiscTEACHER';
$EMAIL_PARENT = 'mailPARENT';
$YEAR = 'year';
$MONTH = 'month';
$DAY = 'day';
$SLOT_NB = 'slotNb';
$QUARTER_NB = 'quarterNb';

if(!isset($_REQUEST[$TEACHER_SSN]) || !isset($_REQUEST[$YEAR]) || !isset($_REQUEST[$MONTH])){

    if(!isset($_REQUEST[$EMAIL_PARENT]) || !isset($_REQUEST[$TEACHER_SSN]) || !isset($_REQUEST[$DAY])){
        echo "not an ajax request";
        exit;
    } else {
        if(!isset($_REQUEST[$SLOT_NB]) || !isset($_REQUEST[$QUARTER_NB])){
            // Ajax request coming from showDaySlots() 
            $db = new dbParent();
            $mailPARENT = $_REQUEST[$EMAIL_PARENT];
            $codFiscTEACHER = $_REQUEST[$TEACHER_SSN];
            $day = $_REQUEST[$DAY];
            $slots = $db->getTeacherSlotsByDay($codFiscTEACHER, $day, $mailPARENT);
            echo $slots;
        } else {
            // Ajax request coming from provideSlotParentMeetings()
            $db = new dbParent();
            $mailPARENT = $_REQUEST[$EMAIL_PARENT];
            $codFiscTEACHER = $_REQUEST[$TEACHER_SSN];
            $day = $_REQUEST[$DAY];
            $slotNb = $_REQUEST[$SLOT_NB];
            $quarterNb = $_REQUEST[$QUARTER_NB];
            $color = $db->bookSlot($codFiscTEACHER, $mailPARENT, $day, $slotNb, $quarterNb);
            echo $color;
        }
    }

} else {

    // Ajax request coming from updateCalendar() 
    $db = new dbParent();
    $codFiscTEACHER = $_REQUEST[$TEACHER_SSN];
    $year = $_REQUEST[$YEAR];
    $month = $_REQUEST[$MONTH];
    echo build_html_calendar($year,$month,$db->viewTeacherSlots($codFiscTEACHER));    

}

?>