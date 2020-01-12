<?php

require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "parent") {
    $loggedin = true;
}
if (!$loggedin) {
    header("Location: login.php");
} else {
    if(!isset($_SESSION['childName'])){
        header("Location: chooseChild.php");
        exit;
    }
        require_once "loggedParentNavbar.php";
}

require_once("db.php");
$db = new dbParent();

?>

<script>
    let d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var startingYear;
    var endingYear;
    var semester;

    var monthLabel = {
        1: 'January',
        2: 'February',
        3: 'March',
        4: 'April',
        5: 'May',
        6: 'June',
        9: 'September',
        10: 'October',
        11: 'November',
        12: 'December',
    }

    if (month == 7 || month == 8) {
        // display the previous data
        month = 6;
    }

    if (month > 8 || month==1) {
        // first semester
        semester = 1;
    } else {
        // second semester
        semester = 2;
    }

    function updateLabels() {

        /**
         * Called when the calendar is updated => updates labels and disable/enable the buttons when needed
         */

        document.getElementById('Date').innerHTML = monthLabel[month] + " " + year;

        if (semester == 1) {
            // first semester
            if (month == 1) {
                document.getElementById('button-right').disabled = true;
            } else if (month == 9) {
                document.getElementById('button-left').disabled = true;
            } else {
                document.getElementById('button-left').disabled = false;
                document.getElementById('button-right').disabled = false;
            }

        } else {
            // second semester
            if (month == 6) {
                // you can not display attendance for july or august
                document.getElementById('button-right').disabled = true;
            } else if (month == 2) {
                // you can not display attendance for july or august
                document.getElementById('button-left').disabled = true;
            } else {
                document.getElementById('button-left').disabled = false;
                document.getElementById('button-right').disabled = false;
            }
        }

    }

    function updateCalendar() {
        $.post("bookParentMeetingBE.php", ({
            'year': year,
            'month': month,
            'codFiscTEACHER': teacherSSN
        }), function(text) {
            $("#calendar").replaceWith(text);
            document.getElementById("date").innerHTML = "";
            document.getElementById("daySlots").innerHTML = "";
            updateLabels();

        })
    }

    function monthBefore() {
        /**
         * To be used on the < button
         * When the button is clicked, performs an ajax request and update the calendar
         */

        if (semester == 1) {
            // first semester
            if (month == 9) {
                // you want to see august lectures => no reason
                return;
            }

            if (month == 1) {
                // From january you should go to december
                month = 12;
                year = year - 1;
            } else month -= 1;

        } else {

            // second semester
            if (month == 2) {
                // can't go back
                return;
            } else month -= 1;
        }

        updateCalendar();
    }

    function monthAfter() {
        /**
         * To be used on the > button
         * When the button is clicked, performs an ajax request and update the calendar
         */

        if (semester == 1) {
            // first semester
            if (month == 1) {
                // first semester is ended 
                return;
            }

            if (month == 12) {
                // From january you should go to december
                month = 1;
                year += 1;
            } else month += 1;

        } else {
            // second semester
            if (month == 6) {
                // you want to see July lectures => no reason
                return;
            } else month += 1;
        }

        updateCalendar();
    }

    function showDaySlots(selecteddate) {           
        var old_date = document.getElementById("date").innerHTML;
        document.getElementById("daySlots").innerHTML = "";

        if (old_date.includes(selecteddate)) {
            // Clear date
            document.getElementById("date").innerHTML = "";
            return;
        }

        // Update date
        document.getElementById("date").innerHTML = selecteddate;

        // Ajax request to retrieve the time slots availability for the selected date
        $.post("bookParentMeetingBE.php", ({
            'day': selecteddate,
            'mailPARENT': parentMail,
            'codFiscTEACHER': teacherSSN
        }), function(text) {
            if (text === "") {
                // Error
                window.alert("Oh no! Something went wrong...");
            } else {       

                // "text" contains the slots availability in the form: "1_1_free,1_2_free,1_3_selected,1_4_selected,2_1_full..."

                // Split the string and prepare a matrix (rows: slotNbs, columns: quarters)
                var availability = [];
                for(var i=0; i<6; i++) {
                    availability[i] = new Array(4);
                }
                var arr = text.split(",");   // After this split, we have an array (arr) in the form ["1_1_free" , "1_2_free",...]
                for(let i = 0; i<24; i++){
                    var tmp = arr[i].split("_");    // After this split, we have an array (tmp) in the form ["1", "1", "free"]
                    var slotNb = parseInt(tmp[0]);
                    var quarter = parseInt(tmp[1]);
                    var str = tmp[2];
                    availability[slotNb-1][quarter-1] = str;
                }

                // Prepare a matrix with the quarter hours to be displayed
                var quarters = [];
                for(var i=0; i<6; i++) {
                    quarters[i] = new Array(4);
                }
                quarters[0] = ["8:00-8:15", "8:15-8:30", "8:30-8:45", "8:45-9:00"];
                quarters[1] = ["9:00-9:15", "9:15-9:30", "9:30-9:45", "9:45-10:00"];
                quarters[2] = ["10:00-10:15", "10:15-10:30", "10:30-10:45", "10:45-11:00"];
                quarters[3] = ["11:00-11:15", "11:15-11:30", "11:30-11:45", "11:45-12:00"];
                quarters[4] = ["12:00-12:15", "12:15-12:30", "12:30-12:45", "12:45-13:00"];
                quarters[5] = ["13:00-13:15", "13:15-13:30", "13:30-13:45", "13:45-14:00"];

                // Show the list of time slots 8:00-9:00, 9:00-10:00, 10:00-11:00, 11:00-12:00, 12:00-13:00, 13:00-14:00
                var slots = document.getElementById("daySlots");
                var str = "";
                var hours = ["8:00-9:00", "9:00-10:00", "10:00-11:00", "11:00-12:00", "12:00-13:00", "13:00-14:00"];

                var cont = 0;
                for(let i = 0; i < 6; i++) {
                    str += "<tr>";
                    for(let j = -1; j < 4; j++){

                        if(j == -1){
                            str += "<td><strong>" + hours[cont] + "</strong></td>";
                            cont++;
                            continue;
                        }

                        var freeOrNotFree = availability[i][j];
                        var color = "";
                        if (freeOrNotFree === "free") {
                            color = "lightgreen"; 
                        } else if (freeOrNotFree === "selected") {
                            color = "yellow";
                        } else if (freeOrNotFree === "full") {
                            color = "lightred";
                        }

                        var s = i + 1;
                        var q = j + 1;
                        if (freeOrNotFree !== "no" && freeOrNotFree !== "full") {
                            str += "<td class='pointer " + color + "' id='" + selecteddate + "_" + s + "_" + q + "' onclick='provideSlotParentMeetings(this)'>" + quarters[i][j] + "</td>";
                        } else {
                            str += "<td class='" + color + "' id='" + selecteddate + "_" + s + "_" + q + "'>" + quarters[i][j] + "</td>";
                        }
                    }
                    str += "</tr>";
                }

                slots.innerHTML = str;
            }
        })
    }

    function provideSlotParentMeetings(element) {
        elementID = element.id;      // The id is in the form "YYYY-MM-DD_slotNb_quarterNb"

        // Split the id and retrieve the separate values
        var arr = elementID.split("_");
        var day = arr[0];
        var slotNb = arr[1];
        var quarterNb = arr[2];

        // Check if called by a lightred slot
        if(element.classList.contains("lightred"))
            return;

        $.post("bookParentMeetingBE.php", ({
            'mailPARENT': parentMail,
            'codFiscTEACHER': teacherSSN,
            'day': day,
            'slotNb': slotNb,
            'quarterNb': quarterNb
        }), function(text) {                
            if (text === "error") {
                // Error
                window.alert("Oh no! Something went wrong...");
            } else {
                
                let alreadyProvidedColor = "lightgreen";
                let rootID = elementID.split("_")[0];
                let calendarElement = document.getElementById(rootID);

                if (calendarElement == undefined) return; // the user has already changed window

                // Change the color
                if(element.classList.contains("lightgreen")){
                    element.classList.remove("lightgreen");
                } else if (element.classList.contains("yellow")){
                    element.classList.remove("yellow");
                }
                element.classList.add(text);  
             
                // Remove the cursor pointer if the slot is turning to lightred
                if(element.classList.contains("lightred")){
                    element.classList.remove("pointer");
                }
                
                if (text == alreadyProvidedColor) {
                    if (calendarElement.classList.contains(alreadyProvidedColor)) return; // the element of the table is already ok
                    calendarElement.classList.add(text);
                } else {

                    for (let i = 1; i <= 6; i++) {
                        for (let j = 1; j <= 4; j++) {
                            // check if there are other slots in the same day
                            let row = document.getElementById(rootID + "_" + i + "_" + j);

                            if (row == undefined || row.classList.contains(alreadyProvidedColor)) {
                                // row undefined => the user has already changed the window
                                // or there is another element with background color different from white => another appointment
                                return;
                            }
                        }   
                    }

                    // no other appointments => remove the color from the day
                    calendarElement.classList.remove(alreadyProvidedColor);
                }
            }
        })
    }

    $(document).ready(updateCalendar);
</script>

<?php

$TEACHER_SSN = "teacher";

if(isset($_REQUEST[$TEACHER_SSN])){

    $teacherSSN = $_REQUEST[$TEACHER_SSN];
    $teacherSSN = htmlspecialchars($teacherSSN);
    $teacherNameSurname = $db->getTeacherNameSurname($teacherSSN);

    echo <<<_TITLE

    <div class="text-center">
        <h1>PARENT MEETINGS with $teacherNameSurname</h1><br>
        <div class="row">
            <div class="col-sm-6 col-md-4 col-md-offset-4">
                <div class="thumbnail">
                <div class="caption">
                    <h3>Need some help?</h3>
                    Here you can see...<br><br>
                    <table class='table table-bordered text-center'>
                    <tr><td></td><td>Slot not available for parent meetings</td></tr>
                    <tr><td class='lightgreen'></td><td>Slot available</td></tr>
                    <tr><td class='lightred'></td><td>Slot occupied by another parent</td></tr>
                    <tr><td class='yellow'></td><td>Slot already selected by you</td></tr>
                    </table>
                    Click on a date and select a slot to book a meeting with the teacher!
                </div>
                </div>
            </div>
        </div>

        <h2>
        <button class=" btn btn-default calendar-command-left" onClick=monthBefore() id='button-left'>
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        </button>
        <span class="label label-primary" id='Date'></span>
        <button class=" btn btn-default calendar-command-right" onClick=monthAfter() id='button-right'>
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        </button>
        </h2>
    </div>

_TITLE;

    echo "<div id='calendar'></div>";

    $cleanedTeacherSSN = htmlspecialchars($_REQUEST[$TEACHER_SSN]);
    echo <<<_storeCodFisc

    <script>
        var parentMail="$_SESSION[user]"; 
        var teacherSSN="$cleanedTeacherSSN";        
        var childClass="$_SESSION[class]";
    </script>

_storeCodFisc;

    echo <<< _COSE

    <h2 id="date" class="text-center"></h2>
    <table id="daySlots" class="table table-bordered text-center">
    </table>

_COSE;

} else {
    echo <<<_ALERTMSG
    <div class="container text-center">
        <div class="alert alert-danger alert-dismissible" role="alert">
            Oh no, something went wrong! <strong>No teacher</strong> has been selected for the parent meetings!
        </div> 
    </div> 
_ALERTMSG;
}

require_once("defaultFooter.php");

?>