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
			
        });
        /******************************************* */

        var req;
        var buttonID;

        function fillModalFieldsENTRANCE(obj){
            buttonID = obj.id;
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
            buttonID = obj.id;
            var studName = obj.getAttribute("data-name");
            var studSurname = obj.getAttribute("data-surname");
            var studSSN = obj.getAttribute("data-ssn");
            var studClass = obj.getAttribute("data-c");
            document.getElementById("modalExit-name").innerHTML = studName;
            document.getElementById("modalExit-surname").innerHTML = studSurname;
            document.getElementById("modalExit-ssn").innerHTML = studSSN;
            document.getElementById("modalExit-c").innerHTML = studClass;
        }

        function ajaxRequest(){
            var request;
            try {		
                request = new XMLHttpRequest();										// No Internet Explorer
            } catch (e1) {
                try {
                    request = new ActiveXObject("Msxm12.XMLHTTP");					// Internet Explorer 6+
                } catch (e2) {
                    try {
                        request = new ActiveXObject("Microsoft.XMLHTTP");			// Internet Explorer 5
                    } catch (e3) {
                        request = false;											// No supporto AJAX
                    }
                }
            }
            return request;
        }

        function recordEntrance(){
            // Retrieve the info needed to fill the DB 
            var ssn = document.getElementById("modalEntrance-ssn").innerHTML;
            var selectedIndex = document.getElementById("selectEntrance").selectedIndex;
            var listOfOptions = document.getElementById("selectEntrance").options;
            var hour = listOfOptions[selectedIndex].text;

            // AJAX request
            req = ajaxRequest();
            req.onreadystatechange = endEntrance;         
            req.open("POST", "attendanceBackEnd.php?"+"ssn="+ssn+"&hour="+hour+"&event=entrance", true); 
            req.send(); 
        }

        function recordExit(){
            // Retrieve the info needed to fill the DB 
            var ssn = document.getElementById("modalExit-ssn").innerHTML;
            var selectedIndex = document.getElementById("selectExit").selectedIndex;
            var listOfOptions = document.getElementById("selectExit").options;
            var hour = listOfOptions[selectedIndex].text;

            // AJAX request
            req = ajaxRequest();
            req.onreadystatechange = endExit;         
            req.open("POST", "attendanceBackEnd.php?"+"ssn="+ssn+"&hour="+hour+"&event=exit", true); 
            req.send();            
        }   

        function endEntrance(){

            if(req.readyState == 4 && (req.status == 0 || req.status == 200)) {
                if(req.responseText === "0"){
                    // Something went wrong...
                    window.alert("Oh no! Something went wrong...");
                } else {
                    // Everything is alright
                    var pID = buttonID.replace("entranceButton", "entrance");
                    document.getElementById(pID).innerHTML = "Entrance hour: "+req.responseText;
                    document.getElementById(pID).style.display = "block";
                    document.getElementById(buttonID).disabled = true; 
                    var checkID = buttonID.replace("entranceButton", "");
                    document.getElementById(checkID).checked = false;
                }
            }

            // Dismiss manually the modal window
            $('#myEntrance').modal('hide');
        }

        function endExit(){

            if(req.readyState == 4 && (req.status == 0 || req.status == 200)) {
                if(req.responseText == "0"){
                    // Something went wrong...
                    window.alert("Oh no! Something went wrong...");
                } else {
                    // Everything is alright
                    var pID = buttonID.replace("exitButton", "exit");
                    document.getElementById(pID).innerHTML = "Exit hour: "+req.responseText;
                    document.getElementById(pID).style.display = "block";
                    document.getElementById(buttonID).disabled = true; 
                    var checkID = buttonID.replace("exitButton", "");
                    document.getElementById(checkID).checked = true;
                }
            }

            // Dismiss manually the modal window
            $('#myExit').modal('hide');
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
                <td style="vertical-align: middle;"> <label class="switch"> <input type="checkbox" id="$i"
_ROW;

				$result = $teacher->checkAbsence($fields[2]);
				if($result == 1) {
					// assente
					echo "checked=checked";
				}
			echo <<<_ROW
				
				> <span class="slider round"></span> </label> </td>
                <td style="vertical-align: middle;"><p id="entrance$i" style="display: none"></p><button type="button" id="entranceButton$i" class="btn btn-primary" data-toggle="modal" data-target="#myEntrance" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsENTRANCE(this)">
                Click
                </button></td>
                <td style="vertical-align: middle;"><p id="exit$i" style="display: none"></p><button type="button" id="exitButton$i" class="btn btn-primary" data-toggle="modal" data-target="#myExit" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsEXIT(this)">
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
                        <div class="modal-body col-xs-6 col-xs-offset-3">
                            <form class="form-horizontal attendanceForm">
                                <div class="form-group">
                                    <label class="col-xs-6 control-label">Name:</label>
                                    <div class="col-xs-4">
                                        <p class="form-control-static" id="modalEntrance-name"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-6 control-label">Surname:</label>
                                    <div class="col-xs-4">
                                        <p class="form-control-static" id="modalEntrance-surname"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-6 control-label">SSN:</label>
                                    <div class="col-xs-4">
                                        <p class="form-control-static" id="modalEntrance-ssn"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-6 control-label">Class:</label>
                                    <div class="col-xs-4">
                                        <p class="form-control-static" id="modalEntrance-c"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-6 control-label">Hour:</label>
                                    <select class="form-control" id="selectEntrance">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                        <option>6</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="recordEntrance()">Save changes</button>
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
                    <div class="modal-body col-xs-6 col-xs-offset-3">
                        <form class="form-horizontal attendanceForm">
                            <div class="form-group">
                                <label class="col-xs-6 control-label">Name:</label>
                                <div class="col-xs-4">
                                    <p class="form-control-static" id="modalExit-name"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-6 control-label">Surname:</label>
                                <div class="col-xs-4">
                                    <p class="form-control-static" id="modalExit-surname"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-6 control-label">SSN:</label>
                                <div class="col-xs-4">
                                    <p class="form-control-static" id="modalExit-ssn"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-6 control-label">Class:</label>
                                <div class="col-xs-4">
                                    <p class="form-control-static" id="modalExit-c"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-6 control-label">Hour:</label>
                                <select class="form-control" id="selectExit">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="recordExit()">Save changes</button>
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