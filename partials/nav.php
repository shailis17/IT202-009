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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" 
crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" 
crossorigin="anonymous"></script>
<link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
<!--<script src="helpers.js"></script> -->
<script src="<?php echo get_url('helpers.js'); ?>"></script>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
    <ul class="navbar-nav">
        <?php if (is_logged_in()) : ?>
            <li><a href="<?php echo get_url('home.php'); ?>">Home</a></li>
            <li><a href="<?php echo get_url('profile.php'); ?>">Profile</a></li>
        <?php endif; ?>
        <?php if (!is_logged_in()) : ?>
            <li><a href="<?php echo get_url('login.php'); ?>">Login</a></li>
            <li><a href="<?php echo get_url('register.php'); ?>">Register</a></li>
        <?php endif; ?>
        <?php if (has_role("Admin")) : ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Roles
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="<?php echo get_url('admin/create_role.php'); ?>">Create Role</a></li>
            <li><a class="dropdown-item" href="<?php echo get_url('admin/list_roles.php'); ?>">List Roles</a></li>
            <li><a class="dropdown-item" href="<?php echo get_url('admin/assign_roles.php'); ?>">Assign Roles</a></li>
          </ul>
        </li>
            <li><a href="<?php echo get_url('admin/view_accounts.php'); ?>">View Accounts</a></li>
            <li><a href="<?php echo get_url('admin/manage_users.php'); ?>">Manage Users</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()) : ?>
            <li><a href="<?php echo get_url('logout.php'); ?>">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>