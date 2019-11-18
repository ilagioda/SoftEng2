<?php
require_once("basicChecks.php");
require_once("db.php");


if(isset($_POST['childIndex'])){
    /* coming from the same page, choosing the child */
    checkIfLogged();
    $index = $_POST['childIndex'];
    $_SESSION['child'] = $_SESSION['children'][$index]['codFisc'];
    $_SESSION['childName'] = $_SESSION['children'][$index]['name'];
    $_SESSION['childSurname'] = $_SESSION['children'][$index]['surname'];
    $_SESSION['class'] = $_SESSION['children'][$index]['classID'];
    $_SESSION['childIndex'] = $index;
    header('HTTP/1.1 307 Temporary Redirect');
    header('Location: homepageParent.php');
    exit;
}

if(isset($_POST['email']) /* TODO substitute when login is implemented !isset($_SESSION['user']) */){
    // Logging in

    /**
     * These two will already be set from the login
     */
    
    $_SESSION['user'] = $_POST['email']; //to be commented
    $_SESSION['role'] = "parent"; //to be commented

    // end lines ot be removed
    
    $db = new dbParent();
    $children = $db->retrieveChildren($_SESSION['user']);

    switch(count($children)){

        case 0: 
            $em = $_POST["email"]; //to be substituted with $_SESSION["user"]
            // no children for that email => display error
            require_once("defaultNavbar.php");
            echo<<<_ERROR
            <div class="text-center">
            <h1> No children registered to a class and related to email $em. Please try later to <a href=login.php>login</a></h1>
            </div>
_ERROR;
            exit;

        case 1: 
            // only one child
            $_SESSION['child'] = $children[0]['codFisc'];
            $_SESSION['childName'] = $children[0]['name'];
            $_SESSION['childSurname'] = $children[0]['surname'];
            $_SESSION['class'] = $children[0]['classID'];
            $_SESSION['numChild'] = 1;
            $_SESSION['children'] = $children;
            $_SESSION['childIndex'] = 0;
            header('HTTP/1.1 307 Temporary Redirect');
            header('Location: homepageParent.php');
            break;

        default:
            // more than one child, display children to be chosen
            $_SESSION['numChild'] = count($children);
            $_SESSION['children'] = $children;
            break;
    }
    
} else {
    header('HTTP/1.1 307 Temporary Redirect');
    header('Location: index.php');
    exit;
}

require_once("defaultNavbar.php");

?>

<div>
    <h2 class="text-center"> Choose a child</h2>
    <form action="chooseChild.php" method="POST">
        <table class="table table-striped">
            <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>Fiscal Code </th>
                <th>Class</th>
                <th></th>
            </tr>

<?php
            $i=0;
            // print all the children, one per row
            foreach($_SESSION['children'] as $child){
                echo<<<_CHILDROW

                <tr>
                    <td>$child[name]</td>
                    <td>$child[surname]</td>
                    <td>$child[codFisc]</td>
                    <td>$child[classID]</td>
                    <td><button type="submit" class="btn btn-default btn-sm" name='childIndex' value=$i>Select</td>
                </tr>
_CHILDROW;
                $i++;
        }
?>

        </table>
    </form>
</div>

<?php
require_once("defaultFooter.php")
?>