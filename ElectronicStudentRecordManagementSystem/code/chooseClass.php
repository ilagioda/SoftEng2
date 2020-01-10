<?php
require_once "basicChecks.php";

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    header("Location: login.php");
	exit;
} 

require_once "db.php";

if (isset($_REQUEST['classIndex'])) {
    /* coming from the same page, choosing the class */
    $index = $_REQUEST['classIndex'];
    $_SESSION['comboClass'] = $_SESSION['classes'][$index];
    $_SESSION['classIndex'] = $index;
    header('HTTP/1.1 307 Temporary Redirect');
    header('Location: homepageTeacher.php');
    exit;
}

require_once "loggedTeacherNavbar.php";

if (isset($_SESSION['user'])) {

    $db = new dbTeacher();
    $classes = $db->getClassesByTeacher2($_SESSION['user']);

    switch (count($classes)) {

        case 0:
            // no classes for that teacher => display error
            echo <<<_ERROR
            <div class="text-center">
            <h1> No classes assigned. <a></h1>
            </div>
_ERROR;
            exit;

        case 1:
            // only one class
            $_SESSION['comboClass'] = $classes[0];
            $_SESSION['numClasses'] = 1;
            $_SESSION['classes'] = $classes;
            $_SESSION['classIndex'] = 0;
            header('HTTP/1.1 307 Temporary Redirect');
            header('Location: homepageTeacher.php');
            break;

        default:
            // more than one class, display classes to be chosen
            $_SESSION['numClasses'] = count($classes);
            $_SESSION['classes'] = $classes;
            break;
    }

} else {
    header('HTTP/1.1 307 Temporary Redirect');
    header('Location: index.php');
    exit;
}

//require_once "defaultNavbar.php";

?>

<div>
    <h2 class="text-center"> Choose a class</h2>
    <form action="chooseClass.php" method="POST">
        <table class="table table-striped">
            <tr>
                <th></th>
				<th></th>
            </tr>

<?php
$i = 0;
// print all the classes, one per row
foreach ($_SESSION['classes'] as $class) {
    echo <<<_CLASSROW

                <tr>
                    <td>$class</td>
                    <td><button type="submit" class="btn btn-default btn-sm" name='classIndex' value=$i>Select</td>
                </tr>
_CLASSROW;
    $i++;
}
?>

        </table>
    </form>
</div>

<?php
require_once "defaultFooter.php"
?>