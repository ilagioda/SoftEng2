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

    if(isset($_REQUEST['class'])){

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
        if(empty($students)){
            // The class has no students
            echo "<p>No students in the selected class!!</p>";
        } else {
            // The class has at least one student
            echo "<div class=\"table-responsive\">";
            echo "<table class=\"table table-striped table-bordered\">";
            echo "<tr style=\"color: black; font-size: 20px;\"><td><b>Name</b></td><td><b>Surname</b></td><td><b>SSN</b><td><b>Presence/Absence</b><td><b>ENTRATA</b><td><b>USCITA</b></td></tr>";
            foreach($students as $stud){
                $fields = explode(",", $stud);
                echo<<<_ROW
                <tr><td>$fields[0]</td><td>$fields[1]</td><td>$fields[2]</td><td></td><td></td><td></td></tr>
                _ROW;
            }

           // array_push($students, "".$row['name'].",".$row['surname'].",".$row['codFisc']."");
        
            echo "</table>";
            echo "</div>";
        }
        

        
    } else {

        //The class has not yet be chosen so the list of classses must be shown
        echo<<<_LIST
            <ul class="list-group">
        _LIST;

        foreach($classes as $class){
            echo<<<_ROW
            <a href="attendance.php?class=$class" class="list-group-item">$class</a>     
            _ROW;
        }

        echo<<<_ENDLIST
        </ul>
        _ENDLIST;
    }

echo "</div>";

require_once("defaultFooter.php");
?>


