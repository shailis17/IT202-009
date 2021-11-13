<?php
require(__DIR__ . "/../../partials/nav.php");
reset_session();
$email = se($_POST, "email", "", false);
$username = se($_POST, "username", "", false);
?>

<div class="container-fluid">
    <h2>Register</h2>
    <form onsubmit="return validate(this)" method="POST">
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" type="email" name="email" required value="<?php se($email); ?>"/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" type="text" name="username" required maxlength="30" value="<?php se($username); ?>"/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="pw">Password</label>
            <input class="form-control" type="password" id="pw" name="password" required minlength="8" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="confirm">Confirm</label>
            <input class="form-control" type="password" name="confirm" required minlength="8" />
        </div>
        <input type="submit" value="Register" />
    </form>
</div>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php
 //TODO 2: add PHP Code
 if(isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"]))
 {
    //get the key from $_POST, default to "" if not set, and return the value
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);
    //TODO 3: validate/use
    //$errors = [];
    $hasErrors = false;
    if(empty($email))
    {
       //array_push($errors, "Email must be set");
        flash("Email must be set", "danger");
        $hasErrors = true;
    }
    //sanitize
    //$email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = sanitize_email($email);
    //validate
    //if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    if(!is_valid_email($email))
    {
        // array_push($errors, "Invalid email address");
        flash("Invalid email address", "danger");
        $hasErrors = true;
    }
    if(!preg_match('/^[a-z0-9_-]{3,30}$/i', $username))
    {
        flash("Invalid username, must be alphanumeric and can only contain - and/or _", "danger");
        $hasErrors = true;
    }
    if(empty($password))
    {
       //array_push($errors, "Password must be set");
       flash("Password must be set", "danger");
       $hasErrors = true;
    }
    if(empty($confirm))
    {
        //array_push($errors, "Confirm password must be set");
        flash("Confirm password must be set", "danger");
        $hasErrors = true;
    }
    if(strlen($password) < 8)
    {
        //array_push($errors, "Password must be 8 or more characters");
        flash("Password must be 8 or more characters", "danger");
        $hasErrors = true;
    }
    if(strlen($password) > 0 && $password !== $confirm)
    {
        //array_push($errors, "Passwords don't match");
        flash("Passwords don't match", "danger");
        $hasErrors = true;
    }
    
    //if(count($errors) > 0)
    //    echo "<pre>" . var_export($errors, true) . "</pre>";
    if($hasErrors)
    {
        //flash("<pre>" . var_export($errors, true) . "</pre>");
        flash("An unexpected error occurred, please try again", "danger");
    }
    else
    {
        //echo "Welcome, $email!";
        //flash("Welcome, $email");
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users(email, password, username) VALUES (:email, :password, :username)");
        try
        {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            //echo "You've been registered!";
            flash("You've been registered!", "success");
        }
        catch (Exception $e)
        {
            //echo "There was a problem registering";
            //flash("There was a problem registering");
            //echo "<pre>" . var_export($e, true) . "</pre>";
            //flash("<pre>" . var_export($e, true) . "</pre>");
            users_check_duplicate($e->errorInfo);
        }
    }
 }
?>

<?php
require(__DIR__ . "/../../partials/flash.php");
?>