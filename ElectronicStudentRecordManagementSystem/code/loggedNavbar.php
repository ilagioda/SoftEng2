<?php

require_once("basicChecks.php");

$whoAmI = $_SESSION['role'];

switch($whoAmI){

    case "admin":
        require_once("loggedAdminNavbar.php");
        break;
    case "teacher":
        if($_SESSION['principal'] == 1)
            require_once("loggedPrincipalNavbar.php");
        else
            require_once("loggedTeacherNavbar.php");
        break;
    case "parent":
        require_once("loggedParentNavbar.php");
        break;
    default: 
        require_once("defaultNavbar.php");
        break;
}


?>