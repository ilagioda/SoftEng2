<?php

require_once "basicChecks.php";
$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedAdminNavbar.php";
}
require_once("db.php");
$db = new dbAdmin();
$err = $msg= "";


if(isset($_POST['title']) && isset($_POST['textCommunication']) && isset($_POST['comboClass']) && $_POST['title'] != "" && $_POST['textCommunication']!="" && $_POST['comboClass'] != ""){
    $title = $_POST['title'];
    $text = $_POST['textCommunication'];
    $class = $_POST['comboClass'];

    $res = $db->insertInternalCommunication($class, $title, $text);
    if(!$res) $err = "Some error occurred. Please retry later";
    else $msg = "Communication inserted correctly.";
    $_POST = array();
    
}
?>

<div class="container">
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
        <form class="form-horizontal" method="POST" action="publishInternalCommunications.php">
        <h3 class="text-center">Publish Internal Communications</h3><br>
        <div class="form-group text-center">
        <label for="comboClass">CLASS: </label>
            <select class="form-control" id="comboClass" name="comboClass" required> 
                    <option value="" disabled selected>Select class...</option>
                    <?php 
                        $classes=$db->getClasses();
                        while (($class = $classes->fetch_array(MYSQLI_ASSOC)) != null) {
                            echo "<option value=".$class['classID'].">".$class['classID']."</option>";
                        }
                    ?>
            </select>
        </div>
        <div class="form-group text-center">
			<label for="Title">TITLE: </label>
			<input type="text" class="form-control text-center" name="title" id="title" placeholder="Enter the TITLE" autocomplete="off" title="Enter the title" required>
        </div>
        <div class="form-group text-center">
			<label for="name">Text: </label>
			<input type="text" class="form-control text-center" name="textCommunication" id="textCommunication" placeholder="Enter the text" autocomplete="off" title="Enter the name" required>
		</div>
        <div class="text-center">
        <input type="reset" class="btn btn-default btn-lg text-center" style="margin-bottom: 40px;" name="Reset" id="Reset" value="Reset">
		<input type="submit" class="btn btn-primary btn-lg text-center" style="margin-bottom: 40px;" name="publish" id="publish" value="Publish">
        </div>
        </form>
    </div>
</div>

<?
require_once("defaultFooter.php");

?>