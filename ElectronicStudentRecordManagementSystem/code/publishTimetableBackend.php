<?php

require_once("classTeacher.php");

if (!isset($_SESSION))
    session_start();
if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {

    require_once("db.php");
    $db = new dbAdmin();
    $timetable = array();

    // Check if all the data has been sent
    if(!isset($_REQUEST["class"]))
        die("Not all parameters has been passed!");
    for($i=1; $i<=6; $i++){
        if(!isset($_REQUEST["mon_".$i]) || !isset($_REQUEST["tue_".$i]) || !isset($_REQUEST["wed_".$i]) || !isset($_REQUEST["thu_".$i]) || !isset($_REQUEST["fri_".$i]))
            die("Not all parameters has been passed!");
    }

    $class = $_REQUEST["class"];

    // Retrieve the data that has been sent
    for($i=1; $i<=6; $i++){

        $row = array();

        $monSub = $_REQUEST["mon_".$i];
        $row[0] = "mon";
        $row[1] = $i;
        $row[2] = $monSub;
        array_push($timetable, $row);
        $tueSub = $_REQUEST["tue_".$i];
        $row[0] = "tue";
        $row[1] = $i;
        $row[2] = $tueSub;
        array_push($timetable, $row);
        $wedSub = $_REQUEST["wed_".$i];
        $row[0] = "wed";
        $row[1] = $i;
        $row[2] = $wedSub;
        array_push($timetable, $row);
        $thuSub = $_REQUEST["thu_".$i];
        $row[0] = "thu";
        $row[1] = $i;
        $row[2] = $thuSub;
        array_push($timetable, $row);
        $friSub = $_REQUEST["fri_".$i];
        $row[0] = "fri";
        $row[1] = $i;
        $row[2] = $friSub;
        array_push($timetable, $row);
    }

    $res = $db->storeTimetable($class, $timetable);

    if($res == 0){
        return "error";
    } else {
        return "ok";
    }

} else {
    die("You are not supposed to be here.");
}
