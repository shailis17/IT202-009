<?php
    require_once(__DIR__ . "/../../partials/nav.php");
    if (!is_logged_in()) {
        die(header("Location: login.php"));
    }

    $uid = get_user_id();
    $query = "SELECT account_number, account_type, balance, created, id from Accounts ";
    $params = null;

    $query .= " WHERE user_id = :uid";
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

    if (isset($_POST["account_id"]) && isset($_POST["withdraw"])) 
    {
        $withdraw = (int)se($_POST, "withdraw", "", false);
        $aid = se($_POST, "account_id", "", false);
        $memo = $_POST["memo"];
        if (!($withdraw > 0))
        {
            flash("Input a value to withdraw", "warning");
        }
        elseif ($withdraw > get_account_balance($aid))
        {
            flash("Insufficient Funds", "warning");
        }
        else
        {
            change_balance($withdraw, "withdraw",$aid, $aid, -1, $memo);
            refresh_account_balance($aid);
            flash("Withdraw was successful", "success");
        }
    }
    else
        flash("Account Not Selected", "warning");
?>

<div class="container-fluid">
    <h2>Withdraw</h2>
    <div>
        <form method="POST">
            <div class="mb-3">
                <label for="accountList" class="form-label">Choose an Account to Withdraw Money To</label>
                <select class="form-select" name="account_id" id="accountList" autocomplete="off">
                <?php if (!empty($accounts)) : ?>
                    <?php foreach ($accounts as $account) : ?>
                        <option value="<?php se($account, 'id'); ?>">
                            <?php se($account, "account_number"); ?> (Type: <?php se($account, 'account_type'); ?>; Balance = $<?php se($account, "balance"); ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?> 
            </div>
            <div class="mb-3">
                <label class="form-label" for="d">Amount to Withdraw</label>
                <input class="form-control" type="number" name="withdraw" id="d"></input>
            </div>
            <div class="mb-3">
                <label class="form-label" for="m">Memo</label>
                <input class="form-control" type="text" placeholder="Withdraw" aria-label="default input example" name="memo">
            </div>
            <input type="submit" value="Withdraw" />
        </form>
    </div>
</div>

<?php
    require_once(__DIR__ . "/../../partials/flash.php");
?>