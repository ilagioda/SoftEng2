<?php
require_once("basicChecks.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'COMPOSER/vendor/autoload.php';
require_once("classphpmailer.php");
require_once("class.smtp.php");

if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
    die("$to_address is not a valid mail address");
}

$to_address = $_POST['mail'];
$teacher = $_POST['teacher'];
$day = $_POST['day'];

// echo "my to-address is ".$to_address;

$mail = new PHPMailer(); 

$mail->IsSMTP();  // telling the class to use SMTP
$mail->Mailer = "smtp";
$mail->SMTPDebug = 4;
$mail->Host = "smtp.live.com"; // specify main and backup server
$mail->Port = 25; // set the port to use


$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->SMTPSecure = "tls"; // tls

$mail->Username = "no-reply-se2school@hotmail.com"; // SMTP username
$mail->Password = "softeng2"; // SMTP password 

$mail->From = "no-reply-se2school@hotmail.com";
$mail->FromName = "Softeng2 School";

$mail->AddAddress($to_address); //cla_9_6@hotmail.it
$mail->AddReplyTo("no-reply-se2school@hotmail.com", "Softeng2 School"); //no-reply-se2school@hotmail.com
$mail->IsHTML(true);

$mail->Subject  = "ElectronicStudentRecordManagementSystem Credentials";
//$mail->Body     = "Hi! \n\n Your credentials are the following. \n USER: $to_address \n PASSWORD: $pw\n ";
$mail->Body = 'Communication from the PoliTOschool:
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<div>
        <p> We are sorry to inform you that the teacher '.$teacher.' has cancelled all the slots of '.$day.' which were initially available for parent meetings.
        <br><br>Kind regards.<br></p>
        
</div>
</body>
</html>';
$mail->WordWrap = 50;

if(!$mail->Send()) {
  echo 'Message was not sent.';
  echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
  echo 'Message has been sent.';
}
?>