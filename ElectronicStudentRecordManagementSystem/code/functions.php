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
    if(!isset($_SESSION['role']) || !isset($_SESSION['user'])){
        echo "<img src='images/gandalf.jpg' class='img-responsive center-block'>";
        die();
    }
}

function convertMark($rawMark){

    /**
     * Converts a literal mark (7,7+,7-,7/8,7.5) in the correspondent float value
     * 
     * @param rawMark is the literal mark (taken as output from the db)
     * @return float 
     */

    if(strpos($rawMark,"/")==true){
        // mark is of type 7/8

        // select the last character
        $mark = floatval(substr($rawMark,-1));

        // remove 0.25
        $mark-=0.25;
    } elseif(strpos($rawMark,"+")){
        // mark is of type 7+

        // select the first character
        $mark = floatval(substr($rawMark,0,1));

        // add 0.25
        $mark+=0.25;

    } elseif(strpos($rawMark,"-")){
        // mark is of type 7-

        // select the first character
        $mark = floatval(substr($rawMark,0,1));

        // remove 0.25
        $mark-=0.25;
    } else {
        // mark of type 7 or 7.5
        $mark = floatval($rawMark);
    }
    return $mark;
}
?>
