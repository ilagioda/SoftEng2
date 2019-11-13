<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
	
	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher";
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
    $db = new dbTeacher();
	$error = 0;
  
    if(!isset($_REQUEST["comboClass"]) || !isset($_REQUEST["comboSubject"]) ||
		!isset($_REQUEST["lessontime"]) || !isset($_REQUEST["comboHour"]) || !isset($_REQUEST["topics"]) 
		|| empty($_REQUEST["topics"])){
        $error = 1;
    } else {
		$class = $_REQUEST["comboClass"];
		$subject = $_REQUEST["comboSubject"];
		$date = $_REQUEST["lessontime"];
		$hour = $_REQUEST["comboHour"];
		$topics = $_REQUEST["topics"];
		$db->insertDailyLesson($date, $hour, $class, $_SESSION['user'], $subject, $topics);
	}
	

    if($error != 0){ ?>
		<div class='alert alert-danger' role='alert'>
			<a href="#" class="alert-link"> Oh no! Something went wrong... </a>
		</div>
    <?php
	} else {
    ?> 
	
		<div class="alert alert-success" role="alert">
			<a href="#" class="alert-link"> Daily lesson successfully recorded!</a>
		</div>
		
	<?php 
		}
	?>
	<div>
	<form method="POST" action="homepageTeacher.php">
		<input type="submit" name="homepage" value="Homepage" class="btn btn-primary">
	</form>
	</div>

<?php 
    require_once("defaultFooter.php")
?>
