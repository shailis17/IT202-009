<?php
//Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if(strpos($domain, ":"))
    $domain = explode(":", $domain)[0];
session_set_cookie_params([
    "lifetime" => 60 * 60,
    "path" => "/Project",
    //domain => $_SERVER["HTTP_HOST"] || "localhost",
    "domain" => $domain,
    "secure" => true,
    "httponly" => true,
    "samesite" => "lax"
]);
session_start();
//include functions here so we can have it on every page that uses the nav bar
//that way we don't need to include so many other files on each page
//nav will pull in functions and functions will pull in db
require(__DIR__."/../lib/functions.php");
?>

<!-- include css and js files -->
<link rel="stylesheet" href="styles.css">
<script src="helpers.js"></script>

<nav>
    <ul>
        <?php if (is_logged_in()) : ?>
            <li><a href="home.php">Home</a></li>
        <?php endif; ?>
        <?php if (!is_logged_in()) : ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()) : ?>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>