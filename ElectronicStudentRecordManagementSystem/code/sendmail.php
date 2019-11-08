<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");

echo <<<_PROVA
    <head><style>
    body {
        padding-top:20px;
    }
    </style></head>
_PROVA;

echo <<<_FORM

<div class="container">
  <h2>Emails to be confirmed</h2>
  <p class="text-center">Search by email address:</p>  
  <input class="form-control" id="myInput" type="text" placeholder="Search..">
  <br>
  <table class="table table-striped sendmail">
    <thead>
      <tr>
        <th>Email</th>
        <th>Confirm</th>
      </tr>
    </thead>
    <tbody id="myTable">
      <tr class="danger">
        <td>john@example.com</td>
        <td class="td_resized_sendmail"><button type="button" class="btn btn-success primary btn-lg">SEND</td>
      </tr>
      <tr class="info">
      <td>mary@mail.com</td>
        <td class="td_resized_sendmail"><button type="button" class="btn btn-success primary btn-lg">SEND</td>
      </tr>
      <tr class="success">
        <td>july@greatstuff.com</td>
        <td class="td_resized_sendmail"><button type="button" class="btn btn-success primary btn-lg">SEND</td>
      </tr>
      <tr class="info">
        <td>a_r@test.com</td>
        <td class="td_resized_sendmail"><button type="button" class="btn btn-success primary btn-lg">SEND</td>
      </tr>
    </tbody>
  </table>
</div>

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

_FORM;

//USEFUL TO PRINT ALERT MESSAGES IF FORM IS OK OR NOT

/* <div class="col-md-12">
<small><i></i>Add alerts if form ok... success, else error.</i></small>
<div class="alert alert-success"><strong><span class="glyphicon glyphicon-send"></span> Success! Message sent. (If form ok!)</strong></div>
<div class="alert alert-danger"><span class="glyphicon glyphicon-alert"></span><strong> Error! Please check the inputs. (If form error!)</strong></div>
</div> */

//TO SAY THAT A CERTAIN INPUT IS GOOD WITH JS ADD A <i> glyphoon </i> between <span class="input-group-addon"> and </span>

require_once("defaultFooter.php");
