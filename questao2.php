<?php

// questão 2, exercício de refatoração
$loggedinBySession = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
$loggedinByCookie = isset($_COOKIE['loggedin']) && $_COOKIE['loggedin'];

if ($loggedinBySession || $loggedinByCookie) {
    header("Location: http://www.google.com");
    exit();
}
