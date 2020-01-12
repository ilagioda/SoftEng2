<?php
require_once("redirectHTTPS.php");
require_once("functions.php");

if(!isset($_SESSION)){
    session_start();
}
require_once("inactivity.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Electronic Student Record Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap-3.4.1-dist/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles.css">

    <!-- CHECKING JAVASCRIPT ENABLED -->
    <noscript>
        <meta HTTP-EQUIV="refresh" content="0; url=JavaScriptDisabled.html">
    </noscript>

    <!-- CHECKING COOKIES ENABLED -->
    <script>
        try {
            document.cookie = 'cookietest=1';
            var cookiesEnabled = document.cookie.indexOf('cookietest=') !== -1;
            document.cookie = 'cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT';
            if (!cookiesEnabled) window.location="cookiesDisabled.php";
        } catch (e) {
            window.location="cookiesDisabled.php";
        }
    </script>
</head>