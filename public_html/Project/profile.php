<?php
require_once(__DIR__ . "/../../partials/nav.php");
/*
if (!is_logged_in()) {
    redirect("login.php");
}*/

/**
 * Logic:
 * Check if query params have an id
 * If so, use that id
 * Else check logged in user id
 * otherwise redirect away
 */
$user_id = se($_GET, "id", get_user_id(), false);
error_log("user id $user_id");
$isMe = $user_id === get_user_id();
//!! makes the value into a true or false value regardless of the data https://stackoverflow.com/a/2127324
$edit = !!se($_GET, "edit", false, false); //if key is present allow edit, otherwise no edit
if ($user_id < 1) {
    flash("Invalid user", "danger");
    redirect("home.php");
    //die(header("Location: home.php"));
}
?>
<?php
//only allow profile updating if profile belongs to the user visiting this page and it's in edit mode
if (isset($_POST["save"]) && $isMe && $edit) {
    $email = se($_POST, "email", null, false);
    $username = se($_POST, "username", null, false);
    $firstname = se($_POST, "firstname", null, false);
    $lastname = se($_POST, "lastname", null, false);
    $visibility = !!se($_POST, "visibility", false, false) ? 1 : 0;

    $params = [":email" => $email, ":username" => $username, "firstname" => $firstname, "lastname" => $lastname, ":vis" => $visibility, ":id" => get_user_id()];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users set email = :email, username = :username, firstname = :firstname, lastname = :lastname, visibility = :vis where id = :id");
    try {
        $stmt->execute($params);
    } catch (Exception $e) {
        if ($e->errorInfo[1] === 1062) {
            //https://www.php.net/manual/en/function.preg-match.php
            preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
            if (isset($matches[1])) {
                flash("The chosen " . $matches[1] . " is not available.", "warning");
            } else {
                //TODO come up with a nice error message
                //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
                flash("An unexpected error occurred, please try again" . var_export($e->errorInfo, true), "danger");
            }
        } else {
            //TODO come up with a nice error message
            //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            flash("An unexpected error occurred, please try again" . var_export($e->errorInfo, true), "danger");
        }
    }
    //select fresh data from table
    $stmt = $db->prepare("SELECT id, email, IFNULL(username, email) as `username`, firstname, lastname, visibility from Users where id = :id LIMIT 1");
    try {
        $stmt->execute([":id" => get_user_id()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            //$_SESSION["user"] = $user;
            $_SESSION["user"]["email"] = $user["email"];
            $_SESSION["user"]["username"] = $user["username"];
            $_SESSION["user"]["firstname"] = $user["firstname"];
            $_SESSION["user"]["lastname"] = $user["lastname"];
            $_SESSION["user"]["visibility"] = $user["visibility"];

        } else {
            flash("User doesn't exist", "danger");
        }
    } catch (Exception $e) {
        flash("An unexpected error occurred, please try again" . var_export($e->errorInfo, true), "danger");
        //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
    }


    //check/update password
    $current_password = se($_POST, "currentPassword", null, false);
    $new_password = se($_POST, "newPassword", null, false);
    $confirm_password = se($_POST, "confirmPassword", null, false);
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            //TODO validate current
            $stmt = $db->prepare("SELECT password from Users where id = :id");
            try {
                $stmt->execute([":id" => get_user_id()]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($result["password"])) {
                    if (password_verify($current_password, $result["password"])) {
                        $query = "UPDATE Users set password = :password where id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->execute([
                            ":id" => get_user_id(),
                            ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                        ]);

                        flash("Password reset", "success");
                    } else {
                        flash("Current password is invalid", "warning");
                    }
                }
            } catch (Exception $e) {
                //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
                flash("An unexpected error occurred, please try again" . var_export($e->errorInfo, true), "danger");
            }
        } else {
            flash("New passwords don't match", "warning");
        }
    }
}
?>

<?php
$email = get_user_email();
$username = get_username();
$firstname = get_user_firstname();
$lastname = get_user_lastname();
$visibility = get_user_visibilty();
?>

<div class="container-fluid">
    <h1>Profile</h1>
    <div class="mb-3">
    <?php if ($isMe) : ?>
        <?php if ($edit) : ?>
            <a class="btn btn-outline-secondary" href="?">View</a>
        <?php else : ?>
            <a class="btn  btn-outline-secondary" href="?edit=true">Edit</a>
        <?php endif; ?>
    <?php endif; ?>
    </div>

    <!-- show public info -->
    <?php if (!$edit) : ?>
        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" type="text" name="username" id="username" value="<?php se($username); ?>" readonly/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="firstname">First Name</label>
            <input class="form-control" type="text" name="firstname" id="firstname" value="<?php se($firstname); ?>" readonly/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="lastname">Last Name</label>
            <input class="form-control" type="text" name="lastname" id="lastname" value="<?php se($lastname); ?>" readonly/>
        </div>
    <?php endif; ?>
    
    <?php if ($isMe && $edit) : ?>
    <form method="POST" onsubmit="return validate(this);">
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" type="email" name="email" id="email" value="<?php se($email); ?>" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" type="text" name="username" id="username" value="<?php se($username); ?>" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="firstname">First Name</label>
            <input class="form-control" type="text" name="firstname" id="firstname" value="<?php se($firstname); ?>" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="lastname">Last Name</label>
            <input class="form-control" type="text" name="lastname" id="lastname" value="<?php se($lastname); ?>" />
        </div>
        <div class="mb-3">
            <div class="form-check form-switch">
                <input name="visibility" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" <?php if ($visibility) echo "checked"; ?> autocomplete="off">
                <label class="form-check-label" for="flexSwitchCheckDefault">Make Profile Public</label>
            </div>
        </div>
        <!-- DO NOT PRELOAD PASSWORD -->
        <div class="mb-3"><h5>Password Reset</h5></div>
        <div class="mb-3">
            <label class="form-label" for="cp">Current Password</label>
            <input class="form-control" type="password" name="currentPassword" id="cp" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="np">New Password</label>
            <input class="form-control" type="password" name="newPassword" id="np" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="conp">Confirm Password</label>
            <input class="form-control" type="password" name="confirmPassword" id="conp" />
        </div>
        <input type="submit" value="Update Profile" name="save" />
    </form>
    <?php endif; ?>
</div>

<script>
    function validate(form) {
        let pw = form.newPassword.value;
        let con = form.confirmPassword.value;
        let isValid = true;
        //TODO add other client side validation....

        //example of using flash via javascript
        //find the flash container, create a new element, appendChild
        if (pw !== con) {
            //find the container
            let flash = document.getElementById("flash");
            //create a div (or whatever wrapper we want)
            let outerDiv = document.createElement("div");
            outerDiv.className = "row justify-content-center";
            let innerDiv = document.createElement("div");

            //apply the CSS (these are bootstrap classes which we'll learn later)
            innerDiv.className = "alert alert-warning";
            //set the content
            innerDiv.innerText = "Password and Confirm password must match";

            outerDiv.appendChild(innerDiv);
            //add the element to the DOM (if we don't it merely exists in memory)
            flash.appendChild(outerDiv);
            isValid = false;
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>