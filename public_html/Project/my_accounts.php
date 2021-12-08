<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

$uid = get_user_id();
$query = "SELECT account_number, account_type, balance, created, id from Accounts ";
$params = null;

$query .= " WHERE user_id = :uid";
$params =  [":uid" => "$uid"];

$query .= " ORDER BY created desc LIMIT 5";
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

if (isset($_POST["account_id"]))
{
    $src_id = (int)se($_POST, "account_id", "", false);
    //Sorts & Filters
    $startDate = se($_GET, "start", date("Y-m-d", strtotime("-1 month")), false);
    $endDate = se($_GET, "end", date("Y-m-d"), false);
    $type = se($_GET, "type", false, false);
    $orderby = se($_GET, "orderby", false, false);
    
    //split query into data and total
    $base_query = "SELECT src, dest, transactionType, balanceChange, memo, created from Transaction_History ";
    $total_query = "SELECT count(1) as total FROM Transaction_History ";
    //dynamic query
    $query = " WHERE 1=1"; //1=1 shortcut to conditionally build AND clauses
    $params = []; //define default params, add keys as needed and pass to execute
    //apply src filter
    $query .= " AND src = :src_id ";
    $params =  [":src_id" => "$src_id"];

    //apply start-end date filter
    if ($startDate) 
    {
        $query .= " AND created >= :startDate ";
        $params[":startDate"] = $startDate;
    }
    if ($endDate) 
    {
        $query .= " AND created <= :endDate ";
        //offset the time to be 1 minute before end of day
        //by default the time component is 00:00:00 which is the beginning if this day
        //$params[":end"] = $end;
        $params[":end"] = date("Y-m-d 23:59:59", strtotime($endDate));
    }

    //apply type filter
    if (!empty($type)) {
        $query .= " AND transactionType = :type ";
        $params[":type"] = "$type";
    }
    //apply column and order sort
    if (!empty($orderby))
    {
        $query .= " ORDER BY created $orderby ";
    }
    else
    {
        $query .= " ORDER BY created desc ";
    }
    //paginate function
    $per_page = 10;
    paginate($total_query . $query, $params, $per_page);
    $query .= " LIMIT :offset, :count";
    $params[":offset"] = $offset;
    $params[":count"] = $per_page;

    $db = getDB();
    $transactions = [];

    //get the records
    $stmt = $db->prepare($base_query . $query); //dynamically generated query
    //we'll want to convert this to use bindValue so ensure they're integers so lets map our array
    foreach ($params as $key => $value) {
        $t = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $value, $t);
    }
    $params = null; //set it to null to avoid issues
    
    try {
        $stmt->execute($params); //dynamically populated params to bind
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $transactions = $r;
        }
        else {
            flash("No transactions found", "warning");
        }
    } 
    catch (PDOException $e) {
        flash(var_export($e->errorInfo, true), "danger");
    }
    
    //SHOWS 10 LATEST TRANSACTIONS ORDERED BY CREATED NEW TO OLD ==> per Milestone2
    /*
    $query = "SELECT src, dest, transactionType, balanceChange, memo, created from Transaction_History ";
    $params = null;

    $query .= " WHERE src = :src_id";
    $params =  [":src_id" => "$src_id"];

    $query .= " ORDER BY created desc LIMIT 10";
    $db = getDB();
    $stmt = $db->prepare($query);
    global $transactions; $transactions = [];

    try {
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($results) {
            $transactions = $results;
        } else {
            flash("No transactions found", "warning");
        }
    } catch (PDOException $e) {
        flash(var_export($e->errorInfo, true), "danger");
    }
    */
}
?>

<div class="container-fluid">
    <h2>My Accounts</h2>
    <table class="table">
        <thead>
            <th>Account Number</th>
            <th>Account Type</th>
            <th>Balance</th>
        </thead>
        <tbody>
            <?php if (empty($accounts)) : ?>
                <tr>
                    <td colspan="100%">No accounts</td>
                </tr>
            <?php else : ?>
                <?php foreach ($accounts as $account) : ?>
                    <tr>
                        <td><?php se($account, "account_number"); ?></td>
                        <td><?php se($account, "account_type"); ?></td>
                        <td><?php se($account, "balance"); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="account_id" value="<?php se($account, 'id'); ?>" />
                                <input type="hidden" name="account_number" value="<?php se($account, 'account_number'); ?>" />
                                <input type="hidden" name="type" value="<?php se($account, 'account_type'); ?>" />
                                <input type="hidden" name="balance" value="<?php se($account, 'balance'); ?>" />
                                <input type="hidden" name="created" value="<?php se($account, 'created'); ?>" />

                                <input type="submit" value="More Info" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="container-fluid">
    <?php if (isset($_POST["account_id"])) : ?>
        <h3>Account Information</h3>
        <table class="table">
            <thead>
                <th>Account Number</th>
                <th>Account Type</th>
                <th>Balance</th>
                <th>Opened</th>
            </thead>
            <tr>
                <td><?php se($_POST, "account_number"); ?></td>
                <td><?php se($_POST, "type"); ?></td>
                <td><?php se($_POST, "balance"); ?></td>
                <td><?php se($_POST, "created"); ?></td>
            </tr>
        </table>
        <h4>Transaction History</h4>
        <div>
            <form>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="start-date">Start</span>
                        <input name="start" type="date" class="form-control" placeholder="mm/dd/yyyy" aria-label="start date" aria-describedby="start-date" value="<?php se($startDate); ?>">
                    <span class="input-group-text" id="end-date">End</span>
                        <input name="end" type="date" class="form-control" placeholder="mm/dd/yyyy" aria-label="end date" aria-describedby="end-date" value="<?php se($endDate); ?>">
                    <span class="input-group-text" id="filter">Transaction Type</span>
                    <select class="form-control" name="type" value="<?php se($type); ?>">
                        <option value="deposit">Deposit</option>
                        <option value="withdraw">Withdraw</option>
                        <option value="transfer">Transfer</option>
                    </select>
                    <span class="input-group-text" id="sort">Sort</span>
                    <select class="form-control" name="sort" aria-label="sort" aria-describedby="sort">
                        <option value="desc">Created New to Old</option>
                        <option value="asc">Created Old to New</option>
                    </select>
                </div>
                <input type="submit" value="Filter" />
            </form>
        </div>
        <table class="table">
            <thead>
                <th>Src</th>
                <th>Dest</th>
                <th>Transaction Type</th>
                <th>Balance Change</th>
                <th>Memo</th>
                <th>Date & Time</th>
            </thead>

            <?php if (empty($transactions)) : ?>
                <tr>
                    <td colspan="100%">No transactions</td>
                </tr>
            <?php else : ?>
                <?php foreach ($transactions as $transaction) : ?>
                    <tr>
                        <td><?php se($transaction, "src"); ?></td>
                        <td><?php se($transaction, "dest"); ?></td>
                        <td><?php se($transaction, "transactionType"); ?></td>
                        <td><?php se($transaction, "balanceChange"); ?></td>
                        <td><?php se($transaction, "memo"); ?></td>
                        <td><?php se($transaction, "created"); ?></td>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>