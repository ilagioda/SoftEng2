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

if(!isset($_REQUEST['codFiscTEACHER']) || !isset($_REQUEST['year']) || !isset($_REQUEST['month'])){

    if(!isset($_REQUEST['codFiscPARENT']) || !isset($_REQUEST['codFiscTEACHER']) || !isset($_REQUEST['day'])){
        echo "not an ajax request";
        exit;
    } else {
        if(!isset($_REQUEST['slotNb'])){
            // Ajax request coming from showDaySlots() 
            $db = new dbParent();
            $codFiscPARENT = $_REQUEST['codFiscPARENT'];
            $codFiscTEACHER = $_REQUEST['codFiscTEACHER'];
            $day = $_REQUEST['day'];
            $slots = $db->showParentMeetingSlotsOfTheDay($codFisc, $day);    // TODO questa 1
            echo $slots;
        } else {
            // Ajax request coming from provideSlotParentMeetings()
            $db = new dbParent();
            $codFiscPARENT = $_REQUEST['codFiscPARENT'];
            $codFiscTEACHER = $_REQUEST['codFiscTEACHER'];
            $day = $_REQUEST['day'];
            $slotNb = $_REQUEST['slotNb'];
            $color = $db->provideSlot($codFisc, $day, $slotNb);    // TODO questa 2
            echo $color;
        }
    }

} else {

    // Ajax request coming from updateCalendar() 
    $db = new dbParent();
    $codFiscTEACHER = $_REQUEST['codFiscTEACHER'];
    $year = $_REQUEST['year'];
    $month = $_REQUEST['month'];
    echo build_html_calendar($year,$month,$db->viewTeacherSlots($codFiscTEACHER));    

}

?>