<?php

function destroySession()
{
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 3600 * 24,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();  // destroy session
}

function checkIfLogged()
{
    if (!isset($_SESSION['role']) || !isset($_SESSION['user'])) {
        echo "<h1 class='text-center'> <a href='index.php'> HOME </a></h1>";
        echo "<img src='images/gandalf.jpg' class='img-responsive center-block'>";
        die();
    }
}

function convertMark($rawMark)
{
    //TESTED

    /**
     * Converts a literal mark (7,7+,7-,7/8,7.5) in the correspondent float value
     * 
     * @param rawMark is the literal mark (taken as output from the db)
     * @return float 
     */

    if (!preg_match("/^([0-9]|[1][0]|[0-9]\+|[0-9]\.5|([1-9]|10)\-|0\/1|1\/2|2\/3|3\/4|4\/5|5\/6|6\/7|7\/8|8\/9|9\/10)$/", $rawMark))
        return -1;

    if (strpos($rawMark, "/") == true) {
        // mark is of type 7/8

        // select the last character
        $mark = floatval(explode('/', $rawMark)[1]);
        // remove 0.25
        $mark -= 0.25;
    } elseif (strpos($rawMark, "+")) {
        // mark is of type 7+

        // select the first character
        $mark = floatval(substr($rawMark, 0, 1));

        // add 0.25
        $mark += 0.25;
    } elseif (strpos($rawMark, "-")) {
        // mark is of type 7-

        // select the first character
        $mark = floatval(substr($rawMark, 0, 1));

        // remove 0.25
        $mark -= 0.25;
    } else {
        // mark of type 7 or 7.5
        $mark = floatval($rawMark);
    }
    return $mark;
}

function getCurrentSemester()
{
    /**
     * SQL-ready function
     * @return Array with [0] as starting date and [1] as ending date of the current semester
     */

    /* Select current year */
    $year = intval(date("Y"));
    $month = intval(date("m"));

    if ($month == 1) {
        // first semester, but new year => put it back to make it work
        $endingDate = $year . "-01-31"; // to January
        $year = $year - 1;
        $beginningDate = $year . "-09-01"; // from September

    } elseif ($month > 8) {
        // first semester
        $beginningDate = $year . "-09-01"; // from September
        $year = $year + 1;
        $endingDate = $year . "-01-31"; // to January

    } else {
        // second semester
        $beginningDate = $year . "-02-01"; // from February
        $endingDate = $year . "-06-30"; // to June
    }

    return array($beginningDate, $endingDate);
}

function getCurrentAcademicYear()
{

    /**
     * SQL-ready function
     * @return Array with [0] as starting date and [1] as ending date of the current academic year
     */

    /* Select current year */
    $year = intval(date("Y"));
    $month = intval(date("m"));

    if ($month <= 7) {
        // second semester
        $year = $year - 1;
    }

    $beginningDate = $year . "-08-01";

    $year = $year + 1;
    $endingDate = $year . "-07-31";

    return array($beginningDate, $endingDate);
}

function build_html_calendar($year, $month, $events = null)
{
    /**
     * Returns the calendar's html for the given year and month.
     *
     * @param $year (Integer) The year, e.g. 2015.
     * @param $month (Integer) The month, e.g. 7.
     * @param $events (Array) An array of events where the key is the day's date
     * in the format "Y-m-d", the value is the event => "absent" | "early - hh:mm" | "late - hh:mm"
     * @return (String) The calendar's html.
     */

    // CSS classes
    $css_cal = 'calendar table';
    $css_cal_row = 'calendar-row';
    $css_cal_day_head = 'calendar-day-head';
    $css_cal_day = 'calendar-day';
    $css_cal_day_number = 'day-number';
    $css_cal_day_blank = 'calendar-day-np';
    $css_cal_day_event = 'calendar-day-event';
    $css_cal_event = 'calendar-event';

    // CSS class to make the date pressable
    $pressableClass = "pointer";

    // Table headings
    $headings = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];

    // Start: draw table
    $calendar =
        "<table cellpadding='0' cellspacing='0' class='{$css_cal}' id='calendar'>" .
        "<tr class='{$css_cal_row}'>" .
        "<td class='{$css_cal_day_head}'>" .
        implode("</td><td class='{$css_cal_day_head}'>", $headings) .
        "</td>" .
        "</tr>";

    // Days and weeks
    $running_day = date('N', mktime(0, 0, 0, $month, 1, $year));
    $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));

    // Row for week one
    $calendar .= "<tr class='{$css_cal_row}'>";

    // Print "blank" days until the first of the current week
    for ($x = 1; $x < $running_day; $x++) {
        $calendar .= "<td class='{$css_cal_day_blank}'> </td>";
    }

    // Check if coming from Parent Meeetings
    if (isset($events["1996-07-25"])) {
        $ila = true;
    } else {
        $ila = false;
    }

    // Keep going with days...
    for ($day = 1; $day <= $days_in_month; $day++) {

        // Check if there is an event today
        $cur_date = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));

        // Check if the date is in the past
        $today_date = date("Y-m-d");
        if ($cur_date < $today_date) {
            $past = true;
        } else {
            $past = false;
        }

        // Check if saturday or sunday
        if (date('N', strtotime($cur_date)) >= 6) {
            $satsun = true;
        } else {
            $satsun = false;
        }

        $draw_event = false;
        if (isset($events) && isset($events[$cur_date])) {
            $draw_event = true;
            $teacherMeetings = false;
            $assignment = false;
			$lecture = false;
            $attendance = false; // to store the hour/hours
            $late = false; // to say if the user entered late or exited early

            $event = $events[$cur_date];

            if ($event == "absent") {
                // absent
                $attendance = $event;
                $color = "darkred";
            } else if (strpos($event, 'early') === 0) { //begins with
                // exits early
                // someone came for him => no problem

                $attendance = explode("-", $event)[1];
                $color = "lightblue";
            } else if (strpos($event, 'late') === 0) {
                // enters late
                $hours = explode("-", $event);
                $late = true;
                if (count($hours) == 3) {
                    //entered late and exited early
                    $attendance = $hours[1] . " - " . $hours[2]; // late - early
                    $color = "orange";
                } else {
                    $attendance = $hours[1];
                    $color = "yellow";
                }
            } else if (strpos($event, 'View assignments:') !== false) {
                //assignment
                $assignment = true;
                $color = "orange";
            } else if (strpos($event, 'View lectures:') !== false) {
                //lecture
                $lecture = true;
                $color = "lightblue";
            } else if (strpos($event, 'teacherMeetings') !== false) {
                // Parent meeting timeslots (Teacher's side)
                $teacherMeetings = true;
                $color = "lightgreen";
            } else $color = "";
        } else $color = "";

        //style='background-color:#b3ffcc'

        //Day cell with assignment (clickable)
        if ($draw_event) {
            if ($assignment) {
                $assText = ltrim($event, 'View assignments:');
                $calendar .= "<td class='{$css_cal_day} {$css_cal_day_event} $color $pressableClass' id='$cur_date' onclick=\"showAssignment(this.id, '$assText')\">";
            } else if ($lecture) {
                $topic = ltrim($event, 'View lectures:');
                $calendar .= "<td class='{$css_cal_day} {$css_cal_day_event} $color $pressableClass' id='$cur_date' onclick=\"showLectures(this.id, '$topic')\">";
            } else if ($teacherMeetings == true && !$past) {
                // Day cell with parent meetings time slots (clickable)
                $calendar .= "<td class='{$css_cal_day} {$css_cal_day_event} $color $pressableClass' id='$cur_date' onclick=\"showDaySlots(this.id)\">";
            } else if ($attendance !== false) {
                // Day cell with attendance (clickable)
                if ($attendance == "absent") {
                    $calendar .= "<td class='{$css_cal_day} {$css_cal_day_event} $color' id='$cur_date'>";
                } else {
                    $hours = explode("-", $attendance);
                    if ($late) {
                        // the student is late or both entered late and exited early
                        $assText = 'The student entered at ' . strval($hours[0]) . '° hour';
                        if (count($hours) == 2) {
                            // exited early AND entered late
                            $assText .= " and exited at " . strval($hours[1]) . '°hour';
                        }
                    } else {
                        // the student exited early
                        $assText = 'The student exited at ' . strval($hours[0]) . '°hour';
                    }
                    $calendar .= "<td class='{$css_cal_day} {$css_cal_day_event} $color $pressableClass' id='$cur_date' onclick=\"showAttendance(this.id, '$assText')\">";
                }
            } else {
                // you should not be here
                return false;
            }
        } else if ($ila) {
            // provide parent meetings
            if ($past || $satsun) {
                $color = "gray";
                $calendar .= "<td class='{$css_cal_day} {$css_cal_day_event} $color' id='$cur_date'>";
            } else {
                $calendar .= "<td class='{$css_cal_day} {$css_cal_day_event} $color $pressableClass' id='$cur_date' onclick=\"showDaySlots(this.id)\">";
            }
        } else {
            // Day cell - in case the event is simply absent
            $calendar .= "<td class='{$css_cal_day}'>";
        }


        // Add the day number
        $calendar .= "<div class='{$css_cal_day_number}'>" . $day . "</div>";

        // Insert an event for this day
        if ($draw_event) {
            if ($assignment) {
                $assignmentClass = "assignment";
            } else $assignmentClass = "";

            $calendar .= "<div class='{$css_cal_event} $assignmentClass'></div>";
        }

        // Close day cell
        $calendar .= "</td>";

        // New row
        if ($running_day == 7) {
            $calendar .= "</tr>";
            if (($day + 1) <= $days_in_month) {
                $calendar .= "<tr class='{$css_cal_row}'>";
            }
            $running_day = 1;
        }

        // Increment the running day
        else {
            $running_day++;
        }
    } // for $day

    // Finish the rest of the days in the week
    if ($running_day != 1) {
        for ($x = $running_day; $x <= 7; $x++) {
            $calendar .= "<td class='{$css_cal_day_blank}'> </td>";
        }
    }

    // Final row
    $calendar .= "</tr>";

    // End the table
    $calendar .= '</table>';

    // All done, return result
    return $calendar;
}

function printTimetable($timetableToShow)
{

    // Function that prints a given timetable 

    // Prepare arrays which will be useful when filling the HTML table 
    $hours = array("8:00", "9:00", "10:00", "11:00", "12:00", "13:00");

    for ($i = 1; $i <= 6; $i++) {

        $hour = $hours[$i - 1];
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
}

function navSubjects($subjects) {
	
	echo "<ul id='myTab' class='nav nav-pills' style='justify-content: center; display: flex;'>";
	echo "<li class='text-center active' style='width:20%;'><a href='#$subjects[0]' data-toggle='tab'>$subjects[0]</a></li>";
	foreach($subjects as $subject) {
		if($subject != $subjects[0])
			echo "<li class='text-center' style='width:20%;'><a href='#$subject' data-toggle='tab'>$subject</a></li>";
	}
	echo "</ul><br>";

}
