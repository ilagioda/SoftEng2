<?php
    require_once("basicChecks.php");

    $loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher" && $_SESSION['principal']) {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedNavbar.php";
}
 
?>
<div class="text-center">
	<p>
        <?php
            echo "<h1>Welcome to your homepage ".$_SESSION["user"]."!</h1><br>";
        ?>
		
	</p>
    <p>
        WORK IN PROGRESS :)
    </p>
</div>

<?php 
    require_once("defaultFooter.php")
?>