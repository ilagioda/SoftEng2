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

if(!isset($_REQUEST['codFisc']) || !isset($_REQUEST['year']) || !isset($_REQUEST['month'])){

    if(!isset($_REQUEST['codFisc']) || !isset($_REQUEST['day'])){
        // TERZA RICHIESTA AJAX?? ----------------------------------------------------------------------------------
    } else {
        // Ajax request coming from showDaySlots() 
        $db = new dbTeacher();
        $codFisc = $_REQUEST['codFisc'];
        $day = $_REQUEST['day'];
        $slots = $db->showParentMeetingSlotsOfTheDay($codFisc, $day);    
        return $slots;
    }

} else {
    // Ajax request coming from updateCalendar() 
    $db = new dbTeacher();
    $codFisc = $_REQUEST['codFisc'];
    $year = $_REQUEST['year'];
    $month = $_REQUEST['month'];

    echo build_html_calendar($year,$month,$db->viewSlotsAlreadyProvided($codFisc));     
}

?>