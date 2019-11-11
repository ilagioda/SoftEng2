<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
    
    /* FIXME remove the next lines when login is implemented */
    
    $_SESSION['user'] = 'jon.snow@parent.it';
    $_SESSION['role'] = 'parent';
    
    /* End lines to be changed*/    
?>
<h1 align="center"> PARENT HOMEPAGE </h1>
<div class="centralDiv">
	<p>
        <?php
            echo "Welcome to your homepage PARENT ".$_SESSION["user"]."!";
        ?>
		
	</p>
</div>

<?php 
    require_once("defaultFooter.php")
?>
