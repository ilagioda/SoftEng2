<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
    
    /* FIXME remove the next lines when login is implemented */
    
    $_SESSION['user'] = 'GDILRI';
    $_SESSION['role'] = 'teacher';
    
    /* End lines to be changed*/    
?>
<h1 align="center"> TEACHER HOMEPAGE </h1>
<div class="centralDiv">
	<p>
        <?php
            echo "Welcome to your homepage TEACHER ".$_SESSION["user"]."!";
        ?>
	</p>
</div>

<?php 
    require_once("defaultFooter.php")
?>
