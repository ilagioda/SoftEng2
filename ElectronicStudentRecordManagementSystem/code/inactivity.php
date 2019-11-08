<?php
require_once("redirectHTTPS.php");
require_once("functions.php");

session_name("ESRMS"); //electronic student record management system
session_start();
$t=time();
$diff=0;
$max_idle_time=600;
if(isset($_SESSION['user'])){
    if (isset($_SESSION['time'])){
        $t0=$_SESSION['time'];
        $diff=($t-$t0);  // inactivity period
    }
    if ($diff > $max_idle_time) { // inactivity period too long
        
        destroySession();

        // redirect client to login page
        header('HTTP/1.1 307 temporary redirect');
        header('Location: login.php?msg=SessionTimeOut');
        exit; // IMPORTANT to avoid further output from the script
    } else {
        $_SESSION['time']=time(); /* update time */
        //echo '<html><body>Updated last access time: '.$_SESSION['time'].'</body></html>';
    }
}
?> 