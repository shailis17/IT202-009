<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

if (isset($_POST["checkings"]) && isset($_POST["deposit"])) 
{
    $type = "checkings";
    $deposit = (int)se($_POST, "deposit", "", false);
    if ($deposit < 5) 
    {
        flash("Minimum deposit is $5", "warning");
    } 
    else 
    {
        try 
        {
            $db = getDB();
            $an = null;
            $stmt = $db->prepare("INSERT INTO Accounts (account_number, user_id, balance, account_type) VALUES(:an, :uid, :deposit, :type)");
            $uid = get_user_id(); //caching a reference

            try {
                $stmt->execute([":an" => $an, ":uid" => null, ":type" => null, ":deposit" => null]);
                $account_id = $db->lastInsertId();
                //flash("account_id = $account_id");
                $an = str_pad($account_id+1,12,"202", STR_PAD_LEFT);
                $stmt->execute([":an" => $an, ":uid" => $uid, ":type" => $type, ":deposit" => $deposit]);
                
                flash("Successfully created account!", "success");
            } 
            catch (PDOException $e) {
                flash("An unexpected error occurred, please try again " . var_export($e->errorInfo, true), "danger");
            }
        }
        catch (PDOException $e) 
        {
            $code = se($e->errorInfo, 0, "00000", false);
            //if it's a duplicate error, just let the loop happen
            //otherwise throw the error since it's likely something looping won't resolve
            //and we don't want to get stuck here forever
            if ($code !== "23000") 
            {
                throw $e;
            }
        }

        $aid = $account_id + 1;
        change_balance($deposit, "deposit", $aid, -1, $aid, "opening balance");
        refresh_account_balance($aid);
        die(header("Location: " . get_url("my_accounts.php")));
    }
}
else
    flash("Account type must be selected", "warning");
?>

<div class="container-fluid">
    <h2>Create Account</h2>
    <form method="POST">
        <div class="form-check">
            <h4>Account Type</h4>
            <input class="form-check-input" type="radio" name="checkings" id="checkings">
            <label class="form-check-label" for="checkings">
                Checkings
            </label>
        </div>
        <div class="mb-3">
            <label class="form-label" for="d">Deposit (Min = $5) </label>
            <input class="form-control" type="number" name="deposit" id="d"></input>
        </div>
        <input type="submit" value="Create Account" />
    </form>
</div>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>