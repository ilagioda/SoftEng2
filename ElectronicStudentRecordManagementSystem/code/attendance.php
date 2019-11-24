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
    echo "<h3><i>$today</i></h3><br><br>";

    //--- DEBUG ---
    //echo $_REQUEST['class'];

    // Store in a variable the name of the selected class
    $chosenClass = $_REQUEST['class'];

    // Retrieve the student of the selected class
    $students = $teacher->getStudents2($chosenClass);
    $_SESSION['students'] = $students;
    ?>
    <script>
        /*********************************************/
        $(document).ready(function() {
            $("input[type='checkbox']").change(function() {
                // alert("Mi hai svegliato");
                alert("The once who called me has id: " + this.id);

                $.post("attendanceBackEnd.php", {
                        event: "presence",
                        i: this.id
                    },
                    function(data, status) {
                        alert("Data: " + data + "\nStatus: " + status);
                    });

            });
			
			$(function(){
				$('input:checkbox').each(function() {
					// Iterate over the checkboxes and set their "check" values based on the session data
					var $el = $(this);
					$el.prop('checked', sessionStorage[$el.prop('id')] === 'true');
				});

				$('input:checkbox').on('change', function() {
					// save the individual checkbox in the session inside the `change` event, 
					// using the checkbox "id" attribute
					var $el = $(this);
					sessionStorage[$el.prop('id')] = $el.is(':checked');
				});
			});
        });
		
        /******************************************* */

        function fillModalFieldsENTRANCE(obj){
            var studName = obj.getAttribute("data-name");
            var studSurname = obj.getAttribute("data-surname");
            var studSSN = obj.getAttribute("data-ssn");
            var studClass = obj.getAttribute("data-c");
            document.getElementById("modalEntrance-name").innerHTML = studName;
            document.getElementById("modalEntrance-surname").innerHTML = studSurname;
            document.getElementById("modalEntrance-ssn").innerHTML = studSSN;
            document.getElementById("modalEntrance-c").innerHTML = studClass;
        }

        function fillModalFieldsEXIT(obj){
            var studName = obj.getAttribute("data-name");
            var studSurname = obj.getAttribute("data-surname");
            var studSSN = obj.getAttribute("data-ssn");
            var studClass = obj.getAttribute("data-c");
            document.getElementById("modalExit-name").innerHTML = studName;
            document.getElementById("modalExit-surname").innerHTML = studSurname;
            document.getElementById("modalExit-ssn").innerHTML = studSSN;
            document.getElementById("modalExit-c").innerHTML = studClass;
        }

    </script>
<?php
    // Create the table containing the students
    // Check if the class has at least one student
    if (empty($students)) {
        // The class has no students
        echo "<p>No students in the selected class!</p>";
    } else {
        // The class has at least one student
        echo "<div class=\"table-responsive\">";
        echo "<table class=\"table table-striped table-bordered text-center\">";
        echo "<tr style=\"color: black; font-size: 20px;\"><td><b>Name</b></td><td><b>Surname</b></td><td><b>SSN</b><td><b>Presence/Absence</b><td><b>Late entrance</b><td><b>Early exit</b></td></tr>";
		
		$i = 0;
        foreach ($students as $stud) {
	
            $fields = explode(",", $stud);
            // $fields[0] --> name
            // $fields[1] --> surname
            // $fields[2] --> SSN
            // coloumns: name, surname, ssn, presence, lateEntrance, earlyExit
            echo <<<_ROW
                <tr>
                <td style="vertical-align: middle;">$fields[0]</td>
                <td style="vertical-align: middle;">$fields[1]</td>
                <td style="vertical-align: middle;">$fields[2]</td>
                <td style="vertical-align: middle;"> <label class="switch"> <input type="checkbox" id="$i"> <span class="slider round"></span> </label> </td>
                <td style="vertical-align: middle;"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myEntrance" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsENTRANCE(this)">
                Click
                </button></td>
                <td style="vertical-align: middle;"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myExit" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsEXIT(this)">
                Click
                </button></td>
_ROW;
            $i++;
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
                            <span id="modalEntrance-name"></span><br>
                            <span id="modalEntrance-surname"></span><br>
                            <span id="modalEntrance-ssn"></span><br>
                            <span id="modalEntrance-c"></span><br>
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

        echo <<<_MODALEXIT
        <div class="modal fade" id="myExit" tabindex="-1" role="dialog" aria-labelledby="myExitlabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myExitlabel">Record EARLY EXIT</h4>
                    </div>
                    <div class="modal-body">
                        <span id="modalExit-name"></span><br>
                        <span id="modalExit-surname"></span><br>
                        <span id="modalExit-ssn"></span><br>
                        <span id="modalExit-c"></span><br>
                        <span>TENDINA CON HOUR (1..6)</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
_MODALEXIT;

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