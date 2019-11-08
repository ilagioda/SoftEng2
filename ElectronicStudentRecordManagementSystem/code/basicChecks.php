<?php
require_once("redirectHTTPS.php");
require_once("functions.php");
require_once("inactivity.php");

$previous = $_SERVER['REQUEST_URI'];
////////////////////////////////////// CHECKING NO SCRIPT ENABLED
echo <<<_NOSCRIPT
<noscript>
    <meta HTTP-EQUIV="refresh" content="0;url=JSorCookiesDisabled.php?prev=$previous"></noscript>
_NOSCRIPT;
/////////////////////////////////////////////////////////////////

///////////////////////////////////// CHECKING COOKIES ENABLES
echo <<<_CHKCOOKIES
<script><!--
areCookiesEnabled();
//--></script>
_CHKCOOKIES;
/////////////////////////////////////////////////////////////

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Electronic Student Record Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="functions.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>