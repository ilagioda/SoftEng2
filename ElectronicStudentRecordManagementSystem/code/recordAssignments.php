<?php
	require_once("basicChecks.php");
	
	$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedTeacherNavbar.php";
}
	
	require_once("classTeacher.php");
	$teacher=new Teacher();
	$db = new dbTeacher();
    $err = $msg= "";

	
if(isset($_POST["assignments"]) && !empty(isset($_POST["assignments"])) && isset($_POST['assignmentstime']) 
	&& isset($_FILES['file']['name']) && $_FILES['file']['name'] !="" && isset($_POST['comboClass']) && isset($_POST['comboSubject'])){
		
    $date = $db->sanitizeString($_POST['assignmentstime']);
    $text = $db->sanitizeString($_POST['assignments']);
    $class = $db->sanitizeString($_POST['comboClass']);
    $subject = $db->sanitizeString($_POST['comboSubject']);
	
	$target_dir = "assignmentsMaterial/$class";
    $file = $_FILES['file']['name'];
    $path = pathinfo($file);
    $filename = $path['filename'];
    $ext = $path['extension'];
    $temp_name = $_FILES['file']['tmp_name'];

    //check if directory of class exists
    if(!file_exists($target_dir)){
        mkdir($target_dir);
    }
    $target_dir = $target_dir . "/" . $subject . "/";

    //If directory with the name of the subject does not exist, it will be created
    if(!file_exists($target_dir)){
        mkdir($target_dir);
    }

    $path_filename_ext = $target_dir.$filename.".".$ext;

    // Check if file already exists
    if (file_exists($path_filename_ext)) {
        $err = "Sorry, file already exists.";
    }else{
        //upload the file
        move_uploaded_file($temp_name,$path_filename_ext);
        if(!$db->insertAssignmentsMaterial($date, $path_filename_ext, $class, $subject, $text)){
			$err = "Some error occurred. Please retry.";
        } 
        $msg = "File Uploaded Successfully!";
    }
    $_POST = array();
    
} elseif(isset($_POST["comboClass"]) && isset($_POST["comboSubject"]) && isset($_POST["assignmentstime"])
			&& isset($_POST["assignments"]) && !empty($_POST["assignments"])) {
				
		$result = $db->insertNewAssignments($_POST['assignmentstime'], $_POST['comboClass'], $_POST['comboSubject'], $_POST['assignments']);
		if($result == -1) {
			$err = "Assignments already inserted for that subject! Try to edit the assignments in the section '<a href='viewAllAssignments.php'>View all records</a>";
		} else {
			$msg = "Assigments successfully recorded!";
		}
}
?>
	<div class="row">
    <?php
        if($err != ""){
            echo <<<_ERR
            <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><span class="glyphicon glyphicon-send"></span> $err</strong></div>
_ERR;
        } 
        if ($msg != ""){
            echo <<<_MSG
            <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><span class="glyphicon glyphicon-send"></span> $msg</strong></div>
_MSG;
        }
    
?>

<script>

function checkIfHoliday(day) {
	
	var holidays = ["2019-12-23","2019-12-24", "2019-12-25", "2019-12-26",
					"2019-12-27", "2019-12-28", "2019-12-29", "2019-12-30",
					"2019-12-31", "2020-01-01", "2020-01-02", "2020-01-03",
					"2020-01-04", "2020-02-22", "2020-02-23", "2020-02-24",
					"2020-02-25", "2020-02-26", "2020-04-09", "2020-04-10",
					"2020-04-11", "2020-04-12", "2020-04-13", "2020-04-14",
					"2020-05-02", "2020-05-02"];
						
						
	for(var i=0; i<holidays.length; i++) {
		var temp = new Date(holidays[i]);
		if(day.getTime() == temp.getTime()) {
			return true;
		}
	}
	
	return false;
}


$(document).ready(function(){
	$("#comboClass").change(function() {
		var comboClass = $("option:selected", this).val();

		$.ajax({
			type:		"POST",
			dataType:	"text",
			url:		"selectSubjects.php",
			data:		"comboClass="+comboClass,
			cache:		false,
			success:	function(response){
							$('#comboSubject').html(response);
						},
			error: 		function(){
							alert("Error: subjects not loaded");
						}
		});
	});
	
	var warning = $('<p class="text-danger">').text('Error: you must select a working day!')
	$('#assignmentstime').change(function(e) {

		var d = new Date(e.target.value)
		if(d.getDay() === 6 || d.getDay() === 0 || checkIfHoliday(d)) {
			$('#confirm').attr('disabled',true);
			$('#assignmentstime').after(warning);
		} else {
			warning.remove()
			$('#confirm').attr('disabled',false);
		}
	});
	
});


function bs_input_file() {
	$(".input-file").before(
		function() {
			if ( ! $(this).prev().hasClass('input-ghost') ) {
				var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
				element.attr("name",$(this).attr("name"));
				element.change(function(){
					element.next(element).find('input').val((element.val()).split('\\').pop());
				});
				$(this).find("button.btn-choose").click(function(){
					element.click();
				});
				$(this).find("button.btn-reset").click(function(){
					element.val(null);
					$(this).parents(".input-file").find('input').val('');
				});
				$(this).find('input').css("cursor","pointer");
				$(this).find('input').mousedown(function() {
					$(this).parents('.input-file').prev().click();
					return false;
				});
				return element;
			}
		}
	);
}
$(function() {
	bs_input_file();
});

</script>

<ul class="nav nav-tabs">

  <li role="presentation" class="active"><a href="#">New record</a></li>
  <li role="presentation"><a href="viewAllAssignments.php">View all records</a></li>
</ul>

<div class="panel panel-default" id="container">
	<div class="panel-body">
	<h1 class="text-center"> Record assignments </h1>
	<div class="form-group">

		<form class="navbar-form navbar form-inline" method="POST" action="recordAssignments.php" enctype="multipart/form-data">
			<table class="table table-hover">
				<tr><td><label>Class </label></td><td>
				<select class="form-control" id="comboClass" name="comboClass" style="width:100%" required> 
				<option value="" disabled selected>Select class...</option>

				<?php 
					$classes=$teacher->getClassesByTeacher();
					foreach($classes as $value) {
						echo "<option value=".$value.">".$value."</option>";
					}
				?>
				</select></td></tr>
				<tr><td><label>Subject </label></td><td>
				<select class="form-control" id="comboSubject" name="comboSubject" style="width:100%" required>
				<option value="" disabled selected>Select subject...</option>
				</select></td></tr>
				<tr><td><label>Date</label></td><td>  
				<input class="form-control" type="date" name="assignmentstime" id="assignmentstime"
						min="<?php echo date("Y-m-d");  ?>" max="<?php echo date("Y-m-d", strtotime('2020-06-10')); ?>"
						style="width:100%" required> </td></tr>
				<tr><td><label>Assignments</label></td>
					<td><textarea class="form-control" name="assignments" rows="4" cols="50" placeholder="Description..." style="width:100%" required></textarea>
					<span id="helpBlock" class="help-block">Add a description of the assignments and, optionally, load a file...</span>
					<div class="form-group" style="width:100%; margin-left:auto; margin-right:auto;">
						<div class="input-group input-file" name="file">
							
							<input type="text" class="form-control" placeholder='No file selected' />
							<span class="input-group-btn">
								<button class="btn btn-warning btn-choose" type="button">
								<span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span>&emsp;
								Choose </button>
							</span>
						</div>
					</div>
					
					</td>
				</tr>
			</table>
			<button type="reset" class = "btn btn-default" style="margin-right:5px">Reset</button>
			<button type="submit" id="confirm" class="btn btn-success">Confirm</button>
		</form>
		</div>
	</div>
</div>

<?php
	require_once("defaultFooter.php")
?>
