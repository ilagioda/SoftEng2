<?php
require_once 'basicChecks.php';
$error = $user = $pass = "";

if (isset($_POST['user'])) {
    $user = sanitizeString($_POST['user']);
    $pass = $_POST['pass'];

    if ($user == "" || $pass == "") {
        $error = 'Not all fields were entered';
    } else {
        $pass = md5($pass);
        $result = queryMysql("SELECT user,pass FROM users
        WHERE user='$user' AND pass='$pass'");
        if (!$result) {
            $msg = "Unable to login";
        } else {

            if ($result->num_rows == 0) {
                $error = "Invalid login attempt";
            } else {
                $_SESSION['user'] = $user;
                $_SESSION['time'] = time();
                $_SESSION['seats'] = "";
                header("Location: index.php?view=$user");
            }
        }
    }
}

$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "SessionTimeOut") {
    $msg = "Session expired. Please log in to continue using the application";
}

echo <<<_END
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
<body>
<nav class="navbar navbar-inverse">
<div class="container-fluid">
    <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="index.php"><img class="logo" src=images/logo.png></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
    <ul class="nav navbar-nav">
        <li class="active"><a href="index.php">Home</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Projects</a></li>
        <li><a href="#">Contact</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
    </ul>
    </div>
</div>
</nav>
<div class="container">
    <div class="card card-login mx-auto text-center bg-dark">
        <div class="card-header mx-auto bg-dark">
            <span> <img src="images/login_logo.png" class="w-75" alt="Logo"> </span><br/>
                        <span id="msg" value=""> $msg </span>
                        <span class='error'>$error</span>

        </div>
        <form class="form-signin">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required="" autofocus="">
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required="">
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block login" type="submit">Sign in</button>
      </form>

            </form>
        </div>
    </div>
</div>


  </body>
</html>
_END;
