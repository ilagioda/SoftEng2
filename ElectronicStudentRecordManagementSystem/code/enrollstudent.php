<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
    
    /* FIXME remove the next lines when login is implemented */
    
    $_SESSION['user'] = 'GDILRI';
    $_SESSION['role'] = 'admin';
    
    /* End lines to be changed*/
?>

<script type='text/javascript'>

	/* The follow flags are 'false' if the corresponding fields are empty; 'true' if they are not empty */
	var stud_name_FLAG = false;
	var stud_surname_FLAG = false;
	var stud_ssn_FLAG = false;
	var par1_name_FLAG = false;
	var par1_surname_FLAG = false;
	var par1_ssn_FLAG = false;
	var par1_email_FLAG = false;
	var par2_name_FLAG = false;
	var par2_surname_FLAG = false;
	var par2_ssn_FLAG = false;
	var par2_email_FLAG = false;

	fuction updateStudName(){
		var stud_name = document.getElementById("name");
		if(stud_name === "")
			stud_name_FLAG = false;
		else
			stud_name_FLAG = true;
		
		checkAll();
	}

	fuction updateStudSurname(){
		var stud_surname = document.getElementById("surname");
		if(stud_surname === "")
			stud_surname_FLAG = false;
		else
			stud_surname_FLAG = true;

		checkAll();
	}

	fuction updateStudSSN(){
		var stud_ssn = document.getElementById("codfisc");
		if(stud_ssn === "")
			stud_ssn_FLAG = false;
		else
			stud_ssn_FLAG = true;
		
		checkAll();
	}

	fuction updatePar1Name(){
		var par1_name = document.getElementById("name1");
		if(par1_name === "")
			par1_name_FLAG = false;
		else
			par1_name_FLAG = true;
		
		checkAll();
	}

	fuction updatePar1Surname(){
		var par1_surname = document.getElementById("surname1");
		if(par1_surname === "")
			par1_surname_FLAG = false;
		else
			par1_surname_FLAG = true;
		
		checkAll();
	}

	fuction updatePar1SSN(){
		var par1_ssn = document.getElementById("codfisc1");
		if(par1_ssn === "")
			par1_ssn_FLAG = false;
		else
			par1_ssn_FLAG = true;

		checkAll();
	}

	fuction updatePar1Email(){
		var par1_email = document.getElementById("email1");
		if(par1_email === "")
			par1_email_FLAG = false;
		else
			par1_email_FLAG = true;
		
		checkAll();
	}

	fuction updatePar2Name(){
		var par2_name = document.getElementById("name2");
		if(par2_name === "")
			par2_name_FLAG = false;
		else
			par2_name_FLAG = true;

		checkAll();
	}

	fuction updatePar2Surname(){
		var par2_surname = document.getElementById("surname2");
		if(par2_surname === "")
			par2_surname_FLAG = false;
		else
			par2_surname_FLAG = true;

		checkAll();
	}

	fuction updatePar2SSN(){
		var par2_ssn = document.getElementById("codfisc2");
		if(par2_ssn === "")
			par2_ssn_FLAG = false;
		else
			par2_ssn_FLAG = true;

		checkAll();
	}

	fuction updatePar2Email(){
		var par2_email = document.getElementById("email2");
		if(par2_email === "")
			par2_email_FLAG = false;
		else
			par2_email_FLAG = true;

		checkAll();
	}

	function checkAll(){
		document.getElementById("enrollSubmit").disabled = true;
		if(stud_name_FLAG && stud_surname_FLAG && stud_ssn_FLAG && par1_name_FLAG && par1_surname_FLAG && par1_ssn_FLAG && par1_email_FLAG && par2_name_FLAG && par2_surname_FLAG && par2_ssn_FLAG && par2_email_FLAG){
			/* All the fields (even Parent 2 fields) have been filled */
			document.getElementById("enrollSubmit").disabled = false;
			document.getElementById("errorMessage").innerHTML = "Attention! Still missing information!";
		} else if (stud_name_FLAG && stud_surname_FLAG && stud_ssn_FLAG && par1_name_FLAG && par1_surname_FLAG && par1_ssn_FLAG && par1_email_FLAG && !par2_name_FLAG && !par2_surname_FLAG && !par2_ssn_FLAG && !par2_email_FLAG){
			/* All the fields (but Parent 2 fields are ALL EMPTY) have been filled */
			document.getElementById("enrollSubmit").disabled = false;
			document.getElementById("errorMessage").innerHTML = "Attention! Still missing information!";
		}
	}


	// function allinfocheck(){

	// 	var somethingmissing = 0;

	// 	var stud_name = document.getElementById("name");
	// 	var stud_surname = document.getElementById("surname");
	// 	var stud_ssn = document.getElementById("codfisc");
	// 	var par1_name = document.getElementById("name1");
	// 	var par1_surname = document.getElementById("surname1");
	// 	var par1_ssn = document.getElementById("codfisc1");
	// 	var par1_email = document.getElementById("email1");

	// 	/* Check if all essential fields have been filled */
	// 	if(stud_name === "" || stud_surname === "" || stud_ssn === "" || par1_name === "" || par1_surname === "" || par1_ssn === "" || par1_email === ""){
	// 		somethingmissing = 1;
	// 	}else { 
	// 		/* Check wheter all or none the fields about parent 2 haev been filled */
	// 		var par2_name = document.getElementById("name2");
	// 		var par2_surname = document.getElementById("surname2");
	// 		var par2_ssn = document.getElementById("codfisc2");
	// 		var par2_email = document.getElementById("email2");
	// 		if(par1_name === "" && par1_surname === "" && par1_ssn === "" && par1_email === ""){
	// 			somethingmissing = 0;
	// 		} else if (par1_name !== "" && par1_surname !== "" && par1_ssn !== "" && par1_email !== "") {
	// 			somethingmissing = 0;
	// 		} else {
	// 			somethingmissing = 1;
	// 		}
	// 	}

	// 	if(somethingmissing){
	// 		document.getElementById("errorMessage").innerHTML = "Attention! Still missing information!";
	// 		document.getElementById("enrollSubmit").disabled = true;  
	// 	} else {
	// 		document.getElementById("enrollSubmit").disabled = false;  
	// 	}
	}
</script>

<form method="POST" action="homepageAdmin.php">
	<input type="submit" name="homepageAdmin" id="homepageAdmin" value="Homepage"> 	
</form>

<h1 class="enrollTitle" align="center"> Enroll a student</h1>
<div class="centralDiv">
	<form method="POST" action="enrollstudentPART2.php" id="enrollForm">
	<table id="enrollTable">
		<tr class="info"><td><b>STUDENT INFORMATION</b></td><td></td></tr>
		<tr><td><label for="name">Name*: </label></td><td><input type="text" size="50" name="name" id="name" placeholder="Enter the student's name" autocomplete="off" onkeyup="updateStudName()" title="Enter the student's name" required></td></tr>
		<tr><td><label for="surname">Surname*: </label></td><td><input type="text" size="50" name="surname" id="surname" placeholder="Enter the student's surname" autocomplete="off" onkeyup="updateStudSurname()" title="Enter the student's surname" required></td>
		<tr><td><label for="codfisc">SSN code*: </label></td><td><input type="text" size="50" name="codfisc" id="codfisc" placeholder="Enter the student's SSN code" autocomplete="off" onkeyup="updateStudSSN()" title="Enter the student's SSN code" required></td>
		
		<tr class="info"><td><b>PARENT 1 INFORMATION</b></td><td></td></tr>
		<tr><td><label for="name1">Name*: </label></td><td><input type="text" size="50" name="name1" id="name1" placeholder="Enter the parent 1's name" autocomplete="off" onkeyup="updatePar1Name()" title="Enter the student's name" required></td></tr>
		<tr><td><label for="surname1">Surname*: </label></td><td><input type="text" size="50" name="surname1" id="surname1" placeholder="Enter the parent 1's surname" autocomplete="off" onkeyup="updatePar1Surname()" title="Enter the student's surname" required></td>
		<tr><td><label for="codfisc1">SSN code*: </label></td><td><input type="text" size="50" name="codfisc1" id="codfisc1" placeholder="Enter the parent 1's SSN code" autocomplete="off" onkeyup="updatePar1SSN()" title="Enter the student's SSN code" required></td>
		<tr><td><label for="email1">E-mail*: </label></td><td><input type="email" size="50" name="email1" id="email1" placeholder="Enter the parent 1's e-mail" autocomplete="off" onkeyup="updatePar1Email()" title="Enter the parent 2's e-mail" required></td></tr>
		
		<tr class="info"><td><b>PARENT 2 INFORMATION</b></td><td></td></tr>
		<tr><td><label for="name2">Name: </label></td><td><input type="text" size="50" name="name2" id="name2" placeholder="Enter the parent 2's name" autocomplete="off" onkeyup="updatePar2Name()" title="Enter the student's name"></td></tr>
		<tr><td><label for="surname2">Surname: </label></td><td><input type="text" size="50" name="surname2" id="surname" placeholder="Enter the parent 2's surname" autocomplete="off" onkeyup="updatePar2Surname()" title="Enter the student's surname"></td>
		<tr><td><label for="codfisc2">SSN code: </label></td><td><input type="text" size="50" name="codfisc2" id="codfisc" placeholder="Enter the parent 2's SSN code" autocomplete="off" onkeyup="updatePar2SSN()" title="Enter the student's SSN code"></td>
		<tr><td><label for="email2">E-mail: </label></td><td><input type="email" size="50" name="email2" id="email2" placeholder="Enter the parent 2's e-mail" autocomplete="off" onkeyup="updatePar2Email()" title="Enter the parent 2's e-mail"></td></tr>
	</table>
		<input type="reset" name="enrollCancel" id="enrollCancel" value="Cancel"> 
		<input type="submit" class="pulsante" name="enrollSubmit" id="enrollSubmit" value="Enroll student">
	</form>
	<p id="errorMessage"></p>
</div>

<?php 
    require_once("defaultFooter.php")
?>