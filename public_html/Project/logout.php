<?php
session_start();
session_unset();
session_destroy();
require(__DIR__ . "/../../partials/flash.php");
flash("Succesfully logged out", "success");
header("Location: login.php");