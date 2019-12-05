<script type='text/javascript'>

    var req;
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

    function storeTimetable(){

        // Retrieve the selected class
        var chosenClass = document.getElementById("classID").innerHTML.replace("Class ", "");;

        // Retrieve the subjects and prepare the AJAX request
        var dest = "publishTimetableBackend.php?";

        // req.open("POST", "publishTimetableBackend.php?" + "ssn=" + ssn + "&hour=" + hour + "&date=" + day + "&event=entrance", true);
        for(let i=1; i<=6; i++){
            var subMon = document.getElementById("mon_"+i).innerHTML;
    
            if(i==1){
                dest += "mon_"+i+"="+subMon;
            } else {
                dest += "&mon_"+i+"="+subMon;
            }

            var subTue = document.getElementById("tue_"+i).innerHTML;
            dest += "&tue_"+i+"="+subTue;
            var subWed = document.getElementById("wed_"+i).innerHTML;
            dest += "&wed_"+i+"="+subWed;
            var subThu = document.getElementById("thu_"+i).innerHTML;
            dest += "&thu_"+i+"="+subThu;
            var subFri = document.getElementById("fri_"+i).innerHTML;
            dest += "&fri_"+i+"="+subFri;
        }

        // AJAX request
        req = ajaxRequest();
        req.onreadystatechange = endStore;
        req.open("POST", dest);
        req.send();
    }

    function endStore(){
        // ALERT SUCCESS OR FAILURE ----------------------------------------------------------------------------
    }

</script>

<?php

require_once('basicChecks.php');
$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedAdminNavbar.php";
}

require_once('db.php');
$dbAdmin = new dbAdmin();

echo "<div class=text-center style='margin-bottom: 30px;'>";
echo "<h1>PUBLISH TIMETABLE</h1><br>";

if (isset($_REQUEST["class"])) {

    // The admin has selected a class

    // Store in a variable the name of the selected class
    $chosenClass = $_REQUEST["class"];
    echo "<h3 id='classID'><i>Class $chosenClass</i></h3><br>";

    // Create a local variable which will contain the timetable loaded from the CSV file
    $loadedTimetable = array();

    $errore = 0;    
    // Open the correct CSV file for reading 
    // NOTE: the @ before fopen is important because it blocks the "Warning" text on display
    if (($fp = @fopen("./timetables/timetable$chosenClass.CSV", "r")) !== FALSE) {
        
        // Convert each line into the local $data variable
        while (($row = fgetcsv($fp, 1000, ";")) !== FALSE) {

            if($row[0] !== "day"){
                // Read the data from a single line and store it into the $loadedTimetable
                array_push($loadedTimetable, $row);     
            }   
        }

        // Close the file
        fclose($fp);
    } else {
        echo "<i>Oh no! The CSV file containing the timetable for the selected class is missing!</i>";
        $errore = 1;
    }

    if(!$errore){

        echo <<<_OPENTABLE
        <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
        <tr style="font-size: 20px;"><td></td><td><b>Monday</b></td><td><b>Tuesday</b></td><td><b>Wednesday</b></td><td><b>Thursday</b></td><td><b>Friday</b></td></tr>
_OPENTABLE;

        // Create the array that will be used in the HTML table
        $timetableToShow = array();

        // Iterate over the lectures and re-order the data associated with them
        foreach ($loadedTimetable as $lecture){
            // Store the row with the format: $timetableToShow[1]["mon"] = "Math"
            // $lecture[0] = day
            // $lecture[1] = hour
            // $lecture[2] = subject
            $timetableToShow[$lecture[1]][$lecture[0]] = $lecture[2];
        }

        // Prepare arrays which will be useful when filling the HTML table 
        $hours = array("8:00","9:00", "10:00", "11:00", "12:00", "13:00");
        $cont = 0;

        for($i=1; $i<=6; $i++){
            
            $hour = $hours[$i-1];
            $mon = $timetableToShow[$i]["mon"];
            $tue = $timetableToShow[$i]["tue"];
            $wed = $timetableToShow[$i]["wed"];
            $thu = $timetableToShow[$i]["thu"];
            $fri = $timetableToShow[$i]["fri"];

            echo <<<_ROW
            <tr>
            <td style="vertical-align: middle;"><b>$hour<b></td>
            <td style="vertical-align: middle;" id="mon_$i">$mon</td>
            <td style="vertical-align: middle;" id="tue_$i">$tue</td>
            <td style="vertical-align: middle;" id="wed_$i">$wed</td>
            <td style="vertical-align: middle;" id="thu_$i">$thu</td>
            <td style="vertical-align: middle;" id="fri_$i">$fri</td>
            </tr>
_ROW;
        }

        echo <<<_CLOSETABLE
            </table>
            </div>
_CLOSETABLE;

        echo <<<_PUBLISHBUTTON
            <button type="submit" class="btn btn-lg btn-primary" onclick="storeTimetable()">Publish</button>
_PUBLISHBUTTON;
    }
} else {

    // The admin has to select a class

    $classes = $dbAdmin->retrieveAllClasses();     

    echo <<<_LIST
            <ul class="list-group">
_LIST;

    foreach ($classes as $class) {
        echo <<<_CLASSLINK
            <a href="publishTimetable.php?class=$class" class="list-group-item">$class</a>     
_CLASSLINK;
    }

    echo <<<_ENDLIST
        </ul>
_ENDLIST;

}

echo "</div>";

require_once("defaultFooter.php");
?>