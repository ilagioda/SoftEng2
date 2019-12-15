<?php

require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
    $loggedin = true;
}
if (!$loggedin) {
    header("Location: login.php");
} else {
    require_once "loggedTeacherNavbar.php";
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

    if (month > 8) {
        // first semester
        semester = 1;
        endingYear = year + 1;
        startingYear = year;
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
        $.post("provideParentMeetingSlotsBE.php", ({
            'year': year,
            'month': month,
            'codFisc': fiscalCode
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
        $.post("provideParentMeetingSlotsBE.php", ({
            'day': selecteddate,
            'codFisc': fiscalCode
        }), function(text) {
            if (text === "") {
                // Error
                window.alert("Oh no! Something went wrong...");
            } else {
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
                    var color = "white";
                    if (freeOrNotFree === "lesson") {
                        color = "#bfbfbf";
                    } else if (freeOrNotFree === "selected") {
                        color = "#b3ffcc";
                    }

                    // str += "<tr><td style='background-color:"+color+"'>"+hours[i]+"</td></tr>";
                    var slotNb = i + 1;
                    if (freeOrNotFree !== "lesson") {
                        str += "<tr><td style='background-color:" + color + "; cursor: pointer;' id='" + selecteddate + "_" + slotNb + "' onclick='provideSlotParentMeetings(this)'>" + hours[i] + "</td></tr>";
                    } else {
                        str += "<tr><td style='background-color:" + color + "' id='" + selecteddate + "_" + slotNb + "'>" + hours[i] + "</td></tr>";
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

        $.post("provideParentMeetingSlotsBE.php", ({
            'codFisc': fiscalCode,
            'day': day,
            'slotNb': slotNb
        }), function(text) {
            if (text === "error") {
                // Error
                window.alert("Oh no! Something went wrong...");
            } else {
                
                element.style.backgroundColor=text; // element should have the right color

                let rootID = elementID.split("_")[0];
                let calendarElement = document.getElementById(rootID);

                if (calendarElement == undefined) return; // the user has already changed window

                if (text == "#b3ffcc") {

                    if (calendarElement.style.backgroundColor.includes("rgb(179")) return; // the element of the table is already ok

                    element.style.backgroundColor = text;
                    calendarElement.style.backgroundColor = "#b3ffcc";

                    let userIcon = document.createElement("div");
                    userIcon.classList.add("glyphicon");
                    userIcon.classList.add("glyphicon-user");

                    let container = document.createElement("div");
                    container.classList.add("calendar-event");
                    container.classList.add("text-center");

                    container.appendChild(userIcon);
                    calendarElement.appendChild(container);

                } else {

                    for (let i = 1; i <= 6; i++) {
                        // check if there are other slots in the same day

                        let row = document.getElementById(rootID + "_" + i);
                        let color = row.style.backgroundColor;

                        if (row == undefined || color.includes("rgb(179")) {
                            // row undefined => the user has already changed the window
                            // or there is another element with background color different from white => another appointment
                            return;
                        }
                    }

                    // no other appointments => remove the color from the day
                    calendarElement.style.backgroundColor = "white";
                    calendarElement.lastChild.remove()
                }
            }
        })
    }

    $(document).ready(updateCalendar);
</script>

<?php


echo <<<_TITLE

    <div class="text-center">
        <h1>PARENT MEETINGS TIME SLOTS</h1><br>
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

echo <<<_storeCodFisc

<script>
var fiscalCode="$_SESSION[user]";
</script>

_storeCodFisc;

echo <<< _COSE

<h2 id="date" class="text-center"></h2>
<table id="daySlots" class="table table-bordered text-center">
</table>

_COSE;

require_once("defaultFooter.php");

?>