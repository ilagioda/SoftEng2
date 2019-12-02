<?php
require_once "basicChecks.php";

$loggedin = false;
if (isset($_SESSION['user'])) {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    require_once "defaultNavbar.php";
} else {
    require_once "loggedNavbar.php";
}
/**
 *  To be removed => here until logout is implemented
 * */
//$_SESSION = array();
require_once "db.php";
$db = new db();
?>

<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-sm-2 sidenav">
            <p><a href="#">Rules</a></p>
            <p><a href="#">History</a></p>
            <p><a href="#">Calendar</a></p>
            <p><a href="#">Language</a></p>
        </div>
        <div class="col-md-8 text-center">
            <h1>Welcome!</h1>
            <img id="central_image" src="images/NEWlogo.png">
            <div class="overflow-auto">
                <?php
                $res = $db->getAnnouncements();
                if(!$res) echo "<p>Some problem occurred. We're sorry.</p>";
                else{
                    if($res->num_rows == 0) 
                        echo <<<_NOANNOUNCEMENT
                        <div class="card-index">
                        <div class="card-header-index text-left">
                            NONE
                        </div>
                        <div class="card-body-index">
                            <h5 class="card-title-index"><strong> NONE </strong></h5>
                            <p>No announcement to be shown.</p>
                        </div>
                    </div>
_NOANNOUNCEMENT;
                    else{
                        foreach($res as $tuple){
                            $timestamp = $tuple['Timestamp'];
                            $text = $tuple['Text'];
                            $title = $tuple['Title'];

                            echo <<<_ANNOUNCEMENT
                            <div class="card-index">
                                <div class="card-header-index text-left">
                                    $timestamp
                                </div>
                                <div class="card-body-index">
                                    <h5 class="card-title-index"><strong> $title </strong></h5>
                                    <p>$text</p>
                                </div>
                            </div>
_ANNOUNCEMENT;
                        }
                    }
                }


                ?>
            </div>

        </div>
        <div class="col-sm-2 sidenav">
            <div class="well">
                <p><img class="ads_logos" src="images/fb_logo.png"></p>
                <!--Logo facebook -->
            </div>
            <div class="well">
                <p><img class="ads_logos" src="images/instagram_logo.png"></p>
                <!--Logo instagram -->
            </div>
            <div class="well">
                <p><img class="ads_logos" src="images/whatsapp_logo.png"></p>
                <!--Logo instagram -->
            </div>
            <div class="well">
                <p><img class="ads_logos" src="images/twitter_logo.png"></p>
                <!--Logo instagram -->
            </div>
        </div>
    </div>
</div>

<?php
require_once "defaultFooter.php"
?>