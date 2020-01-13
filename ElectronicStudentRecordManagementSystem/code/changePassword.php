<?php
require_once("basicChecks.php");

//var_dump($_SESSION);

$loggedin = false;
if (isset($_SESSION['user'])) {
  $loggedin = true;
}
if (!$loggedin) {
  //require_once("defaultNavbar.php");
  header("Location: login.php");
} else {
    if($_SESSION['role'] == "teacher")
        require_once("loggedTeacherNavbar.php");
    elseif($_SESSION['role'] == "admin"){
        require_once("loggedAdminNavbar.php");
    }
    elseif($_SESSION['role'] == "parent"){
        require_once("loggedParentNavbar.php");
    }
}

require_once("db.php");
$db = new db();
$msg = "";
if(isset($_POST['oldPw']) && $_POST['oldPw'] != "" && isset($_POST['newPw']) && $_POST['newPw'] != "" && isset($_POST['confirmNewPw']) && $_POST['confirmNewPw'] != "" && $_POST['newPw'] == $_POST['confirmNewPw'] && $_POST['newPw'] != $_POST['oldPw']){
    $hashedPw = password_hash($_POST['newPw'], PASSWORD_DEFAULT);
    switch($_SESSION['role']){
        case "teacher":
            if($db->checkOldPw($_SESSION['user'],$_POST['oldPw'],'Teachers')){
                if($db->changePasswordOfficial($_SESSION['user'], $hashedPw, 'Teachers')){
                    $msg = "Password correctly changed.";
                }
                else $msg = "Some error occurred, please retry.";
            }
            else $msg = "Some error occurred, please retry.";
            break;
        case "parent":
            if($db->checkOldPw($_SESSION['user'],$_POST['oldPw'],'Parents')){
                if($db->changePassword($_SESSION['user'], $hashedPw, 'Parents', $first_time = false)){
                    $msg = "Password correctly changed.";
                }
            }
            else $msg = "Some error occurred, please retry.";
            break;
        case "admin":
            if($db->checkOldPw($_SESSION['user'],$_POST['oldPw'],'Admins')){
                if($db->changePasswordOfficial($_SESSION['user'], $hashedPw, 'Admins')){
                    $msg = "Password correctly changed.";
                }
                else $msg = "Some error occurred, please retry.";
            }
            else $msg = "Some error occurred, please retry.";
            break;
        default:
            $msg = "Some error occurred. We're sorry.";
            break;
    }
}

echo "<h3 class='text-center'>Hi $_SESSION[role], would you like to change your password?</h3>";

echo <<<_FORM
<form class="form-horizontal" action="changePassword.php" method="POST">
                        <div class="form-group text-center">
                            <label class="control-label text-center">Old password:</label>
                            <div>
                                <input type="password" class="form-control text-center" name="oldPw" id="oldPw">
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <label class="control-label text-center">New password:</label>
                            <div>
                                <input type="password" class="form-control text-center" name="newPw" id="newPw">
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <label class="control-label text-center">Confirm new password:</label>
                            <div>
                                <input type="password" class="form-control text-center" name="confirmNewPw" id="confirmNewPw">
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <div>
                                <button type="Submit" id="saveButton"  name ='saveButton' class="btn btn-primary" style="margin-top: 0px" value="">Save changes</button>
                            </div>
                        </div> 
</form>
_FORM;


echo '<div class="text-center"><h4>'.$msg.'</h4></div>';




require_once("defaultFooter.php");
?>