<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

$firstname = se($_POST, "firstname", "", false);
$lastname = se($_POST, "lastname", "", false);

if(isset($_POST['search']) && (!empty($_POST['firstname']) || !empty($_POST['lastname']) || !empty($_POST['an'])))
{
    //set up search variables
    $firstname = se($_POST, "firstname", "", false);
    $lastname = se($_POST, "lastname", "", false);

    //build query
    $query = "SELECT id, firstname, lastname, username, email from Users Where active = 1";

    if ($firstname) 
    {
        $query .= " AND firstname = :firstname ";
        $params[":firstname"] = $firstname;
    }
    if ($lastname) 
    {
        $query .= " AND lastname = :lastname ";
        $params[":lastname"] = $lastname;
    }

    //search database
    $db = getDB();
    $stmt = $db->prepare($query);
    $users = [];
    try {
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($results) {
            $users = $results;
        } else {
            flash("No users found", "warning");
        }
    } catch (PDOException $e) {
        flash(var_export($e->errorInfo, true), "danger");
    }
}
else
{
    flash("Need information to search", "warning");
}

//open an account
if (isset($_POST["a_type"]) && isset($_POST["deposit"])) 
{
    $type = se($_POST, "a_type", "", false);
    $apy = getAPY($type);

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
            $uid = (int)se($_POST, "uid", "", false); //get user id from POST
            //flash("uid = $uid");
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
        //redirect("admin/view_accounts.php");
    }
}
else
    flash("Account type must be selected", "warning");

//deactivate user
if(isset($_POST['deactivate']) && isset($_POST['de_uid']))
{
    $uid = (int)se($_POST, "de_uid", "", false);
    $q = "UPDATE Users set active = 0 where id = :de_uid";
    $db = getDB();
    $stmt = $db->prepare($q);
    try {
        $stmt->execute([":de_uid" => $uid]);
    } catch (PDOException $e) {
        flash("Error closing account: " . var_export($e->errorInfo, true), "danger");
    }

    flash("Successfully deactivated account, you may refresh/navigate away from the page", "success");

}


?>

<div class="container-fluid">
    <h2>Manage Users</h2>
    <div>
        <h4>Search by:</h4>
        <form method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text" id="firstname">First Name</span>
                <input type="text" name="firstname" class="form-control" aria-label="User's First Name" aria-describedby="firstname" value="<?php se($firstname); ?>">
                
                <span class="input-group-text" id="lastname">Last Name</span>
                <input type="text" name="lastname" class="form-control" aria-label="User's Last Name" aria-describedby="lastname" value="<?php se($lastname); ?>">
            </div>
            <input type="submit" name="search" value="Filter" />
        </form>
    </div>
</div>

<?php if (isset($_POST["search"])) : ?>
<!-- list users --> 
<div class="container-fluid">
    <h2>Users</h2>
    <table class="table">
        <thead>
            <th>First Name</th>
            <th>Last Name</th>
            <th>User Name</th>
            <th>Email</th>
        </thead>
        <tbody>
            <?php if (empty($users)) : ?>
                <tr>
                    <td colspan="100%">No Users</td>
                </tr>
            <?php else : ?>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php se($user, "firstname"); ?></td>
                        <td><?php se($user, "lastname"); ?></td>
                        <td><?php se($user, "username"); ?></td>
                        <td><?php se($user, "email"); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="uid" value="<?php se($user, 'id'); ?>" />

                                <input type="submit" name="open" value="Open An Account" />
                            </form>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to deactivate this user?');">
                                <input type="hidden" name="de_uid" value="<?php se($user, 'id'); ?>" />
                                <input type="submit" name="deactivate" value="Deactivate User" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (isset($_POST["open"])) : ?>
<div class="container-fluid">
    <h3>Open An Account</h3>
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
        <input type="hidden" name="uid" value="<?php se($_POST, 'uid'); ?>" />
        <input type="submit" value="Create Account" />
    </form>
</div>
<?php endif; ?>

<?php
    require_once(__DIR__ . "/../../../partials/flash.php");
?>
