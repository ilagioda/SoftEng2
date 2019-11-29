<?php
require_once 'basicChecks.php';
require_once 'db.php';

$error="";

if (isset($_SESSION['firstUser']) && isset($_SESSION['firstRole'])) {
    require_once "defaultNavbar.php";

    if (isset($_POST['newPass']) && isset($_POST['newPassConfirm'])) {
        if ($_POST['newPassConfirm'] != $_POST['newPass']) {
            $error = "Passwords do not match.";
        } else {
            $db = new db();
            $user = $_SESSION['firstUser'];
            $newPw = $_POST['newPass'];
            $hashedNewPw = password_hash($newPw, PASSWORD_DEFAULT);

            // first letter uppercase - to achieve compatibility with DB
            $firstRole = ucfirst($_SESSION['firstRole'])."s";
            if (!$db->ChangePassword($user, $hashedNewPw, $firstRole,true)) {
                $error = "Sorry. Unable to change password. Please retry later on.";
            } else {
                //operation complete - password changed, firstUser and firstRole not needed anymore
                $_SESSION['user'] = $_SESSION['firstUser'];
                $_SESSION['role'] = $_SESSION['firstRole'];
                unset($_SESSION['firstUser']);
                unset($_SESSION['firstRole']);
                //switch on role
                if ($_SESSION['role'] == "admin") {
                    header("Location: homepageAdmin.php?view=$user");
                } else if ($_SESSION['role'] == "parent") {
                    header("Location: chooseChild.php?view=$user");
                } else if ($_SESSION['role'] == "teacher") {
                    header("Location: homepageTeacher.php?view=$user");
                } 
                else{
                    header("Location: index.php");
                }
                exit;
            }
        }

    }

    $msg = "";
    if (isset($_GET['msg']) && $_GET['msg'] == "SessionTimeOut") {
        $msg = "Session expired. Please log in to continue using the application";
    }

    echo <<<_LOGINBODY

        <div class="card card-login mx-auto text-center bg-dark">
            <div class="card-header mx-auto bg-dark">
                <span> <img src="images/login_logo.png" class="w-75" alt="Logo"> </span><br />
                <span id="msg" value=""> $msg </span>
                <span class='error'>$error</span>

            </div>
            <form class="form-signin" action="firstLogin.php" method="post">
                <h2 class="form-signin-heading"> Hi $_SESSION[firstUser], please change your password.</h2>
                <label for="newPass" class="sr-only">New password</label>
                <input type="password" id="newPass" class="form-control" name="newPass" placeholder="New password" required="">
                <label for="newPassConfirm" class="sr-only">New password confirmation</label>
                <input type="password" id="newPassConfirm" class="form-control" name="newPassConfirm" placeholder="Confirm new password" required="">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block login" type="submit">Sign in</button>
            </form>
        </div>
    </div>
</body>

</html>
_LOGINBODY;
} else if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case "admin":
            require_once "loggedAdminNavbar.php";
            break;
        case "parent":
            require_once "loggedParentNavbar.php";
            break;
        case "teacher":
            require_once "loggedTeacherNavbar.php";
            break;
        default:break;
    }
} else {
    header("Location: login.php");
}

require_once "defaultFooter.php";
