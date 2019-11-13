<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");

/* TODO implement search for the children of the inserted e-mail */

if(isset($_POST['email']) /* TODO add when login is implemented !isset($_SESSION['user']) */){
    // Logging in
    if($_POST['email'] != 'wlt@gmail.it'){
        echo "mail not supported yet => works only for wlt@gmail.it right now";
    } else {
        $_SESSION['user'] = 'wlt@gmail.it';
        $_SESSION['role'] = "parent";
        $_SESSION['child'] = "FRCWTR";
        $_SESSION['childName'] = "Walter";
        $_SESSION['childSurname'] = "Forcignanò";
        $_SESSION['class'] = '1A';
    }
} else {
    unset($_SESSION['user']);
}

/* End lines to be changed*/
?>

<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-sm-2 sidenav">
            <p><a href="#">Link</a></p>
            <p><a href="#">Link</a></p>
            <p><a href="#">Link</a></p>
        </div>
        <div class="col-sm-8 text-left">
            <h1>Welcome</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <hr>
            <div class="text-center">
                <h1> PARENT HOMEPAGE </h1>
                <p>
                    <?php
                    if(isset($_SESSION['user'])){
                        
                        echo "Welcome to your homepage PARENT " . $_SESSION["user"] . "!";
                        echo "<h3>You can:<h3>";
                        echo <<< _OPLIST
                        <div class="btn-group-vertical" role="group">
                            <a href="viewMarks.php" class="btn btn-large btn-block btn-info" role="button">View $_SESSION[childName]'s marks</a>
                            <a href="" class="btn btn-large btn-block btn-info" role="button">To be implemented...</a>
                        </div>

_OPLIST;
                    } else {
                        // the user should log in
                        echo <<<_LOGIN
                        <h3> Please enter your e-mail </h3>
                        <br>
                        <form class="form-horizontal" action="homepageParent.php" method="POST">
                            <div class="form-group">
                                <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-10">
                                <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <input type="Submit" value='Sign in' class="btn btn-default">
                                </div>
                            </div>
                        </form>

_LOGIN;

                    }
                    ?>
                </p>
                <hr>
                
            </div>
        </div>
        <div class="col-sm-2 sidenav">
            <div class="well">
                <p>ADS</p>
            </div>
            <div class="well">
                <p>ADS</p>
            </div>
        </div>
    </div>
</div>

<?php
require_once("defaultFooter.php")
?>