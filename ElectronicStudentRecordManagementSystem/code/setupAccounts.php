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
if(isset($_POST['SSN']) && isset($_POST['password']) && isset($_POST['name']) && isset($_POST['surname'])){
    $us = $db->sanitizeString($_POST['SSN']);
    $hashedPw = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $res = $db->getHashedPassword($us);
    if($res === false){
        //ok
        if($_POST['radio_butt'] == "color-1" ){
            $res = $db->insertOfficialAccount("Admins", $us, $hashedPw, $name, $surname);
            if(!$res) $err = "Some error occurred, please retry";
            else $msg = "Admin correctly inserted.";
        }
        else if($_POST['radio_butt'] == "color-2" ){
            $res = $db->insertOfficialAccount("Teachers", $us, $hashedPw, $name, $surname);
            if(!$res) $err = "Some error occurred, please retry";
            else $msg = "Teacher correctly inserted.";

        }
        else if($_POST['radio_butt'] == "color-3" ){
            $res = $db->insertOfficialAccount("Principals", $us, $hashedPw, $name, $surname);
            if(!$res) $err = "Some error occurred, please retry";
            else $msg = "Principal correctly inserted.";

        }
        else{
            $err = "Some error occurred, please retry";
        }
    }
    else{
        //SSN already existing in database
        $err = "Some error occurred, please retry";
    }
}
?>

<div class="container">
	<div class="row">
    <?php
        if($err != ""){
            echo <<<_ERR
            <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><span class="glyphicon glyphicon-send"></span>$err</strong></div>
_ERR;
        } 
        if ($msg != ""){
            echo <<<_MSG
            <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><span class="glyphicon glyphicon-send"></span>$msg</strong></div>
_MSG;
        }
        ?>
        <form class="form-horizontal" method="POST" action="setupAccounts.php" onsubmit="return validateForm()">
        <h3 class="text-center">Register Official Accounts</h3><br>
        <div class="custom-radios text-center">
            <div>
                <input type="radio" id="color-1" name="radio_butt" value="color-1" checked>
                <label for="color-1">
                <span>
                    ADMIN
                </span>
                </label>
            </div>
            <div>
                <input type="radio" id="color-2" name="radio_butt" value="color-2">
                <label for="color-2">
                <span>
                    TEACHER
                </span>
                </label>
            </div>
            <div>
                <input type="radio" id="color-3" name="radio_butt" value="color-3">
                <label for="color-3">
                <span>
                PRINCIPAL
                </span>
                </label>
            </div>
        </div>
        <br>
        <div class="form-group text-center">
			<label for="SSN">SSN*: </label>
			<input type="text" class="form-control text-center" name="SSN" id="SSN" placeholder="Enter the SSN" autocomplete="off" title="Enter the SSN" required>
        </div>
        <div class="form-group text-center">
			<label for="password">Password*: </label>
			<input type="password" class="form-control text-center" name="password" id="password" placeholder="Enter the password" autocomplete="off" title="Enter the password" required>
        </div>
        <div class="form-group text-center">
			<label for="name">Name*: </label>
			<input type="text" class="form-control text-center" name="name" id="name" placeholder="Enter the name" autocomplete="off" title="Enter the name" required>
		</div>
        <div class="form-group text-center">
			<label for="surname">Surname*: </label>
			<input type="text" class="form-control text-center" name="surname" id="surname" placeholder="Enter the surname" autocomplete="off" title="Enter the surname" required>
        </div>
        <div class="text-center">
        <input type="reset" class="btn btn-default btn-lg text-center" style="margin-bottom: 40px;" name="Reset" id="Reset" value="Reset">
		<input type="submit" class="btn btn-primary btn-lg text-center" style="margin-bottom: 40px;" name="Signup" id="Signup" value="Confirm">
        </div>
        </form>
    </div>
</div>


<script>

function validateForm(){	
	var name = document.getElementById("name").value;
    var surname = document.getElementById("surname").value;
    var password = document.getElementById("password").value;
    var SSN = document.getElementById("SSN").value;
	if(name != null && name != "" && surname != null && surname != "" && password != null && password != "" && SSN != null && SSN != "")
		return true;
	else return false;
}

    </script>



<?php
require_once "defaultFooter.php";
?>

