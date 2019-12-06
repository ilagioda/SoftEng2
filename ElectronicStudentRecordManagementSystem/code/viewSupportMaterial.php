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

$subjects = $db->getSubjectTaughtInClass($_SESSION['class']);

echo <<<_STARTTABLE
<h1 class="display-1 text-center"> SUPPORT MATERIALS </h1>
<table class="table table-condensed" style="border-collapse:collapse;">
<thead>
    <tr>
        <th style="font-size:3vh;">Subject</th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
</thead>
<tbody>
_STARTTABLE;

// print the contents

foreach($subjects as $subject) {     
    $subjectID = str_replace(' ', '_', $subject);

    echo <<<_ROW
        <tr data-toggle='collapse' data-target=".$subjectID" class="accordion-toggle visibleRowMarks">
            <td class="col-md-3" style="font-size:2.5vh;">$subject</td>
            <td class="col-md-3"></td>
            <td class="col-md-3"></td>
            <td class="col-md-3"></td>
        </tr>
_ROW;

    $materials = $db->getMaterials($_SESSION['class'], "$subject");
    if($materials->num_rows!=0){

        echo <<<_HIDDENLEGEND
        <tr>
            <td class="hiddenRow legend"><div class="accordian-body collapse $subjectID text-right"> <strong>TIMESTAMP</strong> </div></td>
            <td class="hiddenRow legend"> </td>
            <td class="hiddenRow legend"><div class="accordian-body collapse $subjectID text-right"> <strong> TITLE </strong> </div> </td>
            <td class="hiddenRow legend"><div class="accordian-body collapse $subjectID text-right"> <strong>DIMENSION (kB)</strong> </div></td>
        </tr>
        _HIDDENLEGEND;

        foreach($materials as $material){
            $dimension = round($material["Dimension"]/1000, 0);
            echo <<<_HIDDENROWS
            <tr>
                <td class="hiddenRow marks" > <div class="accordian-body collapse $subjectID text-right">$material[Timestamp]</div> </td>
                <td class="hiddenRow marks" ><div class="accordian-body collapse $subjectID text-right"></div> </td>
                <td class="hiddenRow marks" ><div class="accordian-body collapse $subjectID text-right"> <a href="$material[Filename]"> $material[Title] </a> </div> </td>
                <td class="hiddenRow marks" > <div class="accordian-body collapse $subjectID text-right">$dimension</div> </td>
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


<!--    

  -->
