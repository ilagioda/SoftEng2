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
echo "<h1>ATTENDANCE</h1><br><br>";

if (isset($_REQUEST['class'])) {

    // Print all the information about the students belonging to the selected class

    // Let the teacher choose the day

?>

<div class="container-fluid">
    <form class="form" method="POST" action="attendance.php">
        Choose the day:
        <input class="form-control text-center" type="date" name="dateRequest" min="<?php echo date("Y-m-d", strtotime('monday this week'));  ?>" max="
        <?php
            if (date("Y-m-d") <= date("Y-m-d", strtotime('friday this week'))) {
                echo date("Y-m-d");
            } else {
                echo date("Y-m-d", strtotime('friday this week'));
            }
            ?>">
        <input type="hidden" id="classID" name="class" value="<?php echo $_REQUEST['class'] ?>">
        <button type="submit" class="btn btn-success">Confirm date</button>
    </form>
</div><br><br>

<?php

    // Print the info about the date
    if (isset($_REQUEST['dateRequest'])) {
        $date = $_REQUEST['dateRequest'];                    // Format: 2019-11-22 --> to be put into the DB
        $day = date('l, jS \of F Y', strtotime($date));      // Format: Friday, 22nd of November 2019 --> to be shown on the screen
    } else {
        $date = date('Y-m-j');
        $day = date('l, jS \of F Y');         
    }
    echo "<p id='hiddenDate' style='display: none;'>$date</p>";
    echo "<h3><i>$day</i></h3><br><br>";

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

                var switchID;
                // alert("The once who called me has id: " + this.id);
                switchID = this.id;
                var entrance = $("#entranceButton" + switchID).text().trim();
                var exit = $("#exitButton" + switchID).text().trim();
                // alert(entrance);
                // alert(exit);
                var checkedID = document.getElementById(switchID).checked;
                //alert(checkedID);

                if ((entrance == "Entrance") && (exit == "Exit")) {
                    //alert("Sono qui");
                    alert($("#hiddenDate").text());
                    
                    var dateRequested = $("#hiddenDate").text();
                    $.post("attendanceBackEnd.php", {
                            event: "presence",
                            i: this.id,
                            date: dateRequested
                        },
                        function(data, status) {
                            // if Something has gone wrong. then adjust the toggle to the previous color.
                            if (data == "Something has gone wrong.") {
                                alert("The student has a record for late entrance or early exit, remove them to change the presence.");
                                //Va Cambiato il bottone e messo nello stato precedente.
                                //document.getElementById(switchID).checked = false or true;

                                if (document.getElementById(switchID).checked == true) {
                                    document.getElementById(switchID).checked = false;
                                } else {
                                    document.getElementById(switchID).checked = true;
                                }
                            }
                        });
                } else {
                    //Va Cambiato il bottone e messo nello stato precedente.
                    if (document.getElementById(switchID).checked == true) {
                        document.getElementById(switchID).checked = false;
                    } else {
                        document.getElementById(switchID).checked = true;
                    }
                    //document.getElementById(switchID).checked = false or true;
                }
            });
        });

        /******************************************* */

        // Variables used for late entrance and early exit
        var req;
        var buttonID;

        function fillModalFieldsENTRANCE(obj) {

            // Retrieve and store the button id from which this function has been called 
            buttonID = obj.id;

            // Retrieve the information about student in order to show it in the modal window
            var studName = obj.getAttribute("data-name");
            var studSurname = obj.getAttribute("data-surname");
            var studSSN = obj.getAttribute("data-ssn");
            var studClass = obj.getAttribute("data-c");

            // Fill the modal with the student information
            document.getElementById("modalEntrance-name").innerHTML = studName;
            document.getElementById("modalEntrance-surname").innerHTML = studSurname;
            document.getElementById("modalEntrance-ssn").innerHTML = studSSN;
            document.getElementById("modalEntrance-c").innerHTML = studClass;

            // Retrieve the information about the entrance hour (entranceHour = 0 in case of not-late-entrance)
            var num = buttonID.replace("entranceButton", "");
            var exitP = document.getElementById("exitButton" + num).innerHTML;
            var exitHour;
            if (exitP === "Exit") {
                exitHour = 0;
            } else {
                var exitHourString = exitP.replace("Hour: ", "");
                exitHour = parseInt(exitHourString);
            }

            // Fill the "menu a tendina" with the correct labels (in case of late entrance, from lateEntranceHour to 6)
            var select = document.getElementById("selectEntrance");
            var child = select.lastElementChild;
            while (child) {
                select.removeChild(child);
                child = select.lastElementChild;
            }
            if (exitHour >= 1 && exitHour <= 6) {
                // admissible number
                for (let i = 1; i <= exitHour; i++) {
                    option = document.createElement('option');
                    option.textContent = i;
                    select.appendChild(option);
                }
            } else {
                // not admissible number
                for (let i = 1; i < 7; i++) {
                    option = document.createElement('option');
                    option.textContent = i;
                    select.appendChild(option);
                }
            }
        }

        function fillModalFieldsEXIT(obj) {

            // Retrieve and store the button id from which this function has been called 
            buttonID = obj.id;

            // Retrieve the information about student in order to show it in the modal window
            var studName = obj.getAttribute("data-name");
            var studSurname = obj.getAttribute("data-surname");
            var studSSN = obj.getAttribute("data-ssn");
            var studClass = obj.getAttribute("data-c");

            // Fill the modal with the student information
            document.getElementById("modalExit-name").innerHTML = studName;
            document.getElementById("modalExit-surname").innerHTML = studSurname;
            document.getElementById("modalExit-ssn").innerHTML = studSSN;
            document.getElementById("modalExit-c").innerHTML = studClass;

            // Retrieve the information about the entrance hour (entranceHour = 0 in case of not-late-entrance)
            var num = buttonID.replace("exitButton", "");
            var entranceP = document.getElementById("entranceButton" + num).innerHTML;
            var entranceHour;
            if (entranceP === "Entrance") {
                entranceHour = 0;
            } else {
                var entranceHourString = entranceP.replace("Hour: ", "");
                entranceHour = parseInt(entranceHourString);
            }

            // Fill the "menu a tendina" with the correct labels (in case of late entrance, from lateEntranceHour to 6)
            var select = document.getElementById("selectExit");
            var child = select.lastElementChild;
            while (child) {
                select.removeChild(child);
                child = select.lastElementChild;
            }
            if (entranceHour >= 1 && entranceHour <= 6) {
                // admissible number
                for (let i = entranceHour; i < 7; i++) {
                    option = document.createElement('option');
                    option.textContent = i;
                    select.appendChild(option);
                }
            } else {
                // not admissible number
                for (let i = 1; i < 7; i++) {
                    option = document.createElement('option');
                    option.textContent = i;
                    select.appendChild(option);
                }
            }
        }

        function ajaxRequest() {
            var request;
            try {
                request = new XMLHttpRequest(); // No Internet Explorer
            } catch (e1) {
                try {
                    request = new ActiveXObject("Msxm12.XMLHTTP"); // Internet Explorer 6+
                } catch (e2) {
                    try {
                        request = new ActiveXObject("Microsoft.XMLHTTP"); // Internet Explorer 5
                    } catch (e3) {
                        request = false; // No supporto AJAX
                    }
                }
            }
            return request;
        }

        function recordEntrance(element) {
            // Retrieve the information needed to fill the DB 
            var ssn = document.getElementById("modalEntrance-ssn").innerHTML;
            var day = document.getElementById("hiddenDate").innerHTML;    
            var hour;

            // Understand if this function has been called from the "Remove" or "Save changes" button
            var callingButton = element.innerHTML;
            if (callingButton === "Remove") {
                // The recordEntrance function has been called from the "Remove" button
                hour = 0;
            } else {
                // The recordEntrance function has been called from the "Save changes" button
                var selectedIndex = document.getElementById("selectEntrance").selectedIndex;
                var listOfOptions = document.getElementById("selectEntrance").options;
                hour = listOfOptions[selectedIndex].text;
            }

            // AJAX request
            req = ajaxRequest();
            req.onreadystatechange = endEntrance;
            req.open("POST", "attendanceBackEnd.php?" + "ssn=" + ssn + "&hour=" + hour + "&date=" + day + "&event=entrance", true);
            req.send();
        }

        function recordExit(element) {
            // Retrieve the information needed to fill the DB 
            var ssn = document.getElementById("modalExit-ssn").innerHTML;
            var day = document.getElementById("hiddenDate").innerHTML;    
            var hour;

            // Understand if this function has been called from the "Remove" or "Save changes" button
            var callingButton = element.innerHTML;
            if (callingButton === "Remove") {
                // The recordExit function has been called from the "Remove" button
                hour = 0;
            } else {
                // The recordExit function has been called from the "Save changes" button
                var selectedIndex = document.getElementById("selectExit").selectedIndex;
                var listOfOptions = document.getElementById("selectExit").options;
                hour = listOfOptions[selectedIndex].text;
            }

            // AJAX request
            req = ajaxRequest();
            req.onreadystatechange = endExit;
            req.open("POST", "attendanceBackEnd.php?" + "ssn=" + ssn + "&hour=" + hour + "&date=" + day + "&event=exit", true);
            req.send();
        }

        function endEntrance() {

            if (req.readyState == 4 && (req.status == 0 || req.status == 200)) {
                if (req.responseText === "false") {
                    // Something went wrong...
                    window.alert("Oh no! Something went wrong...");
                } else {
                    // Everything is alright --> Change the text inside the button 
                    if (req.responseText === "0") {
                        document.getElementById(buttonID).innerHTML = "Entrance";

                        // Check if the "exit button" has been already changed (from "Exit" to "Hour: X")  
                        var checkID = buttonID.replace("entranceButton", "");
                        var exitButton = document.getElementById("exitButton" + checkID).innerHTML.trim();
                        if (exitButton === "Exit") {
                            // The exit button is still the same (the student has just entered the class, so he/she is now present)
                            document.getElementById(checkID).checked = false;
                        }
                    } else {
                        document.getElementById(buttonID).innerHTML = "Hour: " + req.responseText;
                        var checkID = buttonID.replace("entranceButton", "");

                        // Check if the "exit button" has been already changed (from "Exit" to "Hour: X")
                        var exitButton = document.getElementById("exitButton" + checkID).innerHTML.trim();
                        if (exitButton === "Exit") {
                            // The exit button is still the same (the student has just entered the class, so he/she is now present)
                            document.getElementById(checkID).checked = false;
                        }
                    }
                }
            }

            // Dismiss manually the modal window
            $('#myEntrance').modal('hide');
        }

        function endExit() {

            if (req.readyState == 4 && (req.status == 0 || req.status == 200)) {
                if (req.responseText == "false") {
                    // Something went wrong...
                    window.alert("Oh no! Something went wrong...");
                } else {
                    // Everything is alright --> Change the text inside the button and change the state of the switch
                    if (req.responseText === "0") {
                        document.getElementById(buttonID).innerHTML = "Exit";
                        var checkID = buttonID.replace("exitButton", "");
                        document.getElementById(checkID).checked = false; 
                    } else {
                        document.getElementById(buttonID).innerHTML = "Hour: " + req.responseText;
                        var checkID = buttonID.replace("exitButton", "");
                        document.getElementById(checkID).checked = true;
                    }


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
            $result = $teacher->checkAbsenceEarlyExitLateEntrance($fields[2], $day);
            //$result = array($date, $codFisc, $absence, $lateEntry, $earlyExit);

            if ($result[2] == 1) {
                // assente
                echo "checked='true'";
            }

            echo '> <span class="slider round"></span> </label> </td>';
            echo <<<_LATEENTRANCE
                <td style="vertical-align: middle;">
_LATEENTRANCE;
            if ($result[3] == 0) {
                echo <<<_ENTRY
                <button type="button" id="entranceButton$i" class="btn btn-primary" data-toggle="modal" data-target="#myEntrance" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsENTRANCE(this)">
                Entrance
                </button></td>
_ENTRY;
            } else {
                echo <<<_ENTRY
                <button type="button" id="entranceButton$i" class="btn btn-primary" data-toggle="modal" data-target="#myEntrance" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsENTRANCE(this)">
                Hour: $result[3]
                </button>
_ENTRY;
            }

            echo <<<_EARLYEXIT
                <td style="vertical-align: middle;">
_EARLYEXIT;
            if ($result[4] == 0) {
                echo <<<_EXIT
                <button type="button" id="exitButton$i" class="btn btn-primary" data-toggle="modal" data-target="#myExit" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsEXIT(this)">
                Exit
                </button></td>
_EXIT;
            } else {
                echo <<<_EXIT
                <button type="button" id="exitButton$i" class="btn btn-primary" data-toggle="modal" data-target="#myExit" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsEXIT(this)">
                Hour: $result[4]
                </button></td>
_EXIT;
            }
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
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger col-xs-3 col-md-offset-3" onclick="recordEntrance(this)">Remove</button>
                            <button type="button" class="btn btn-primary col-xs-3" style="margin-top: 0px" onclick="recordEntrance(this)">Save changes</button>
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
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer" >
                        <button type="button" class="btn btn-danger col-xs-3 col-md-offset-3" onclick="recordExit(this)">Remove</button>
                        <button type="button" class="btn btn-primary col-xs-3" style="margin-top: 0px" onclick="recordExit(this)">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
_MODALEXIT;
    }
} else {

    // The class hasn't been chosen yet, so the list of classes must be shown
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