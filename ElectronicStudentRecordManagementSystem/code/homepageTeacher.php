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
	
	<form method="POST" action="dailyLessonTopics.php">
		<div class="btn-group">
		<button type="button" class="btn btn-primary">Daily lesson topics</button>
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
		<li><a href="recordLesson_pt2.php">New record</a></li>
		<li><a href="viewAllLessonTopics.php">View all records</a></li>
		</ul>
		</div>
	</form>
</div>

<?php 
    require_once("defaultFooter.php")
?>
