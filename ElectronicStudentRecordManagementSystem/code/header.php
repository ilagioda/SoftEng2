    <!DOCTYPE html>
    <html lang="en">
    <head>
    <title>Electronic Student Record Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <style>

        .navbar {
        min-height: 80px;
        }

        .form-control{
            margin-bottom:10px;
            width:30%;
            margin-left:auto;
            margin-right:auto;
        }

        .btn.btn-lg.btn-primary.btn-block{
            margin-bottom:10px;
            width:30%;
            height:100%;
            margin-left:auto;
            margin-right:auto;
        }

        .navbar-brand {
        padding: 0 15px;
        height: 80px;
        line-height: 80px;
        }

        .navbar-toggle {
        /* (80px - button height 34px) / 2 = 23px */
        margin-top: 23px;
        padding: 9px 10px !important;
        }

        @media (min-width: 768px) {
        .navbar-nav > li > a {
            /* (80px - line-height of 27px) / 2 = 26.5px */
            padding-top: 26.5px;
            padding-bottom: 26.5px;
            line-height: 27px;
        }
        }

        .logo{
            padding-top: 0px;
            height: 80px;
            width: 80px;
        }

        /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
        .row.content {height: 640px}

        /* Set gray background color and 100% height */
        .sidenav {
        padding-top: 20px;
        background-color: #f1f1f1;
        height: 100%;
        }

        /* Set black background color, white text and some padding */
        footer {
        background-color: #555;
        color: white;
        padding: 15px;
        }

        /* On small screens, set height to 'auto' for sidenav and grid */
        @media screen and (max-width: 767px) {
        .sidenav {
            height: auto;
            padding: 15px;
        }
        .row.content {height:auto;}
        }
    </style>
    </head>

<script type="text/javascript" src="functions.js"></script>
<script src="jquery.js"></script>
<?php
require_once 'redirect.php';
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

require_once 'inactivity.php';

?>