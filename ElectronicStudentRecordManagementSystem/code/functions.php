<?php 

function destroySession()
{
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
}



function checkIfLogged(){
    if(!isset($_SESSION['role'])){
        echo "<img src='images/gandalf.jpg' class='img-responsive center-block'>";
        die();
    }
}
?>
