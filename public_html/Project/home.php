<?php
require(__DIR__."/../../partials/nav.php");
?>
<h1>Home</h1>
<?php
if(is_logged_in())
{
  //echo "Welcome, " . get_user_email(); 
  flash("Welcome " . get_user_email());
}
else
{
  //echo "You're not logged in";
  flash("You're not logged in");
}
require(__DIR__ . "/../../partials/flash.php");
?>