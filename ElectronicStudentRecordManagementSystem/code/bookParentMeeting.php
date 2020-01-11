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
            } else {        // TODO ila - da fixare
                // "text" contains the slots availability in the form: "1_lesson,2_free,3_free,4_selected,5_selected,6_lesson"

                // Split the string and prepare an array
                var availability = [];
                var arr = text.split(","); // After this split, we have an array (arr) in the form ["1_lesson" , "2_free", "3_free", "4_selected", "5_selected", "6_lesson"]
                for (let i = 0; i < 6; i++) {
                    var tmp = arr[i].split("_"); // After this split, we have an array (tmp) in the form ["1", "lesson"]
                    availability[parseInt(tmp[0])] = tmp[1];
                }

                // Show the list of time slots 8:00-9:00, 9:00-10:00, 10:00-11:00, 11:00-12:00, 12:00-13:00, 13:00-14:00
                var slots = document.getElementById("daySlots");
                var str = "";

                var hours = ["8:00-9:00", "9:00-10:00", "10:00-11:00", "11:00-12:00", "12:00-13:00", "13:00-14:00"];
                for (let i = 0; i < hours.length; i++) {
                    var freeOrNotFree = availability[i + 1];
                    var color = "";
                    if (freeOrNotFree === "lesson") {
                        color = "gray";
                    } else if (freeOrNotFree === "selected") {
                        color = "lightgreen";
                    }

                    // str += "<tr><td style='background-color:"+color+"'>"+hours[i]+"</td></tr>";
                    var slotNb = i + 1;
                    if (freeOrNotFree !== "lesson") {
                        str += "<tr><td class='" + color + "' id='" + selecteddate + "_" + slotNb + "' onclick='provideSlotParentMeetings(this)'>" + hours[i] + "</td></tr>";
                    } else {
                        str += "<tr><td class='" + color + "' id='" + selecteddate + "_" + slotNb + "'>" + hours[i] + "</td></tr>";
                    }
                }

                slots.innerHTML = str;
            }
        })
    }

    function provideSlotParentMeetings(element) {
        elementID = element.id; // The id is in the form "YYYY-MM-DD_slotNb"

        // Split the id and retrieve the separate values
        var arr = elementID.split("_");
        var day = arr[0];
        var slotNb = arr[1];

        $.post("bookParentMeetingBE.php", ({
            'mnailPARENT': parentMail,
            'codFiscTEACHER': teacherSSN,
            'day': day,
            'slotNb': slotNb
        }), function(text) {
            if (text === "error") {
                // Error
                window.alert("Oh no! Something went wrong...");
            } else {
                
                let alreadyProvidedColor = "lightgreen";
                let rootID = elementID.split("_")[0];
                let calendarElement = document.getElementById(rootID);

                if (calendarElement == undefined) return; // the user has already changed window

                if (text == alreadyProvidedColor) {
                    element.classList.add(text); // element should have the right color

                    if (calendarElement.classList.contains(alreadyProvidedColor)) return; // the element of the table is already ok
                    calendarElement.classList.add(text);

                } else {
                    element.classList.remove(alreadyProvidedColor); // element should have the right color

                    for (let i = 1; i <= 6; i++) {
                        // check if there are other slots in the same day

                        let row = document.getElementById(rootID + "_" + i);

                        if (row == undefined || row.classList.contains(alreadyProvidedColor)) {
                            // row undefined => the user has already changed the window
                            // or there is another element with background color different from white => another appointment
                            return;
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

if(isset($_REQUEST["teacher"])){

    $teacherSSN = $_REQUEST["teacher"];
    $teacherSSN = htmlspecialchars($teacherSSN);
    
    echo <<<_TITLE

    <div class="text-center">
        <h1>PARENT MEETINGS with teacher $teacherSSN</h1><br>
        <div class="row">
            <div class="col-sm-6 col-md-4 col-md-offset-4">
                <div class="thumbnail">
                <div class="caption">
                    <h3>Need some help?</h3>
                    Here you can see...<br><br>
                    <table class='table table-bordered text-center'>
                    <tr><td></td><td>Slot free for parent meetings</td></tr>
                    <tr><td class='lightgreen'></td><td>Slot already selected for parent meetings</td></tr>
                    <tr><td class='gray'></td><td>Lesson</td></tr>
                    </table>
                    Click on a day to select a!
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

    <p id="prova"></p> 

_TITLE;

    echo "<div id='calendar'></div>";

    echo <<<_storeCodFisc

    <script>
    var parentMail="$_SESSION[user]"; 
    var teacherSSN="$_REQUEST[teacher]";        
    var childClass="$_SESSION[class]";
    </script>

_storeCodFisc;

    echo <<< _COSE

    <h2 id="date" class="text-center"></h2>
    <table id="daySlots" class="table table-bordered text-center">
    </table>

_COSE;

} else {
    // TODO ila - da mettere un alert con MESSAGGIO DI ERRORE
    echo "ERRORE";
}

require_once("defaultFooter.php");

?>