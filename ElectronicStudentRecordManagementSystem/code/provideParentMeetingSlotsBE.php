<?php

session_start();

if (!(isset($_SESSION['user']) && $_SESSION['role'] == "teacher")) {
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
$TEACHER_SSN = 'codFisc';
$YEAR = 'year';
$MONTH = 'month';
$DAY = 'day';
$SLOT_NB = 'slotNb';

if(!isset($_REQUEST[$TEACHER_SSN]) || !isset($_REQUEST[$YEAR]) || !isset($_REQUEST[$MONTH])){

    if(!isset($_REQUEST[$TEACHER_SSN]) || !isset($_REQUEST[$DAY])){
        echo "not an ajax request";
        exit;
    } else {
        if(!isset($_REQUEST[$SLOT_NB])){
            // Ajax request coming from showDaySlots() 
            $db = new dbTeacher();
            $codFisc = $_REQUEST[$TEACHER_SSN];
            $day = $_REQUEST[$DAY];
            $slots = $db->showParentMeetingSlotsOfTheDay($codFisc, $day);    
            echo $slots;
        } else {
            // Ajax request coming from provideSlotParentMeetings()
            $db = new dbTeacher();
            $codFisc = $_REQUEST[$TEACHER_SSN];
            $day = $_REQUEST[$DAY];
            $slotNb = $_REQUEST[$SLOT_NB];
            $color = $db->provideSlot($codFisc, $day, $slotNb);    
            echo $color;
        }
    }

} else {

    // Ajax request coming from updateCalendar() 
    $db = new dbTeacher();
    $codFisc = $_REQUEST[$TEACHER_SSN];
    $year = $_REQUEST[$YEAR];
    $month = $_REQUEST[$MONTH];
    echo build_html_calendar($year,$month,$db->viewSlotsAlreadyProvided($codFisc));    

}

?>