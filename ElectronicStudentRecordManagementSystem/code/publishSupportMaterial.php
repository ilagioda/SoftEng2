<?php

//support material: in table: timestamp, title, String(file), class, dimension
//                  in viewing: title, timestamp, file, dimension

require_once "basicChecks.php";
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
require_once("db.php");
require_once("classTeacher.php");
$teacher=new Teacher();
$db = new dbTeacher();
$err = $msg= "";

if(isset($_POST['title']) && isset($_FILES['file']['name']) && $_POST['title'] != "" && $_FILES['file']['name'] !="" && isset($_POST['comboClass']) && isset($_POST['comboSubject'])){
    $title = $db->sanitizeString($_POST['title']);
    //$file = $db->sanitizeString($_POST['file']);
    $class = $db->sanitizeString($_POST['comboClass']);
    $subject = $db->sanitizeString($_POST['comboSubject']);

    $target_dir = "supportMaterial/$class";
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
        if(!move_uploaded_file($temp_name,$path_filename_ext)){
            $err = "Some error occurred while uploading the file. Please retry.";
        }
        else{
            if(!$db->insertSupportMaterial($title, $path_filename_ext, $_FILES['file']['size'], $class, $subject)){
                $err = "Some error occurred. Please retry.";
            }
            $msg = "Congratulations! File Uploaded Successfully.";
        }
    }
    $_POST = array();
    
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
        <form class="form-horizontal" method="POST" action="publishSupportMaterial.php" enctype="multipart/form-data">
        <h3 class="text-center">Publish support material</h3><br>
        <div class="form-group text-center">
        <label for="comboClass">CLASS: </label>
            <select class="form-control" id="comboClass" name="comboClass" required> 
                    <option value="" disabled selected>Select class...</option>
                    <?php 
                        $classes=$teacher->getClassesByTeacher();
                        foreach($classes as $value) {
                            echo "<option value=".$value.">".$value."</option>";
                        }
                    ?>
            </select>
        </div>

        <div class="form-group text-center">
        <label for="Subject">SUBJECT: </label>
        <select class="form-control" id="comboSubject" name="comboSubject" required>
                <option value="" disabled selected>Select subject...</option>
        </select>
                    </div>

        <div class="form-group text-center">
			<label for="title">TITLE: </label>
			<input type="text" class="form-control text-center" name="title" id="title" placeholder="Enter the TITLE" autocomplete="off" title="Enter the title" required>
        </div>

        <div class="form-group" style="width:30%; margin-left:auto; margin-right:auto;">
            <div class="input-group input-file" name="file">
                <span class="input-group-btn">
                    <button class="btn btn-default btn-choose" type="button">Choose</button>
                </span>
                <input type="text" class="form-control" placeholder='Choose a file...' />
                <span class="input-group-btn">
                    <button class="btn btn-warning btn-reset" type="button">Reset</button>
                </span>
            </div>
	    </div>
        
        <div class="text-center">
        <input type="reset" class="btn btn-default btn-lg text-center" name="Reset" id="Reset" value="Reset">
		<input type="submit" class="btn btn-primary btn-lg text-center" name="Publish" id="Publish" value="Publish">
        </div>
        </form>
    </div>

<?php
require_once("defaultFooter.php");

?>


<script>
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
});


    </script>