<?php
	require_once("basicChecks.php");

	$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {
    $loggedin = true;
}
if (!$loggedin) {
    //require_once("defaultNavbar.php");
    header("Location: login.php");
} else {
    require_once "loggedNavbar.php";
}
    
    /* FIXME remove the next lines when login is implemented */
    
/*     $_SESSION['user'] = 'GDILRI';
    $_SESSION['role'] = 'admin'; */
    
    /* End lines to be changed*/
?>

<script type='text/javascript'>

	function changeVisibility(){
		var state = document.getElementById("row1").style.visibility;
		if(state === "hidden"){
			document.getElementById("row1").style.visibility = "visible";
			document.getElementById("row2").style.visibility = "visible";
			document.getElementById("row3").style.visibility = "visible";
			document.getElementById("row4").style.visibility = "visible";
			document.getElementById("iconParent2").classList.remove("glyphicon");
			document.getElementById("iconParent2").classList.remove("glyphicon-plus");
			document.getElementById("iconParent2").classList.add("glyphicon");
			document.getElementById("iconParent2").classList.add("glyphicon-minus");
			document.getElementById("name2").required = true;
			document.getElementById("surname2").required = true;
			document.getElementById("codfisc2").required = true;
			document.getElementById("email2").required = true;
		}	
		else{
			document.getElementById("row1").style.visibility = "hidden";
			document.getElementById("row2").style.visibility = "hidden";
			document.getElementById("row3").style.visibility = "hidden";
			document.getElementById("row4").style.visibility = "hidden";
			document.getElementById("iconParent2").classList.remove("glyphicon");
			document.getElementById("iconParent2").classList.remove("glyphicon-minus");
			document.getElementById("iconParent2").classList.add("glyphicon");
			document.getElementById("iconParent2").classList.add("glyphicon-plus");
			document.getElementById("name2").required = false;
			document.getElementById("surname2").required = false;
			document.getElementById("codfisc2").required = false;
			document.getElementById("email2").required = false;
		}
	}
</script>

<h1 align="center"> ENROLL A STUDENT </h1>
<div class="container text-center">
	<div class="row">
	<form class="form-horizontal">
		<br><h2>STUDENT INFORMATION</h2><br>
		<div class="form-group">
			<label for="name">Name*: </label>
			<input type="text" class="form-control" name="name" id="name" placeholder="Enter the student's name" autocomplete="off" title="Enter the student's name" required>
		</div>
		<div class="form-group">
			<label for="surname">Surname*: </label>
			<input type="text" class="form-control" name="surname" id="surname" placeholder="Enter the student's surname" autocomplete="off" title="Enter the student's surname" required>
		</div>
		<div class="form-group">
			<label for="codfisc">SSN code*: </label>
			<input type="text" class="form-control" name="codfisc" id="codfisc" placeholder="Enter the student's SSN code" autocomplete="off" title="Enter the student's SSN code" required>
		</div>
		<br><h2>PARENT 1 INFORMATION</h2><br>
		<div class="form-group">
			<label for="name1">Name*: </label>
			<input type="text" class="form-control" name="name1" id="name1" placeholder="Enter the parent 1's name" autocomplete="off" title="Enter the parent 1's name" required>
		</div>
		<div class="form-group">
			<label for="surname1">Surname*: </label>
			<input type="text" class="form-control" name="surname1" id="surname1" placeholder="Enter the parent 1's surname" autocomplete="off" title="Enter the parent 1's surname" required>
		</div>
		<div class="form-group">
			<label for="codfisc1">SSN code*: </label>
			<input type="text" class="form-control" name="codfisc1" id="codfisc1" placeholder="Enter the parent 1's SSN code" autocomplete="off" title="Enter the parent 1's SSN code" required>
		</div>
		<div class="form-group">
			<label for="email1">E-mail*: </label>
			<input type="email" class="form-control" name="email1" id="email1" placeholder="Enter the parent 1's e-mail" autocomplete="off" title="Enter the parent 1's e-mail" required>
		</div>
		<br><h2 id="rowParent2" onclick="changeVisibility()"><span id="iconParent2" class="glyphicon glyphicon-plus" aria-hidden="true">&emsp;&emsp;</span>PARENT 2 INFORMATION</h2><br>
		<div class="form-group" id="row1" style="visibility: hidden;">
			<label for="name2">Name*: </label>
			<input type="text" class="form-control" name="name2" id="name2" placeholder="Enter the parent 2's name" autocomplete="off" title="Enter the parent 2's name" required>
		</div>
		<div class="form-group" id="row2" style="visibility: hidden;">
			<label for="surname2">Surname*: </label>
			<input type="text" class="form-control" name="surname2" id="surname2" placeholder="Enter the parent 2's surname" autocomplete="off" title="Enter the parent 2's surname" required>
		</div>
		<div class="form-group" id="row3" style="visibility: hidden;">
			<label for="codfisc2">SSN code*: </label>
			<input type="text" class="form-control" name="codfisc2" id="codfisc2" placeholder="Enter the parent 2's SSN code" autocomplete="off" title="Enter the parent 2's SSN code" required>
		</div>
		<div class="form-group" id="row4" style="visibility: hidden;">
			<label for="email2">E-mail*: </label>
			<input type="email" class="form-control" name="email2" id="email2" placeholder="Enter the parent 2's e-mail" autocomplete="off" title="Enter the parent 2's e-mail" required>
		</div>
		<br>
		<input type="reset" class="btn btn-default btn-lg" name="enrollCancel" id="enrollCancel" value="Cancel">
		<input type="submit" class="btn btn-primary btn-lg" name="enrollSubmit" id="enrollSubmit" value="Enroll student">
	</form>
	</div>

	<form method="POST" action="enrollstudentPART2.php" id="enrollForm">
	<table class="enrollTable text-center">
		<tr class="info"><td><b>STUDENT INFORMATION</b></td><td></td></tr>
		<tr><td class="col-md-6"><label for="name">Name*: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="name" id="name" placeholder="Enter the student's name" autocomplete="off" title="Enter the student's name" required></td></tr>
		<tr><td class="col-md-6"><label for="surname">Surname*: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="surname" id="surname" placeholder="Enter the student's surname" autocomplete="off" title="Enter the student's surname" required></td></tr>
		<tr><td class="col-md-6"><label for="codfisc">SSN code*: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="codfisc" id="codfisc" placeholder="Enter the student's SSN code" autocomplete="off" title="Enter the student's SSN code" required></td></tr>
		<tr><td class="col-md-6"></td></tr>
		<tr class="info"><td><b>PARENT 1 INFORMATION</b></td><td></td></tr>
		<tr><td class="col-md-6"><label for="name1">Name*: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="name1" id="name1" placeholder="Enter the parent 1's name" autocomplete="off" title="Enter the parent 1's name" required></td></tr>
		<tr><td class="col-md-6"><label for="surname1">Surname*: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="surname1" id="surname1" placeholder="Enter the parent 1's surname" autocomplete="off" title="Enter the parent 1's surname" required></td></tr>
		<tr><td class="col-md-6"><label for="codfisc1">SSN code*: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="codfisc1" id="codfisc1" placeholder="Enter the parent 1's SSN code" autocomplete="off" title="Enter the parent 1's SSN code" required></td></tr>
		<tr><td class="col-md-6"><label for="email1">E-mail*: </label></td><td class="col-md-6"><input type="email" style="width:100%" name="email1" id="email1" placeholder="Enter the parent 1's e-mail" autocomplete="off" title="Enter the parent 1's e-mail" required></td></tr>
		<tr><td class="col-md-6"></td></tr>
		<tr class="info" id="rowParent2" onclick="changeVisibility()"><td class="col-md-6"><span id="iconParent2" class="glyphicon glyphicon-plus" aria-hidden="true">&emsp;&emsp;</span><b>PARENT 2 INFORMATION</b></td><td></td></tr>
		<tr id="row1" style="visibility: hidden;"><td class="col-md-6"><label for="name2">Name: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="name2" id="name2" placeholder="Enter the parent 2's name" autocomplete="off" title="Enter the parent 2's name"></td></tr>
		<tr id="row2" style="visibility: hidden;"><td class="col-md-6"><label for="surname2">Surname: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="surname2" id="surname2" placeholder="Enter the parent 2's surname" autocomplete="off" title="Enter the parent 2's surname"></td></tr>
		<tr id="row3" style="visibility: hidden;"><td class="col-md-6"><label for="codfisc2">SSN code: </label></td><td class="col-md-6"><input type="text" style="width:100%" name="codfisc2" id="codfisc2" placeholder="Enter the parent 2's SSN code" autocomplete="off" title="Enter the parent 2's SSN code"></td></tr>
		<tr id="row4" style="visibility: hidden;"><td class="col-md-6"><label for="email2">E-mail: </label></td><td class="col-md-6"><input type="email" style="width:100%" name="email2" id="email2" placeholder="Enter the parent 2's e-mail" autocomplete="off" title="Enter the parent 2's e-mail"></td></tr>
	</table>
		<input type="reset" class="btn btn-default btn-lg" name="enrollCancel" id="enrollCancel" value="Cancel">
		<input type="submit" class="btn btn-primary btn-lg" name="enrollSubmit" id="enrollSubmit" value="Enroll student">
	</form>
</div>

<?php 
    require_once("defaultFooter.php")
?>