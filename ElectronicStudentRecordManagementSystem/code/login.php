<?php
require_once 'basicChecks.php';
require_once 'db.php';
$error = $user = $pass = "";

if (isset($_POST['user'])) {
    $db = new db();
    $user = $db->sanitizeString($_POST['user']);
    $pass = $_POST['pass'];

    if ($user == "" || $pass == "") {
        $error = 'Not all fields were entered';
    } else {
        $pw = $db->getHashedPassword($user);
        if ($pw === false) {
            $error = "Invalid login attempt";
        }
        //either no email address or codFisc associated or error in query result
        else {
            $bool = password_verify($pass, $pw["hashedPassword"]);
            if ($bool) {
                if ($pw['firstLogin'] == 1) {
                    $_SESSION['firstUser'] = $pw['user'];
                    $_SESSION['firstRole'] = $pw['role'];
                    header("Location: firstLogin.php");
                    exit;
                } else {
                    $_SESSION['user'] = $pw['user'];
                    $_SESSION['role'] = $pw['role'];
                    //switch on role
                    if ($_SESSION['role'] == "admin") {
                        $_SESSION['sysAdmin'] = $pw['sysAdmin'];
                        header("Location: homepageAdmin.php?view=$user");
                    } else if ($_SESSION['role'] == "parent") {
                        header("Location: chooseChild.php?view=$user");
                    } else if ($_SESSION['role'] == "teacher") {
                        $_SESSION['principal'] = $pw['principal'];
                        header("Location: homepageTeacher.php?view=$user");
                    }
                    exit;
                }
            } else {
                $error = "Invalid login attempt"; //Wrong password
            }
        }
    }
}

$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "SessionTimeOut") {
    $msg = "Session expired. Please log in to continue using the application";
}

echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">';

require_once "defaultNavbar.php";

echo <<<_LOGINBODY

        <div class="card card-login mx-auto text-center bg-dark">
            <div class="card-header mx-auto bg-dark">
                <span> <img src="images/login_logo.png" class="w-75" alt="Logo"> </span><br />
                <span id="msg" value=""> $msg </span>
                <span class='error'>$error</span>

            </div>
            <form class="form-signin" action="login.php" method="post">
                <h2 class="form-signin-heading">Please sign in</h2>
                <label for="inputEmail" class="sr-only">Username</label>
                <input type="text" id="inputEmail" name="user" class="form-control" placeholder="User" required="" autofocus="">
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" class="form-control" name="pass" placeholder="Password" required="">
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

require_once "defaultFooter.php";
