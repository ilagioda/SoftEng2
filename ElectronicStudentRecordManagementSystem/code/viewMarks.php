<?php
require_once("basicChecks.php");

if (!(isset($_SESSION['user']) && $_SESSION['role'] == "parent")) {
    // not logged in
    header("Location: login.php");
    exit;
} 

if(!isset($_SESSION['childName'])){
    header("Location: chooseChild.php");
    exit;
}


require_once("loggedParentNavbar.php");
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
     * Subject,mean => mean
     * Subject,date => Mark
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
        <th>Average grade</th>
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
        // average > 6 
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

    if($mean!="N.C."){

        // print the hidden rows only if the student has at least one mark

        echo <<<_HIDDENLEGEND
            <tr>
                <td class="hiddenRow legend"></td>
                <td class="hiddenRow legend"><div class="accordian-body collapse $subject "> <strong> Date </strong> </div> </td>
                <td class="hiddenRow legend"><div class="accordian-body collapse $subject "> <strong> Specific marks </strong> </div> </td>
                <td class="hiddenRow legend"></td>
            </tr>
_HIDDENLEGEND;
    
        /**
         * Unico modo per sovrascrivere style di bootstrap per le tabelle
         * Introduco border 0 sulle righe chiuse, tranne che nella prima 
         * (per evitare che si veda un pezzo mancante nella tabella)
         * */

        foreach($marks as $date => $mark){
            
            if($date=='mean') continue;

            echo <<<_HIDDENROWS
            <tr>
                <td class="hiddenRow marks" > <div class="accordian-body collapse $subject"></div> </td>
                <td class="hiddenRow marks" ><div class="accordian-body collapse $subject "> $date </div> </td>
                <td class="hiddenRow marks" ><div class="accordian-body collapse $subject "> $mark </div> </td>
                <td class="hiddenRow marks" > <div class="accordian-body collapse $subject "></div> </td>
            </tr>
_HIDDENROWS;

        }
    }
}

echo <<<_ENDTABLE
    </tbody>
</table>
_ENDTABLE;

require_once("defaultFooter.php");

?>
