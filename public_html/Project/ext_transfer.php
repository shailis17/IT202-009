<?php
    require(__DIR__ . "/../../partials/nav.php");
    $transfer = (int)se($_POST, "transfer", "", false);
    $src = (int)se($_POST, "src_id", "", false);
    $lastname = se($_POST, "lastname", "", false);
    $lastfour = se($_POST, "lastfour", "", false);

    if (!is_logged_in()) {
        flash("You don't have permission to view this page", "warning");
        redirect("home.php");
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

    if (isset($_POST["src_id"]) && isset($_POST["lastname"]) && isset($_POST["lastfour"]) && isset($_POST["transfer"])) 
    {
        $transfer = (int)se($_POST, "transfer", "", false);
        $src = (int)se($_POST, "src_id", "", false);
        $lastname = se($_POST, "lastname", "", false);
        $lastfour = se($_POST, "lastfour", "", false);
        $dest = get_dest_id($lastname, $lastfour);
        $memo = $_POST["memo"];

        //flash("dest = $dest");
        if($src == $dest)
        {
            flash("Cannot transfer to the same account", "warning");
        }
        elseif(empty($lastname))
        {
            flash("Please enter the last name associated with the account money is being transfered to", "warning");
        }
        elseif(empty($lastfour))
        {
            flash("Please enter the last four digits of the account number", "warning");
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
            change_balance($transfer, "ext-transfer", $src, $src, $dest, $memo);
            refresh_account_balance($src);
            refresh_account_balance($dest);
            flash("Transfer was successful", "success");
            redirect("my_accounts.php");
        }
    }
    else
        flash("Account Not Selected", "warning");



    function get_dest_id($lastname, $lastfour)
    {
        $q = "SELECT Accounts.id, Accounts.account_number, Accounts.user_id, Users.lastname FROM Accounts INNER JOIN Users ON Accounts.user_id = Users.id WHERE Users.lastname LIKE :lastname AND Accounts.account_number LIKE :an ";
        $p = ["lastname" => $lastname, "an" => "%$lastfour"];

        $db = getDB();
        $stmt = $db->prepare($q);
        $results = [];
        try {
            $stmt->execute($p);
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($r) {
                $results = $r;
                //echo var_export($results, true); 
            } else {
                flash("No accounts found", "warning");
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }

        $dest_account = $results[0];
        $dest_id = (int)se($dest_account, "id", "", false);
        return $dest_id;
    }
?>

<div class="container-fluid">
    <h2>External Transfer</h2>
    <div>
        <form method="POST">
            <div class="mb-3">
                <label for="sourceList" class="form-label">Choose an Account to Transfer Money From</label>
                <select class="form-select" name="src_id" id="sourceList" autocomplete="off">
                <?php if (!empty($accounts)) : ?>
                    <?php foreach ($accounts as $account) : ?>
                        <option value="<?php se($account, 'id'); ?>">
                            <?php se($account, "account_number"); ?> (Type: <?php se($account, 'account_type'); ?>; Balance = $<?php se($account, "balance"); ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?> 
                </select>
            </div>
            <div class="mb-3">
                <h6>Information about Account Money is Being Transfered To</h6>
                <label class="form-label" for="m">Last Name</label>
                <input class="form-control" type="text" aria-label="default input example" name="lastname">
            </div>
            <div>
            <label class="form-label" for="m">Last Four Digits of the Account Number</label>
                <input class="form-control" type="text" aria-label="default input example" name="lastfour">
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