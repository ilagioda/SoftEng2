<?php
require_once("basicChecks.php");
require_once("defaultNavbar.php");
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
<div class="container text-center">
    <form class="form-signin" action="chooseChild.php" method="POST">
        <h1> Hello Parent!</h1>
        <h2 class="form-signin-heading">Please enter your e-mail</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" name="email" class="form-control" placeholder="Email address" required="" autofocus="">
        <button class="btn btn-lg btn-primary btn-block login" type="submit">Sign in</button>
    </form>
</div>