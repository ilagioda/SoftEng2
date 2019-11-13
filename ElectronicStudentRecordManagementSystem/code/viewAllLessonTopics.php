<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
	
	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher";
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
    
?>

<ul class="nav nav-tabs">
  <li role="presentation"><a href="recordLesson.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>

	<?php 
		$lectures = $teacher->getLectures();
		foreach((array)$lectures as $value) {
	?>	
		<div class="panel panel-default">
			<div class="panel-body">
			<table class="table">
			<?php
				echo $value;
			?>
			
			</table>	
			</div>
		</div>
	<?php
		}
	?>


<?php 
    require_once("defaultFooter.php")
?>
