<?php
    require(__DIR__ . "/../../partials/nav.php");

    if (!is_logged_in()) {
        flash("You don't have permission to view this page", "warning");
        redirect("home.php");
    }

    $uid = get_user_id();
    $query = "SELECT account_number, account_type, balance, created, id from Accounts ";
    $params = null;

    $query .= " WHERE user_id = :uid AND active = 1 AND frozen = 0";
    $params =  [":uid" => "$uid"];

    $query .= " ORDER BY created desc";
    $db = getDB();
    error_log("user_id: $uid");
    error_log("query: $query");
    $stmt = $db->prepare($query);
    $accounts = [];
    try {
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($results) {
            $accounts = $results;
            //echo var_export($accounts, true); 
        } else {
            flash("No accounts found", "warning");
        }
    } catch (PDOException $e) {
        flash(var_export($e->errorInfo, true), "danger");
    }

    if (isset($_POST["src_id"]) && isset($_POST["dest_id"]) && isset($_POST["transfer"])) 
    {
        $transfer = (int)se($_POST, "transfer", "", false);
        $src = (int)se($_POST, "src_id", "", false);
        $dest = (int)se($_POST, "dest_id", "", false);
        $dest_type = get_account_type($dest);
        //flash("dest type = $dest_type");
        $memo = $_POST["memo"];
        //$balance = get_account_balance($src);
        //flash("balance = $balance");
        if($src == $dest)
        {
            flash("Cannot transfer to the same account", "warning");
        }
        else if (!($transfer > 0))
        {
            flash("Input a value to transfer (Greater than 0)", "warning");
        }
        else if($transfer > get_account_balance($src))
        {
            flash("Insufficient Funds", "warning");
        }
        else
        {
            if($dest_type == 'loan')
            {
                $owe = get_account_balance($dest)*-1;
                if($owe < $transfer)
                {
                    flash("You only owe $owe to pay off your loan", "warning");
                }
                else
                {
                    change_balance($transfer, "transfer", $src, $src, $dest, $memo);
                    refresh_account_balance($src);
                    refresh_account_balance($dest);
                    flash("Transfer was successful", "success");
                    if(get_account_balance($dest) == 0)
                        flash("Congratulations! You payed off your loan", "success");
                    redirect("my_accounts.php");
                }
            }
            else{
                change_balance($transfer, "transfer", $src, $src, $dest, $memo);
                refresh_account_balance($src);
                refresh_account_balance($dest);
                flash("Transfer was successful", "success");

                redirect("my_accounts.php");
            }
        }
    }
    else
        flash("Account Not Selected", "warning");
?>

<div class="container-fluid">
    <h2>Transfer</h2>
    <div>
        <form method="POST">
            <div class="mb-3">
                <label for="sourceList" class="form-label">Choose an Account to Transfer Money From</label>
                <select class="form-select" name="src_id" id="sourceList" autocomplete="off">
                <?php if (!empty($accounts)) : ?>
                    <?php foreach ($accounts as $account) : ?>
                        <?php if(se($account, "account_type", "", false) != 'loan') : ?>
                        <option value="<?php se($account, 'id'); ?>">
                            <?php se($account, "account_number"); ?> (Type: <?php se($account, 'account_type'); ?>; Balance = $<?php se($account, "balance"); ?>)
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?> 
                </select>
            </div>
            <div>
                <div class="mb-3">
                <label for="destList" class="form-label">Choose an Account to Transfer Money To</label>
                <select class="form-select" name="dest_id" id="destList" autocomplete="off">
                <?php if (!empty($accounts)) : ?>
                    <?php foreach ($accounts as $account) : ?>
                        <option value="<?php se($account, 'id'); ?>">
                            <?php se($account, "account_number"); ?> (Type: <?php se($account, 'account_type'); ?>; Balance = $<?php se($account, "balance"); ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?> 
                </select>
            </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="d">Amount to Transfer</label>
                <input class="form-control" type="number" name="transfer" id="d"></input>
            </div>
            <div class="mb-3">
                <label class="form-label" for="m">Memo</label>
                <input class="form-control" type="text" placeholder="Transfer" aria-label="default input example" name="memo">
            </div>
            <input type="submit" value="Transfer" />
        </form>
    </div>
</div>

<?php
    require_once(__DIR__ . "/../../partials/flash.php");
?>