<?php
require_once("basicChecks.php");

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "parent") {
    $loggedin = true;
}
if (!$loggedin) {
    header("Location: login.php");
    exit;
} else {

    if (!isset($_SESSION['childName'])) {
        header("Location: chooseChild.php");
        exit;
    }
    require_once "loggedParentNavbar.php";
}
require_once("db.php");

$db = new dbParent();

$finalGrades = $db->viewChildFinalGrades($_SESSION['child']); 
// finalGrades is an array that contains subject,finalGrade

$childName = $_SESSION['childName'];
$childSurname = $_SESSION['childSurname']; 

?>

<h1 class="display-1 text-center"> <?php echo "$childName $childSurname"; ?>'s final grades </h1>
<br>
<table class="table table-striped" id="studentTable" style="border-collapse:collapse;">

<?php
	if ($finalGrades) {
?>	
	<thead>
		<tr>
			<th class="text-center"> Subject </th>
			<th class="text-center"> Final grade </th>
		</tr>
	</thead>
	<tbody>
<?php
		foreach($finalGrades as $row) {
			$args = explode(",", $row);
			$subject = $args[0];
			$grade = $args[1];
		
			echo "<tr class='text-center'><td>$subject</td><td>$grade</td></tr>";
		}
	}
?>
	</tbody>
</table>

<?php
	require_once("defaultFooter.php");
?>