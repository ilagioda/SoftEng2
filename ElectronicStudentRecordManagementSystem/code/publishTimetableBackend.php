<?php

require_once("classTeacher.php");

if (!isset($_SESSION))
    session_start();
if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {

    // TODO -------------------------------------------------------------------------------------

} else {
    die("You are not supposed to be here.");
}
