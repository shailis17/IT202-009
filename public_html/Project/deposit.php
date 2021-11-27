<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
?>

<div class="container-fluid">
    <h2>Deposit</h2>
    <p>This is where users will fill out a form and be able to deposit balance into one of their accounts</p>
</div>

