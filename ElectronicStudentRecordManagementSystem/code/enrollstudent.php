<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
    
    /* FIXME remove the next lines when login is implemented */
    
    $_SESSION['user'] = 'GDILRI';
    $_SESSION['role'] = 'admin';
    
    /* End lines to be changed*/
?>

<script type='text/javascript'>

	function changeVisibility(){
		var state = document.getElementById("tableParent2").style.display;
		if(state === "none"){
			document.getElementById("tableParent2").style.display = "inline-block";
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
			document.getElementById("tableParent2").style.display = "none";
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

<form method="POST" action="homepageAdmin.php">
	<input type="submit" name="homepageAdmin" id="homepageAdmin" value="Homepage"> 	
</form>

<h1 class="enrollTitle" align="center"> Enroll a student</h1>
<div class="centralDiv">
	<form method="POST" action="enrollstudentPART2.php" id="enrollForm">
	<table class="enrollTable">
		<tr class="info"><td><b>STUDENT INFORMATION</b></td><td></td></tr>
		<tr><td><label for="name">Name*: </label></td><td><input type="text" size="50" name="name" id="name" placeholder="Enter the student's name" autocomplete="off" title="Enter the student's name" required></td></tr>
		<tr><td><label for="surname">Surname*: </label></td><td><input type="text" size="50" name="surname" id="surname" placeholder="Enter the student's surname" autocomplete="off" title="Enter the student's surname" required></td></tr>
		<tr><td><label for="codfisc">SSN code*: </label></td><td><input type="text" size="50" name="codfisc" id="codfisc" placeholder="Enter the student's SSN code" autocomplete="off" title="Enter the student's SSN code" required></td></tr>
		<tr><td></td></tr>
		<tr class="info"><td><b>PARENT 1 INFORMATION</b></td><td></td></tr>
		<tr><td><label for="name1">Name*: </label></td><td><input type="text" size="50" name="name1" id="name1" placeholder="Enter the parent 1's name" autocomplete="off" title="Enter the student's name" required></td></tr>
		<tr><td><label for="surname1">Surname*: </label></td><td><input type="text" size="50" name="surname1" id="surname1" placeholder="Enter the parent 1's surname" autocomplete="off" title="Enter the student's surname" required></td></tr>
		<tr><td><label for="codfisc1">SSN code*: </label></td><td><input type="text" size="50" name="codfisc1" id="codfisc1" placeholder="Enter the parent 1's SSN code" autocomplete="off" title="Enter the student's SSN code" required></td></tr>
		<tr><td><label for="email1">E-mail*: </label></td><td><input type="email" size="50" name="email1" id="email1" placeholder="Enter the parent 1's e-mail" autocomplete="off" title="Enter the parent 2's e-mail" required></td></tr>
		<tr><td></td></tr>
		<tr class="info" id="rowParent2" onclick="changeVisibility()"><td><span id="iconParent2" class="glyphicon glyphicon-plus" aria-hidden="true">&emsp;&emsp;</span><b>PARENT 2 INFORMATION</b></td><td></td></tr>
	</table>
	<table class="enrollTable" id="tableParent2" style="display: none;">
		<tr><td><label for="name2">Name: </label></td><td><input type="text" size="50" name="name2" id="name2" placeholder="Enter the parent 2's name" autocomplete="off" title="Enter the student's name"></td></tr>
		<tr><td><label for="surname2">Surname: </label></td><td><input type="text" size="50" name="surname2" id="surname2" placeholder="Enter the parent 2's surname" autocomplete="off" title="Enter the student's surname"></td></tr>
		<tr><td><label for="codfisc2">SSN code: </label></td><td><input type="text" size="50" name="codfisc2" id="codfisc2" placeholder="Enter the parent 2's SSN code" autocomplete="off" title="Enter the student's SSN code"></td></tr>
		<tr><td><label for="email2">E-mail: </label></td><td><input type="email" size="50" name="email2" id="email2" placeholder="Enter the parent 2's e-mail" autocomplete="off" title="Enter the parent 2's e-mail"></td></tr>
	</table>
		<input type="reset" name="enrollCancel" id="enrollCancel" value="Cancel"> 
		<input type="submit" class="pulsante" name="enrollSubmit" id="enrollSubmit" value="Enroll student">
	</form>
	<p id="errorMessage"></p>
</div>

<?php 
    require_once("defaultFooter.php")
?>