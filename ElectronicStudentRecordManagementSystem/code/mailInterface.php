<?php
require_once "basicChecks.php";
require_once "defaultNavbar.php";
$_SESSION['user'] = "ueue";
$_SESSION['role'] = "admin";
require_once "db.php";
$_SESSION['db'] = new dbAdmin();
?>

<script>

function sendMail(id, cl){
    email="";
    $.post("sendmail.php", {'mail' : id},
    function(response){
      if(response.includes("Message has been sent.")){
        document.getElementById("answer").innerHTML = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Success! Message sent.</strong></div>';
        document.getElementById(cl).className="danger";
      }
      else{
        document.getElementById("answer").innerHTML = '<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Error. Message has not been sent.</strong></div>';
      }
    });
}

</script>

<div class="container">
  <h2>Emails to be confirmed</h2>
  <span class="label label-success col-md-3 col-md-push-9">Email not sent yet</span><br>
  <span class="label label-danger col-md-3 col-md-push-9">Email sent, but still to be confirmed</span>
  <p class="text-center">Search by email address:</p>
  <input class="form-control" id="myInput" type="text" placeholder="Search..">
  <br>
  <div class="col-md-12">
  <div id="answer"> </div>

  


  </div>
  <table class="table table-striped sendmail">
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
            <tr class="success" id="r$i">
            <td>$email</td>
            <td class="td_resized_sendmail"><button type="button" class="btn btn-success primary btn-lg" id="$email" onclick="sendMail(id, 'r$i')">SEND</td>
          </tr>
        _SUCCESS;
    } else if ($hashedPw != null && $firstLogin == true) {
        //email già mandata, ma utente non ha ancora modificato pw (c'è quella di default)
        echo <<<_SENT
            <tr class="danger" id="r$i">
            <td>$email</td>
            <td class="td_resized_sendmail"><button type="button" class="btn btn-success primary btn-lg" id="$email" onclick="sendMail(id, 'r$i')">SEND</td>
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
