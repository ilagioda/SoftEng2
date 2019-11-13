<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
	
	$_SESSION['user']="GNV";
	$_SESSION['role']="teacher";
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
    
?>


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