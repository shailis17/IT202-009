<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

$firstname = se($_POST, "firstname", "", false);
$lastname = se($_POST, "lastname", "", false);
$an = se($_POST, "an", "", false);

if(isset($_POST['search']) && (!empty($_POST['firstname']) || !empty($_POST['lastname']) || !empty($_POST['an'])))
{
    //set up search variables
    $firstname = se($_POST, "firstname", "", false);
    $lastname = se($_POST, "lastname", "", false);
    $an = se($_POST, "an", "", false);

    //build query
    $query = "SELECT Accounts.id, Accounts.account_number, Accounts.account_type, Accounts.created, Accounts.balance, Users.firstname, Users.lastname FROM Accounts INNER JOIN Users ON Accounts.user_id = Users.id WHERE Accounts.active = 1";
    
    if ($firstname) 
    {
        $query .= " AND Users.firstname = :firstname ";
        $params[":firstname"] = $firstname;
    }
    if ($lastname) 
    {
        $query .= " AND Users.lastname = :lastname ";
        $params[":lastname"] = $lastname;
    }
    if ($an) 
    {
        $query .= " AND Accounts.account_number LIKE :an ";
        $params[":an"] = "%$an%";
    }

    //search database
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
}
else
{
    flash("Need information to search", "warning");
}

if(isset($_POST['freeze']) && isset($_POST['freeze_aid']))
{
    $f_aid = (int)se($_POST, "freeze_aid", "", false);
    $q = "UPDATE Accounts set active = 0 where id = :f_aid";
    $db = getDB();
    $stmt = $db->prepare($q);
    try {
        $stmt->execute([":f_aid" => $f_aid]);
    } catch (PDOException $e) {
        flash("Error closing account: " . var_export($e->errorInfo, true), "danger");
    }

    flash("Successfully froze account, you may refresh/navigate away from the page", "success");

}

//paginate transaction history:
if (isset($_REQUEST["account_id"]))
{
    $src_id = (int)se($_REQUEST, "account_id", "", false);
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
    $params =  [":src_id" => $src_id];

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
        $params[":endDate"] = date("Y-m-d 23:59:59", strtotime($endDate));
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
}

//print loan positive
function loanBalance($balance)
{
    echo((int)$balance*-1);
}

?>

<div class="container-fluid">
    <h2>View User Accounts</h2>
    <div>
        <h4>Search by:</h4>
        <form method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text" id="firstname">First Name</span>
                <input type="text" name="firstname" class="form-control" aria-label="User's First Name" aria-describedby="firstname" value="<?php se($firstname); ?>">
                
                <span class="input-group-text" id="lastname">Last Name</span>
                <input type="text" name="lastname" class="form-control" aria-label="User's Last Name" aria-describedby="lastname" value="<?php se($lastname); ?>">

                <span class="input-group-text" id="an">Account Number</span>
                <input type="text" name="an" class="form-control" aria-label="Account Number" aria-describedby="an" value="<?php se($an); ?>">
            </div>
            <input type="submit" name="search" value="Filter" />
        </form>
    </div>
</div>

<?php if (isset($_POST["search"])) : ?>
<!-- list user accounts --> 
<div class="container-fluid">
    <h2>User Accounts</h2>
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
                        <?php if(se($account, "account_type", "", false) == "loan") : ?>
                            <td><?php loanBalance(se($account, "balance", "", false)); ?>
                        <?php else : ?>
                            <td><?php se($account, "balance"); ?></td>
                        <?php endif; ?>
                        <td>
                            <form method="POST" action="?account_id=<?php se($account, 'id');?>">
                                <input type="hidden" name="account_id" value="<?php se($account, 'id'); ?>" />
                                <input type="hidden" name="account_number" value="<?php se($account, 'account_number'); ?>" />
                                <input type="hidden" name="type" value="<?php se($account, 'account_type'); ?>" />
                                <input type="hidden" name="balance" value="<?php se($account, 'balance'); ?>" />
                                <input type="hidden" name="created" value="<?php se($account, 'created'); ?>" />
                                <input type="hidden" name="apy" value="<?php se($account, 'apy'); ?>" />

                                <input type="submit" name="history" value="More Info" />
                            </form>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to close this account?');">
                                <input type="hidden" name="freeze_aid" value="<?php se($account, 'id'); ?>" />
                                <input type="submit" name="freeze" value="Freeze Account" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- account information and transaction history --> 
<?php if (isset($_POST["history"])) : ?>
<div class="container-fluid">
    <?php if (isset($_REQUEST["account_id"])) : ?>
        <h3>Account Information</h3>
        <table class="table">
            <thead>
                <th>Account Number</th>
                <th>Account Type</th>
                <?php if (se($_POST, 'apy',"", false) > 0) : ?>
                    <th>APY</th>
                <?php endif ?>
                <th>Balance</th>
                <th>Opened</th>
            </thead>
            <tr>
                <td><?php se($_POST, "account_number"); ?></td>
                <td><?php se($_POST, "type"); ?></td>
                <?php if (se($_POST, 'apy',"", false) > 0) : ?>
                    <td><?php se($_POST, "apy"); ?></td>
                <?php endif ?>
                
                <?php if(se($_POST, "type", "", false) == "loan") : ?>
                    <td><?php loanBalance(se($_POST, 'balance',"", false)); ?>
                <?php else : ?>
                    <td><?php se($_POST, 'balance'); ?></td>
                <?php endif; ?>
                
                <td><?php se($_POST, "created"); ?></td>
            </tr>
        </table>
        <h4>Transaction History</h4>
        <div>
            <form>
                <input type = hidden name = account_id value = <?php se($_REQUEST, "account_id"); ?>>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="start-date">Start</span>
                        <input name="start" type="date" class="form-control" placeholder="mm/dd/yyyy" aria-label="start date" aria-describedby="start-date">
                    <span class="input-group-text" id="end-date">End</span>
                        <input name="end" type="date" class="form-control" placeholder="mm/dd/yyyy" aria-label="end date" aria-describedby="end-date">
                    <span class="input-group-text" id="filter">Transaction Type</span>
                    <select class="form-control" name="type">
                        <option value="deposit">Deposit</option>
                        <option value="withdraw">Withdraw</option>
                        <option value="transfer">Transfer</option>
                        <option value="ext-transfer">External Transfer</option>
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
        <?php include(__DIR__ . "/../../../partials/pagination.php"); ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php
    require_once(__DIR__ . "/../../../partials/flash.php");
?>