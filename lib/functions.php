<?php
//TODO 1: require db.php
require_once(__DIR__ . "/db.php");

/** Safe Echo Function
 * Takes in a value and passes it through htmlspecialchars()
 * or
 * Takes an array, a key, and default value and will return the value from the array if the key exists or the default value.
 * Can pass a flag to determine if the value will immediately echo or just return so it can be set to a variable
 */
function se($v, $k = null, $default = "", $isEcho = true) {
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
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
function is_logged_in() 
{
    return isset($_SESSION["user"]); //se($_SESSION, "user", false, false);
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
?>