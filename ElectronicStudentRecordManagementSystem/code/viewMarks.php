<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");
require_once("db.php");

$childName = $_SESSION['childName'];
$childSurname = $_SESSION['childSurname'];

$db = new dbParent();

$marks = $db->viewChildMarks($_SESSION['child']);

$subjects = $db->getSubjectTaughtInClass($_SESSION['class']);

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
        $hour = $row[2];
        $initialMark = $row[3];
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
        $preprocessed_data[$subject][$date . " " . $hour] = $initialMark;
        $count++;
    }
    
    // compute the last mean
    if($prev!="" && $count > 0){
        $preprocessed_data[$prev]['mean'] = $preprocessed_data[$prev]['mean']/$count;
    }

    /**
     * Now $preprocessed_data should be a map containing:
     * Subject,mean => mean
     * Subject,date hour => Mark
     */

}

echo <<<_STARTTABLE
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
_STARTTABLE;

// print the contents

foreach ($preprocessed_data as $subject => $marks) {

    $mean = round($preprocessed_data[$subject]['mean'],2);          

    if($mean==0){
    // no marks for that subject
        $mean = "N.C."; 
        $modifier="";
    } elseif($mean<6){
    // print the row with a different color in case of mark lower than 6
        if($mean < 5 ) $modifier = "danger visibleRowMarks";
        else $modifier = "warning visibleRowMarks";
    } else {
        $modifier = "success visibleRowMarks";
    }
    
    echo <<<_VISIBLEROW
        <tr data-toggle='collapse' data-target=".$subject" class="accordion-toggle $modifier">
            <td class="col-md-3">$subject</td>
            <td class="col-md-3"></td>
            <td class="col-md-3"></td>
            <td class="col-md-3">$mean</td>
        </tr>
_VISIBLEROW;

    if($mean=="N.C.") $subject="";

    echo <<<_HIDDENLEGEND
        <tr>
            <td></td>
            <td class="hiddenRow marks"><div class="accordian-body collapse $subject "> <strong> Date </strong> </div> </td>
            <td class="hiddenRow marks"><div class="accordian-body collapse $subject "> <strong> Specific marks </strong> </div> </td>
            <td></td>
        </tr>
_HIDDENLEGEND;
    

    foreach($marks as $dateHour => $mark){
        
        if($dateHour=='mean') continue;

        echo <<<_HIDDENROWS
        <tr>
            <td class="hiddenRow marks"> <div class="accordian-body collapse $subject"></div> </td>
            <td class="hiddenRow marks"><div class="accordian-body collapse $subject"> $dateHour </div> </td>
            <td class="hiddenRow marks"><div class="accordian-body collapse $subject"> $mark </div> </td>
            <td class="hiddenRow marks"> <div class="accordian-body collapse $subject"></div> </td>
        </tr>
_HIDDENROWS;

    }
}

echo <<<_ENDTABLE
    </tbody>
</table>
_ENDTABLE;

require_once("defaultFooter.php");

?>
