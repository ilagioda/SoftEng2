<?php
require_once 'redirect.php';
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
        //session_unset(); 	// Deprecated
        $_SESSION=array();
        
        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600*24,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
                );
        }
        session_destroy();  // destroy session
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