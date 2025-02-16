<?php
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";

if (!is_logged_in()) {
    die(header("Location: login.php"));
}

?>

You did it! 