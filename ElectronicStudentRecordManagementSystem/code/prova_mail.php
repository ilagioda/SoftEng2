<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'C:\xampp\composer\vendor\autoload.php';
require_once("classphpmailer.php");
require_once("class.smtp.php");

$mail = new PHPMailer(); 

$mail->IsSMTP();  // telling the class to use SMTP
$mail->Mailer = "smtp";
$mail->SMTPDebug = 4;
$mail->Host = "smtp.live.com"; // specify main and backup server
$mail->Port = 25; // set the port to use
//$mail->Port = 25; // set the port to use

$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->SMTPSecure = "tls"; // tsl

$mail->Username = "cla_9_6@hotmail.it"; // SMTP username
$mail->Password = "filoca96"; // SMTP password 

$mail->From = "cla_9_6@hotmail.it";
$mail->FromName = "CLAUDIO";

$mail->AddAddress("s261380@studenti.polito.it");
$mail->AddReplyTo("cla_9_6@hotmail.it", "CLAUDIO");
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