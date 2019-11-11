<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
    
    /* FIXME remove the next lines when login is implemented */
    
    $_SESSION['user'] = 'GDILRI';
    $_SESSION['role'] = 'admin';
    
    /* End lines to be changed*/    
?>
<h1 align="center"> ADMIN HOMEPAGE </h1>
<div class="centralDiv">
	<p>
        <?php
            echo "Welcome to your homepage ADMIN ".$_SESSION["user"]."!";
        ?>
		
	</p>
	<form method="POST" action="enrollstudent.php">
		<input type="submit" class="pulsante" name="enrollStudent" id="enrollStudent" value="Enroll new student">
	</form>
</div>

<?php 
    require_once("defaultFooter.php")
?>
