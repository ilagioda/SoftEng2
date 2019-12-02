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
    require_once "loggedParentNavbar.php";
}
require_once("db.php");


?>

<script>
    let d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth()+1;
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
        $.post("ajaxAssignmentCalendar.php", ({
            'year': year,
            'month': month,
            'codFisc': fiscalCode
        }), function(text) {
            $("#calendar").replaceWith(text);
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
         * To be used on the < button
         * When the button is clicked, performs an ajax request and update the calendar
         */

        if (semester == 1) {
            // first semester
            if(month==1){
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
            } else month+=1;
        }



        updateCalendar();
    }

    function showAssignment($date, $text){
        var old_date = document.getElementById("date").innerHTML;
        document.getElementById("assignmentList").innerHTML="";

        if(old_date.includes($date)){
            // clear date
            document.getElementById("date").innerHTML="";
            return;  
        } 
        
        // Update date
        document.getElementById("date").innerHTML=$date;
        // insert new cards
        var assignments = document.getElementById("assignmentList");
        var str="";
        console.log($text);
        let rows = $text.split("~");

        for(let i=0;i<rows.length;i++){
            row = rows[i].split(":");
            let subject = row[0];
            let assignment = row[2];

            str+='<div class="card-index"><div class="card-header-index text-left"><strong>';
            str+=subject;
            str+='</strong></div><div class="card-body-index"><p>';
            str+=assignment;
            str+='</p></div></div>';
            
        }
        assignments.innerHTML = str;
    }


    $(document).ready(updateCalendar);
</script>

<?php


$childName = $_SESSION['childName'];
$childSurname = $_SESSION['childSurname']; 

$db = new dbParent();


$assignments = $db->viewChildAssignments($_SESSION['child']);

$subjects = $db->getSubjectTaughtInClass($_SESSION['class']);



echo <<<_TITLE

    <div class="text-center">
        <h1>$_SESSION[childName] $_SESSION[childSurname]'s assignments</h1>
        <br>
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

var fiscalCode="$_SESSION[child]";

</script>

_storeCodFisc;

?>


<h2 id="date" class="text-center"></h2>
<div id="assignmentList">
    
</div>

<?php


require_once("defaultFooter.php");


?>
