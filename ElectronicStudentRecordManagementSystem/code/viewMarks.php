<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");

/* FIXME remove the next lines when login is implemented */

$_SESSION['role'] = 'parent';

require_once("db.php");

$_SESSION['db'] = new dbParent();
$_SESSION['child'] = "FRCWTR";

/* End lines to be changed*/

$marks = $_SESSION['db']->viewChildMarks($_SESSION['child']);

echo $marks;

?>

<style>
.hiddenRow {
    padding: 0 !important;
}
</style>

<table class="table table-condensed" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>Subject</th>
            <th>Mean</th>
        </tr>
    </thead>
    <tbody>
        <tr data-toggle="collapse" data-target=".demo1" class="accordion-toggle warning">
            <td class="text-success">Italiano</td>
            <td class="text-danger">6.8</td>
        </tr>
        <tr>
            <td colspan="6" class="hiddenRow"><div class="accordian-body collapse demo1"> Data1 - Voto </div> </td>
        </tr>
        <tr>
            <td colspan="6" class="hiddenRow"><div class="accordian-body collapse demo1"> Data2 - Voto </div> </td>
        </tr>
    </tbody>
</table>

<?php
require_once("defaultFooter.php")
?>