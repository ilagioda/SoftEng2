<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
    
    /* FIXME remove the next lines when login is implemented */
    
    $_SESSION['user'] = 'BigBoss';
    $_SESSION['role'] = 'principal';
    
    /* End lines to be changed*/    
?>
<h1 align="center"> PRINCIPAL HOMEPAGE </h1>
<div class="text-center">
	<p>
        <?php
            echo "<h2>Welcome to your homepage PRINCIPAL ".$_SESSION["user"]."!</h2><br>";
        ?>
		
	</p>
    <p>
        WORK IN PROGRESS :)
    </p>
</div>

<?php 
    require_once("defaultFooter.php")
?>