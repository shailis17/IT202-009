<?php
require(__DIR__."/../../partials/nav.php");
?>
<h1>Home</h1>
<?php
if(is_logged_in(true))
{
  //echo "Welcome, " . get_user_email(); 
  //flash("Welcome " . get_user_email());
  flash("Welcome " . get_username());
  //comment this out if you don't want to see the session variables
  //echo "<pre>" . var_export($_SESSION, true) . "</pre>";
}
else
{
  //echo "You're not logged in";
  flash("You're not logged in");
}
?>

<div class="list-group">
  <a href="<?php echo get_url('my_accounts.php'); ?>" class="list-group-item list-group-item-action active" aria-current="true">
    View My Accounts
  </a>
  <a href="<?php echo get_url('create_account.php'); ?>" class="list-group-item list-group-item-action">Create an Account</a>
  <a href="<?php echo get_url('deposit.php'); ?>" class="list-group-item list-group-item-action">Deposit</a>
  <a href="<?php echo get_url('withdraw.php'); ?>" class="list-group-item list-group-item-action">Withdraw</a>
  <a href="<?php echo get_url('transfer.php'); ?>" class="list-group-item list-group-item-action">Transfer</a>
  <a href="<?php echo get_url('ext_transfer.php'); ?>" class="list-group-item list-group-item-action">External Transfer</a>
  <a href="<?php echo get_url('loan.php'); ?>" class="list-group-item list-group-item-action">Take Out A Loan</a>
</div>

<?php
require(__DIR__ . "/../../partials/flash.php");
?>
