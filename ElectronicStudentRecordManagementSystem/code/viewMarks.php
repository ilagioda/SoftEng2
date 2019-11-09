<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");

/* FIXME remove the next lines when login is implemented */

$_SESSION['user'] = 'wlt@gmail.it';
$_SESSION['role'] = 'parent';

require_once("db.php");

$_SESSION['db'] = new dbParent();
$_SESSION['child'] = "FRCWTR";
$_SESSION['childName'] = "Walter";
$_SESSION['childSurname'] = "Forcignanò";
$_SESSION['class'] = '1A';

/* End lines to be changed*/

$childName = $_SESSION['childName'];
$childSurname = $_SESSION['childSurname'];

$marks = $_SESSION['db']->viewChildMarks($_SESSION['child']);

$subjects = $_SESSION['db']->getSubjectTaughtInClass($_SESSION['class']);

$preprocessed_data = array();

foreach ($subjects as $subject) {
    $preprocessed_data[$subject]['mean']=0;
}

if($marks!=""){

    /**
     * There are marks for that student
     * Format: Subject,Date,Mark;Subject,Date...Mark;
     */

    $rows = explode(";",$marks);
    $count =0;
    $prev = "";

    foreach ($rows as $row) {

        // divide per element
        $row = explode(",",$row);

        $subject = $row[0];
        $date = $row[1];
        $initialMark = $row[2];
        $mark = convertMark($initialMark);

        if($preprocessed_data[$subject]['mean']==0) {
            // first iteration for that subject

            if($prev!=="" && $count > 0){
                $preprocessed_data[$prev]['mean'] = $preprocessed_data[$prev]['mean']/$count;
            }

            $preprocessed_data[$subject]['mean']=0;
            $count = 0;
            $prev = $subject;

        }

        $preprocessed_data[$subject]['mean'] +=$mark;
        $preprocessed_data[$subject][$date] = $initialMark;
        $count++;
    }
    
    // compute the last mean
    if($prev!="" && $count > 0){
        $preprocessed_data[$prev]['mean'] = $preprocessed_data[$prev]['mean']/$count;
    }

    /**
     * Now $preprocessed_data should be a map containing:
     * Subject => mean
     */
}

    echo <<< STARTTABLE

    <h1 class="display-1 text-center"> $childName $childSurname's marks </h1>

    <table class="table table-condensed" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>Subject</th>
            <th></th>
            <th></th>
            <th>Mean</th>
        </tr>
    </thead>
    <tbody>

    STARTTABLE;
    // print the contents

    foreach ($preprocessed_data as $subject => $marks) {

        $mean = round($preprocessed_data[$subject]['mean'],2);          

        $modifier="";

        if($mean==0)
        // no marks for that subject
            $mean = "N.C."; 
        elseif($mean<6){
        // print the row with a different color in case of mark lower than 6
            if($mean < 5 ) $modifier = "danger";
            else $modifier = "warning";
        } else {
            $modifier = "success";
        }
        
        echo <<< VISIBLEROW
            <tr data-toggle='collapse' data-target=".$subject" class="accordion-toggle $modifier">
                <td class="col-md-3">$subject</td>
                <td class="col-md-3"></td>
                <td class="col-md-3"></td>
                <td class="col-md-3">$mean</td>
            </tr>
        VISIBLEROW;

        if($mean=="N.C.") $subject="";

        echo <<< HIDDENLEGEND

            <tr>
                <td></td>
                <td class="hiddenRow marks"><div class="accordian-body collapse $subject "> <strong> Date </strong> </div> </td>
                <td class="hiddenRow marks"><div class="accordian-body collapse $subject "> <strong> Specific marks </strong> </div> </td>
                <td></td>
            </tr>
        HIDDENLEGEND;
        

        foreach($marks as $date => $mark){
        
            if($date=='mean') continue;

            echo <<< HIDDENROWS
            <tr>
                <td class="hiddenRow marks"> <div class="accordian-body collapse $subject"></div> </td>
                <td class="hiddenRow marks"><div class="accordian-body collapse $subject"> $date </div> </td>
                <td class="hiddenRow marks"><div class="accordian-body collapse $subject"> $mark </div> </td>
                <td class="hiddenRow marks"> <div class="accordian-body collapse $subject"></div> </td>
            </tr>
            HIDDENROWS;
        }
    }
    
    echo <<< ENDTABLE

        </tbody>
    </table>
    ENDTABLE;

require_once("defaultFooter.php");

?>
