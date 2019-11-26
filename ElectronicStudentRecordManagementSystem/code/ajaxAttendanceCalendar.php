<?php

session_start();

if (!(isset($_SESSION['user']) && $_SESSION['role'] == "parent")) {
    // not logged in
    exit;
}


if(!isset($_REQUEST['codFisc']) || !isset($_REQUEST['year']) || !isset($_REQUEST['month']) ){
    //not an ajax request
    exit;
}


if ( empty($_SERVER['HTTPS']) || ($_SERVER['HTTPS'] === 'off')){
    // request not on https
    echo "not on https";
    exit;
}


require_once("functions.php");
require_once("db.php");

$db = new dbParent();
$codFisc = $_REQUEST['codFisc'];
$year = $_REQUEST['year'];
$month = $_REQUEST['month'];

echo build_html_calendar($year,$month,$db->retrieveAttendance($codFisc));

?>