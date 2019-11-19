<?php // Example 26-12: logout.php
require_once("basicChecks.php");

if (isset($_SESSION['user']))
{
    destroySession();
    header("Location: index.php");
}
?>