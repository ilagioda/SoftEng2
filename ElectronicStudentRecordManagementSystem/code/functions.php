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

    /**
     * Converts a literal mark (7,7+,7-,7/8,7.5) in the correspondent float value
     * 
     * @param rawMark is the literal mark (taken as output from the db)
     * @return float 
     */

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

    // Keep going with days...
    for ($day = 1; $day <= $days_in_month; $day++) {

        // Check if there is an event today
        $cur_date = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
        $draw_event = false;
        if (isset($events) && isset($events[$cur_date])) {
            $draw_event = true;
            $event = $events[$cur_date];
            if ($event == "absent") {
                // absent
                $color = "style='background-color:orange'";
            } else if (strpos($event, 'early') !== false) {
                // exits early
                // someone came for him => no problem
                $events[$cur_date] = explode("-", $event)[1];
                $color = "style='background-color:lightblue'";
            } else if (strpos($event, 'late') !== false) {
                // enters late
                $events[$cur_date] = explode("-", $event)[1];
                $color = "style='background-color:yellow'";
            } else $color = "";
        } else $color = "";

        // Day cell
        $calendar .= $draw_event ?
            "<td class='{$css_cal_day} {$css_cal_day_event}' $color>" : "<td class='{$css_cal_day}'>";

        // Add the day number
        $calendar .= "<div class='{$css_cal_day_number}'>" . $day . "</div>";

        // Insert an event for this day
        if ($draw_event) {
            $calendar .=
                "<div class='{$css_cal_event} text-center'>" .
                $events[$cur_date] .
                "</div>";
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
