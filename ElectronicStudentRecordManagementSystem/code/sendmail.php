<?php
require_once("basicChecks.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'COMPOSER/vendor/autoload.php';
require_once("classphpmailer.php");
require_once("class.smtp.php");
require_once "db.php";
$db = new dbAdmin();

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
    // FIXME: change the action
    die("invalid mail address");
}

$to_address = $_POST['mail'];

// echo "my to-address is ".$to_address;

$mail = new PHPMailer(); 

$mail->IsSMTP();  // telling the class to use SMTP
$mail->Mailer = "smtp";
$mail->SMTPDebug = 4;
$mail->Host = "smtp.live.com"; // specify main and backup server
$mail->Port = 25; // set the port to use
//$mail->Port = 25; // set the port to use

$pw = generateRandomString();
//echo "PW vale: $pw";
$hashed_pw = password_hash($pw, PASSWORD_DEFAULT);
//echo "hashedPW vale: $hashed_pw";
if(!$db->ChangePassword($to_address, $hashed_pw, "Parents")){
    echo "TO ADDR: ". $to_address;
    echo "HASHED PW: ". $hashed_pw;
    die("Unable to change password for $to_address");
}

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
$mail->Body = 'The credentials you asked for are the following:
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<div>
        <p> USER: ' .$to_address .' <br> PASSWORD: '. $pw .' <br></p>
        <p>Please click <a href ="https://localhost/Softeng2/SoftEng2/ElectronicStudentRecordManagementSystem/code/login.php">HERE </a> to access your login page</p>
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