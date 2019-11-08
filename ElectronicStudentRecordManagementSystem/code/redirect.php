<?php
if ( !empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off')) {
    // La richiesta e' stata fatta su HTTPS
} else {
    // Redirect su HTTPS
    // eventuale distruzione sessione e cookie relativo
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] .
    $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
?>