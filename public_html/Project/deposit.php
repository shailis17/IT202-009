<?php
    require_once(__DIR__ . "/../../partials/nav.php");
    if (!is_logged_in()) {
        die(header("Location: login.php"));
    }

    $uid = get_user_id();
    $query = "SELECT account_number, account_type, balance, created from Accounts ";
    $params = null;
    
    $query .= " WHERE user_id = :uid";
    $params =  [":uid" => "$uid"];
    
    $query .= " ORDER BY created desc";
    $db = getDB();
    $stmt = $db->prepare($query);
    $accounts = [];
    try {
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($results) {
            $accounts = $results;
        } else {
            flash("No accounts found", "warning");
        }
    } catch (PDOException $e) {
        flash(var_export($e->errorInfo, true), "danger");
    }

    if (isset($_POST["account_id"]) && isset($_POST["deposit"])) 
    {
        $deposit = (int)se($_POST, "deposit", "", false);
        $aid = (int)se($_POST, "account_id", "", false)+1;
        $memo = $_POST["memo"];
        if (!($deposit > 0))
        {
            flash("Input a value to deposit", "warning");
        }
        else
        {
            change_balance($deposit, "deposit", -1, $aid, $memo);
            refresh_account_balance();
            flash("Deposit was successful", "success");
        }
    }
    else
        flash("Account Not Selected", "warning");
?>

<div class="container-fluid">
    <h2>Deposit</h2>
    <div>
        <form method="POST">
            <div class="mb-3">
                <label for="accountList" class="form-label">Choose an Account to Deposit Money To</label>
                <input class="form-select" list="accountListOptions" id="accountList" placeholder="Type to search...">
                <?php if (!empty($accounts)) : ?>
                    <datalist id="accountListOptions">
                        <?php foreach ($accounts as $account) : ?>
                            <option value= "<?php se($account, "account_number"); ?> (Type: <?php se($account, 'account_type'); ?>; Balance = $<?php se($account, "balance"); ?>)">
                            <input type="hidden" name="account_id" value="<?php se($account, 'id'); ?>" />
                        <?php endforeach; ?>
                    </datalist>
                <?php endif; ?> 
            </div>
            <div class="mb-3">
                <label class="form-label" for="d">Amount to Deposit</label>
                <input class="form-control" type="number" name="deposit" id="d"></input>
            </div>
            <div class="mb-3">
                <label class="form-label" for="m">Memo</label>
                <input class="form-control" type="text" placeholder="Deposit" aria-label="default input example" name="memo">
            </div>
            <input type="submit" value="Deposit" />
        </form>
    </div>
</div>

<?php
    require_once(__DIR__ . "/../../partials/flash.php");
?>