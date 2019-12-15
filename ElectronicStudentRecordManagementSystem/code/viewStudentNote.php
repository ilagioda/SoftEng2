<?php
require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "parent") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    if (!isset($_SESSION['childName'])) {
        header("Location: chooseChild.php");
        exit;
    }
    require_once "loggedParentNavbar.php";
}
require_once("db.php");
$parentDB = new dbParent();

//checkIfLogged();

echo "<div class=text-center>";

if (isset($_SESSION['child'])) {

    //  echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'
    //    . $_SESSION["childName"] . ' ' . $_SESSION["childSurname"] . '</a>';


    $ssnStudent = $_SESSION['child'];

    $disciplinarNotes = $parentDB->retrieveStudentNotes($ssnStudent);

    if ($disciplinarNotes->num_rows == 0) {
        echo "No disciplinar notes were reported to " . $_SESSION['childName'] . " " . $_SESSION['childSurname'];
    } else {
        echo <<<_TABLEHEAD
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">DATE</th>
                            <th scope="col">Hour</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Note</th>
                        </tr>
                    </thead>
                    <tbody>
_TABLEHEAD;

        $i = 0;
        foreach ($disciplinarNotes as $note) {
            //var_dump($note);
            echo <<<_ROWS
                        <tr>
                            <th scope="row">$i</th>
                            <td>$note[date]</td>
                            <td>$note[hour]</td>
                            <td>$note[subject]</td>
                            <td>$note[Note]</td>
                        </tr>
_ROWS;
            $i++;
        }
        echo "</tbody>
        </table>";
    }
}


echo "</div>";
require_once("defaultFooter.php");