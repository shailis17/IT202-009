<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

if (isset($_POST["a_type"]) && isset($_POST["deposit"])) 
{
    $type = se($_POST, "a_type", "", false);
    $apy = getAPY($type);
    //flash("rate: $apy");
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
            $stmt = $db->prepare("INSERT INTO Accounts (account_number, user_id, balance, account_type, apy) VALUES(:an, :uid, :deposit, :type, :apy)");
            $uid = get_user_id(); //caching a reference

            try {
                $stmt->execute([":an" => $an, ":uid" => null, ":type" => null, ":deposit" => null, ":apy" => null]);
                $account_id = $db->lastInsertId();
                //flash("account_id = $account_id");
                $an = str_pad($account_id+1,12,"202", STR_PAD_LEFT);
                $stmt->execute([":an" => $an, ":uid" => $uid, ":type" => $type, ":deposit" => $deposit, ":apy" => $apy]);
                
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
        redirect("my_accounts.php");
    }
}
else
    flash("Account type must be selected", "warning");

?>

<div class="container-fluid">
    <h2>Create Account</h2>
    <form method="POST">
        <div class="form-check">
            <label for="sourceList" class="form-label">Choose an Account Type</label>
            <select class="form-select" name="a_type" id="accountTypes" autocomplete="off">
                <option value="checkings">Checkings</option>
                <option value="savings">Savings</option>
            </select>
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