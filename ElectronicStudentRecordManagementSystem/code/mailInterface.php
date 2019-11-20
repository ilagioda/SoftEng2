<?php
require_once "basicChecks.php";

$loggedin=false;
if(isset($_SESSION['user']) && $_SESSION['role'] == "admin"){
    $loggedin = true;
}
if(!$loggedin){
    //require_once("defaultNavbar.php");
    header("Location: login.php");
}
else{
    require_once("loggedAdminNavbar.php");
}
require_once "db.php";
$_SESSION['db'] = new dbAdmin();
?>

<script>

function sendMail(id, cl){
  element = document.getElementById("td_load" + cl);
  element_before = document.getElementById("td_load" + cl).innerHTML;
  element.innerHTML='<div class="loader"></div>';
    email="";
    $.post("sendmail.php", {'mail' : id},
    function(response){
      if(response.includes("Message has been sent.")){
        document.getElementById("answer").innerHTML = document.getElementById("answer").innerHTML + '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Success! Credentials sent to '+ id +'.</strong></div>';
        document.getElementById(cl).className="";
        document.getElementById("td_load"+cl).innerHTML = element_before;
      }
      else{
        document.getElementById("answer").innerHTML = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Error. Message has not been sent.</strong></div>';
        document.getElementById("td_load"+cl).innerHTML = element_before;
      }
    });
}

</script>

<div class="container">
  <h2>Emails to be confirmed</h2>
  <span class="badge badge-pill badge-yellow col-md-3 col-md-push-9">Email not sent yet</span><br>
  <span class="badge badge-pill badge-white col-md-3 col-md-push-9">Email sent, but still to be confirmed</span>
  <p class="text-center">Search by email address:</p>
  <input class="form-control" id="myInput" type="text" placeholder="Search..">
  <br>
  <div class="col-md-12">
  <div id="answer"> </div>

  


  </div>
  <table class="table sendmail">
    <thead>
      <tr>
        <th>Email</th>
        <th>Confirm</th>
      </tr>
    </thead>
    <tbody id="myTable">
    <?php
$i = 0;
$ParentsMailList = $_SESSION['db']->TakeParentsMail();
foreach ($ParentsMailList as $tuple) {
    $email = $tuple['email'];
    $hashedPw = $tuple['hashedPassword'];
    $firstLogin = $tuple['firstLogin'];
    if ($hashedPw == null && $firstLogin == true) {
        //email ancora da inviare
        echo <<<_SUCCESS
            <tr class="warning" id="r$i">
            <td>$email</td>
            <td class="td_resized_sendmail" id="td_loadr$i"><button type="button" class="btn btn-success primary btn-lg" id="$email" onclick="sendMail(id, 'r$i')">SEND</td>
          </tr>
_SUCCESS;
    } else if ($hashedPw != null && $firstLogin == true) {
        //email già mandata, ma utente non ha ancora modificato pw (c'è quella di default)
        echo <<<_SENT
            <tr class="default" id="r$i">
            <td>$email</td>
            <td class="td_resized_sendmail" id="td_loadr$i"><button type="button" class="btn btn-success primary btn-lg" id="$email" onclick="sendMail(id, 'r$i')">SEND</td>
            </tr>
_SENT;
    }
    $i++;
}

?>
    </tbody>
  </table>
  </div>
  <?php
require_once "defaultFooter.php";
?>

<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    
  });

});

</script>

<!-- //USEFUL TO PRINT ALERT MESSAGES IF FORM IS OK OR NOT

<div class="col-md-12">
<small><i></i>Add alerts if form ok... success, else error.</i></small>
<div class="alert alert-success"><strong><span class="glyphicon glyphicon-send"></span> Success! Message sent. (If form ok!)</strong></div>
<div class="alert alert-danger"><span class="glyphicon glyphicon-alert"></span><strong> Error! Please check the inputs. (If form error!)</strong></div>
</div>

//TO SAY THAT A CERTAIN INPUT IS GOOD WITH JS ADD A <i> glyphoon </i> between <span class="input-group-addon"> and </span> -->
