<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");

/* FIXME remove the next lines when login is implemented */

$_SESSION['user'] = 'wlt@gmail.it';
$_SESSION['role'] = 'parent';

require_once("db.php");

$_SESSION['db'] = new dbParent();
$_SESSION['child'] = "FRCWTR";

/* End lines to be changed*/

$marks = $_SESSION['db']->viewChildMarks($_SESSION['child']);

$preprocessed_data = array();

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

        if(!isset($preprocessed_data[$subject]['mean'])) {
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

        if($mean<6) 
            // print the row with a different color in case of warning
            $warning = "warning";
        else $warning = "";
        
        echo <<< VISIBLEROW
            <tr data-toggle='collapse' data-target=".$subject" class="accordion-toggle $warning">
                <td>$subject</td>
                <td></td>
                <td></td>
                <td>$mean</td>
            </tr>
        VISIBLEROW;

        
        echo <<< HIDDENLEGEND

            <tr>
                <td></td>
                <td class="hiddenRow marks"><div class="accordian-body collapse $subject"> <strong> Date </strong> </div> </td>
                <td class="hiddenRow marks"><div class="accordian-body collapse $subject"> <strong> Specific marks </strong> </div> </td>
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
