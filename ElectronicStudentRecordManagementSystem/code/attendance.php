<script type='text/javascript'>
    /*
        data-name="$fields[0]" data-surname="$fields[0]" data-ssn="$fields[0]" data-c="$chosenClass"
        <p id="modal-name"><p>
        <p id="modal-surname"><p>
        <p id="modal-ssn"><p>
        <p id="modal-c"><p>
     */

    var ATTRIBUTES = ['name', 'surname', 'ssn', 'c'];
    $('[data-toggle="modal"]').on('click', function(e) {
        // convert target (e.g. the button) to jquery object
        var $target = $(e.target);
        // modal targeted by the button
        var modalSelector = $target.data('target');
        // iterate over each possible data-* attribute
        ATTRIBUTES.forEach(function(attributeName) {
            // retrieve the dom element corresponding to current attribute
            var $modalAttribute = $(modalSelector + ' #modal-' + attributeName);
            var dataValue = $target.data(attributeName);
            // if the attribute value is empty, $target.data() will return undefined.
            // In JS boolean expressions return operands and are not coerced into
            // booleans. That way is dataValue is undefined, the left part of the following
            // Boolean expression evaluate to false and the empty string will be returned
            $modalAttribute.text(dataValue || '');
        });
    });
</script>

<?php

require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedTeacherNavbar.php";
}

require_once("classTeacher.php");

// Create a Teacher object
$teacher = new Teacher();
$classes = $teacher->getClassesByTeacher();

echo "<div class=text-center>";
echo "<h1>ATTENDANCE</h1>";

if (isset($_REQUEST['class'])) {

    // Print all the information about the students belonging to the selected class

    // Print the date
    $today = date('l, jS \of F Y');         // Format: Friday, 22nd of November 2019 --> to be shown on the screen
    $day = date('j-m-y');                   // Format: 22-11-19 --> to be saved into the attendance table in the DB
    echo "<h3><i>$today</i></h3><br><br>";

    //--- DEBUG ---
    //echo $_REQUEST['class'];

    // Store in a variable the name of the selected class
    $chosenClass = $_REQUEST['class'];

    // Retrieve the student of the selected class
    $students = $teacher->getStudents2($chosenClass);

    // Create the table containing the students
    // Check if the class has at least one student
    if (empty($students)) {
        // The class has no students
        echo "<p>No students in the selected class!!</p>";
    } else {
        // The class has at least one student
        echo "<div class=\"table-responsive\">";
        echo "<table class=\"table table-striped table-bordered text-center\">";
        echo "<tr style=\"color: black; font-size: 20px;\"><td><b>Name</b></td><td><b>Surname</b></td><td><b>SSN</b><td><b>Presence/Absence</b><td><b>Late entrance</b><td><b>Early exit</b></td></tr>";
        foreach ($students as $stud) {
            $fields = explode(",", $stud);
            // $fields[0] --> name
            // $fields[1] --> surname
            // $fields[2] --> SSN
            // coloumns: name, surname, ssn, presence, lateEntrance, earlyExit
            echo <<<_ROW
                <tr>
                <td align="center" style="vertical-align: middle;">$fields[0]</td>
                <td align="center" style="vertical-align: middle;">$fields[1]</td>
                <td align="center" style="vertical-align: middle;">$fields[2]</td>
                <td align="center" style="vertical-align: middle;"> <label class="switch"> <input type="checkbox"> <span class="slider round"></span> </label> </td>
                <td align="center" style="vertical-align: middle;"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myEntrance" data-name="$fields[0]" data-surname="$fields[0]" data-ssn="$fields[0]" data-c="$chosenClass">
                Click
                </button></td>
                <td align="center" style="vertical-align: middle;"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myExit" data-name="$fields[0]" data-surname="$fields[0]" data-ssn="$fields[0]" data-c="$chosenClass">
                Click
                </button></td>
_ROW;
        }
        echo "</table>";
        echo "</div>";

        echo <<<_MODALENTRANCE
            <div class="modal fade" id="myEntrance" tabindex="-1" role="dialog" aria-labelledby="myEntrancelabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myEntrancelabel">Record LATE ENTRANCE</h4>
                        </div>
                        <div class="modal-body">
                            <span id="modal-name"></span>
                            <span id="modal-surname"></span>
                            <span id="modal-ssn"></span>
                            <span id="modal-c"></span>
                            <span>TENDINA CON HOUR (1..6)</span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
_MODALENTRANCE;
    }
} else {

    //The class has not yet be chosen so the list of classses must be shown
    echo <<<_LIST
            <ul class="list-group">
_LIST;

    foreach ($classes as $class) {
        echo <<<_ROW
            <a href="attendance.php?class=$class" class="list-group-item">$class</a>     
_ROW;
    }

    echo <<<_ENDLIST
        </ul>
_ENDLIST;
}

echo "</div>";

require_once("defaultFooter.php");
?>