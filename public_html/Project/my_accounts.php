<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

//handle the toggle first so select pulls fresh data
$uid = get_user_id();
$query = "SELECT account_number, account_type, balance from Accounts ";
$params = null;
if (isset($_POST["user_id"])) {
    $search = se($_POST, "user_id", "", false);
    $query .= " WHERE user_id LIKE :uid";
    $params =  [":user_id" => "%$search%"];
}

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
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
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
                                <input type="submit" value="More Info" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>