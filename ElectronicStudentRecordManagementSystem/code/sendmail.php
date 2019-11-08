<?php
require_once("basicChecks.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'C:\xampp\composer\vendor\autoload.php';
require_once("classphpmailer.php");
require_once("class.smtp.php");

$to_address = $_POST['mail'];

echo "my to-address is ".$to_address;

$mail = new PHPMailer(); 

$mail->IsSMTP();  // telling the class to use SMTP
$mail->Mailer = "smtp";
$mail->SMTPDebug = 4;
$mail->Host = "smtp.live.com"; // specify main and backup server
$mail->Port = 25; // set the port to use
//$mail->Port = 25; // set the port to use

$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->SMTPSecure = "tls"; // tls

$mail->Username = "no-reply-se2school@hotmail.com"; // SMTP username
$mail->Password = "softeng2"; // SMTP password 

$mail->From = "no-reply-se2school@hotmail.com";
$mail->FromName = "Softeng2 School";

$mail->AddAddress($to_address); //cla_9_6@hotmail.it
$mail->AddReplyTo("no-reply-se2school@hotmail.com", "Softeng2 School"); //no-reply-se2school@hotmail.com
$mail->IsHTML(true);

$mail->Subject  = "First PHPMailer Message";
$mail->Body     = "Hi! \n\n This is my first e-mail sent through PHPMailer.";
$mail->WordWrap = 50;

if(!$mail->Send()) {
  echo 'Message was not sent.';
  echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
  echo 'Message has been sent.';
}
?>