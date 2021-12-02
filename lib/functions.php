<?php
//TODO 1: require db.php
require_once(__DIR__ . "/db.php");

/** Safe Echo Function
 * Takes in a value and passes it through htmlspecialchars()
 * or
 * Takes an array, a key, and default value and will return the value from the array if the key exists or the default value.
 * Can pass a flag to determine if the value will immediately echo or just return so it can be set to a variable
 */

$BASE_PATH = '/Project/'; //This is going to be a helper for redirecting to our base project path since it's nested in another folder

function se($v, $k = null, $default = "", $isEcho = true) 
{
    if (is_array($v) && isset($k) && isset($v[$k])) 
    {
        $returnValue = $v[$k];
    } 
    else if (is_object($v) && isset($k) && isset($v->$k)) 
    {
        $returnValue = $v->$k;
    } 
    else 
    {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) 
        {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) 
    {
        $returnValue = $default;
    }
    
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}

//TODO 2: filter helpers
function sanitize_email($email = "") 
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "") 
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}

//TODO 3: User helpers
//function is_logged_in() 
function is_logged_in($redirect = false, $destination = "login.php")
{
    //return isset($_SESSION["user"]); //<== se($_SESSION, "user", false, false);
    $isLoggedIn = isset($_SESSION["user"]);
    if ($redirect && !$isLoggedIn) {
        flash("You must be logged in to view this page", "warning");
        die(header("Location: $destination"));
    }
    return $isLoggedIn; //se($_SESSION, "user", false, false);
}
function has_role($role) 
{
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) 
    {
        foreach ($_SESSION["user"]["roles"] as $r) 
        {
            if ($r["name"] === $role) 
            {
                return true;
            }
        }
    }
    return false;
}
function get_username() 
{
    if (is_logged_in()) 
    { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "username", "", false);
    }
    return "";
}
function get_user_email() 
{
    if (is_logged_in()) 
    { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "email", "", false);
    }
    return "";
}
function get_user_id() 
{
    if (is_logged_in()) 
    { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "id", false, false);
    }
    return false;
}

//TODO 4: Flash Message Helpers
function flash($msg = "", $color = "info")
{
    $message = ["text" => $msg, "color" => $color];
    if (isset($_SESSION['flash'])) 
    {
        array_push($_SESSION['flash'], $message);
    } 
    else 
    {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}

function getMessages()
{
    if (isset($_SESSION['flash'])) 
    {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

//TODO generic helpers
function reset_session()
{
    session_unset();
    session_destroy();
    session_start();
}
function users_check_duplicate($errorInfo)
{
    if ($errorInfo[1] === 1062) {
        //https://www.php.net/manual/en/function.preg-match.php
        preg_match("/Users.(\w+)/", $errorInfo[2], $matches);
        if (isset($matches[1])) {
            flash("The chosen " . $matches[1] . " is not available.", "warning");
        } else {
            //TODO come up with a nice error message
            flash("<pre>" . var_export($errorInfo, true) . "</pre>");
        }
    } else {
        //TODO come up with a nice error message
        flash("<pre>" . var_export($errorInfo, true) . "</pre>");
    }
}

function get_url($dest)
{
    global $BASE_PATH;
    if (str_starts_with($dest, "/")) {
        //handle absolute path
        return $dest;
    }
    //handle relative path
    return $BASE_PATH . $dest;
}

//transactions and account management helper functions
function get_user_account_id()
{
    if (is_logged_in() && isset($_SESSION["user"]["account"])) {
        return se($_SESSION["user"]["account"], "id", 0, false);
    }
    return 0;
}

function get_account_balance($aid)
{
    $query = "SELECT balance, id from Accounts ";
    $params = null;

    $query .= " WHERE id = :aid";
    $params =  [":aid" => "$aid"];

    $query .= " ORDER BY created desc";
    $db = getDB();

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

    $account = $accounts[0];
    $balance = (int)se($account, "balance","", false);
    return $balance;
}

function get_world_id($type = "world")
{
    $query = "SELECT account_type, id from Accounts ";
    $params = null;

    $query .= " WHERE account_type = :type";
    $params =  [":type" => "$type"];

    $query .= " ORDER BY created desc";
    $db = getDB();

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

    $account = $accounts[0];
    $world_id = (int)se($account, "id", "", false);
    return $world_id;
}

function refresh_account_balance($src_id)
{
    
    //cache account balance via Transaction_History history
    $query = "UPDATE Accounts set balance = (SELECT IFNULL(SUM(balanceChange), 0) from Transaction_History WHERE src = :src) where id = :src";
    $db = getDB();
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":src" => $src_id]);
    } catch (PDOException $e) {
        flash("Error refreshing account: " . var_export($e->errorInfo, true), "danger");
    }
}

/**
 * balanceChange should be passed as a positive value.
 * $src should be where the balanceChange is coming from
 * $dest should be where the balanceChange is going
 */
function change_balance($balanceChange, $transactionType, $aid, $src = -1, $dest = -1, $memo = "")
{
    //I'm choosing to ignore the record of 0 point transactions
    if ($balanceChange > 0) 
    {
        $query = "INSERT INTO Transaction_History (src, dest, balanceChange, transactionType, memo) 
            VALUES (:acs, :acd, :pc, :r,:m), 
            (:acs2, :acd2, :pc2, :r, :m)";
        //insert both records at once, note the placeholders kept the same and the ones changed.
        $params[":acs"] = $src;
        $params[":acd"] = $dest;
        $params[":r"] = $transactionType;
        $params[":m"] = $memo;
        $params[":pc"] = ($balanceChange * -1); //src account is giving away money

        $params[":acs2"] = $dest;
        $params[":acd2"] = $src;
        $params[":pc2"] = $balanceChange;   //dest account is recieving money
        $db = getDB();
        $stmt = $db->prepare($query);
        try 
        {
            $stmt->execute($params);
            //Only refresh the balance of the user if the logged in user's account is part of the transfer
            //this is needed so future features don't waste time/resources or potentially cause an error when a calculation
            //occurs without a logged in user
            if ($src == $aid|| $dest == $aid) 
            {
                refresh_account_balance($aid);
            }
        } 
        catch (PDOException $e) 
        {
            flash("Transfer error occurred: " . var_export($e->errorInfo, true), "danger");
        }
    }
}
?>

?>