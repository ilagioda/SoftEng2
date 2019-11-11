<?php
    require_once("basicChecks.php");
    require_once("defaultNavbar.php");
    
    /* FIXME remove the next lines when login is implemented */
    
    $_SESSION['user'] = 'GDILRI';
    $_SESSION['role'] = 'admin';
    
    /* End lines to be changed*/
    
    require_once("db.php");
    
    $db = new dbAdmin();
    $error = 0;
    $parent2 = 0;
       
    /* Check if all requested fields are set */
    if(!isset($_REQUEST["name"]) || !isset($_REQUEST["surname"]) || !isset($_REQUEST["codfisc"]) || !isset($_REQUEST["name1"]) || !isset($_REQUEST["surname1"]) || !isset($_REQUEST["codfisc1"]) || !isset($_REQUEST["email1"]) || !isset($_REQUEST["name2"]) || !isset($_REQUEST["surname2"]) || !isset($_REQUEST["codfisc2"]) || !isset($_REQUEST["email2"])){
        $error = 1;
    } 
    
    /* Store the data in local variables */
    $student_name = $_REQUEST["name"];
    $student_surname = $_REQUEST["surname"];
    $student_SSN = $_REQUEST["codfisc"];
    
    $parent1_name = $_REQUEST["name1"];
    $parent1_surname = $_REQUEST["surname1"];
    $parent1_SSN = $_REQUEST["codfisc1"];
    $parent1_email = $_REQUEST["email1"];
    
    $parent2_name = $_REQUEST["name2"];
    $parent2_surname = $_REQUEST["surname2"];
    $parent2_SSN = $_REQUEST["codfisc2"];
    $parent2_email = $_REQUEST["email2"];
            
    /* Check if all or none information about parent 2 has been inserted */
    if(!empty($parent2_name) && !empty($parent2_surname) && !empty($parent2_SSN) && !empty($parent2_email)){
        $error = 0;
    } else {
        if(empty($parent2_name) && empty($parent2_surname) && empty($parent2_SSN) && empty($parent2_email)){
            $error = 0;
        } else {
            $error = 1;
        }
    }
    
    if($error == 0){
       $ok = 0;
       $ok = $db->enrollStudent($student_name, $student_surname, $student_SSN, $parent1_name, $parent1_surname, $parent1_SSN, $parent1_email, $parent2_name, $parent2_surname, $parent2_SSN, $parent2_email);
       /* ok = 1 --> student enrolled correctly ; ok = 0 --> error*/
       if($ok == 0){
            $error = 1;
       }
    }
        
?>
<h1 class="enrollTitle" align="center"> Enroll a student</h1>
<div class="enrollDiv">
	<p id="enrollMessage">
		<?php 
    		if($error != 0){
    		    echo "Oh no! Something went wrong... ";
    		} else {
    		    echo "Hurray! Student enrolled correctly!";
      		}
		?>
	</p>
	<form method="POST" action="enrollstudent.php">
		<input type="submit" class="pulsante" name="enrollNewStudent" id="enrollNewStudent" value="Enroll new student">
	</form>
	<form method="POST" action="homepageAdmin.php">
		<input type="submit" class="pulsante" name="enrollGoBackHome" id="enrollGoBackHome" value="Go back to Homepage">
	</form>
</div>

<?php 
    require_once("defaultFooter.php")
?>
