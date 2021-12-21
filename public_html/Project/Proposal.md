# Project Name: Simple Bank
## Project Summary: This project will create a bank simulation for users. They’ll be able to have various accounts, do standard bank functions like deposit, withdraw, internal (user’s accounts)/external(other user’s accounts) transfers, and creating/closing 
## Github Link: https://github.com/shailis17/IT202-009/tree/prod
## Project Board Link: https://github.com/shailis17/IT202-009/projects/1
## Website Link: https://sss8-prod.herokuapp.com/Project
## Final Demo Video: https://mediaspace.njit.edu/media/IT202-009+Demo.webex/1_pp11wos1
## Your Name: Shaili Soni

<!--
### Line item / Feature template (use this for each bullet point)
#### Don't delete this

- [ ] (mm/dd/yyyy of completion) Feature Title (from the proposal bullet point, if it's a sub-point indent it properly)
        -  List of Evidence of Feature Completion
            - Status: Pending (Completed, Partially working, Incomplete, Pending)
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
                - PR link #1 (repeat as necessary)
            - Screenshots
                - Screenshot #1 (paste the image so it uploads to github) (repeat as necessary)
                    - Screenshot #1 description explaining what you're trying to show
### End Line item / Feature Template
--> 
### Proposal Checklist and Evidence

- Milestone 1
    - [X] (11/09/2021) User will be able to register a new account
        -  List of Evidence of Feature Completion
            - Status: Complete
            - Direct Link: https://sss8-prod.herokuapp.com/Project/register.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/11
                - PR link #2: https://github.com/shailis17/IT202-009/pull/27
                - PR link #3: https://github.com/shailis17/IT202-009/pull/32 
            - Screenshots/Evidence
                - Screenshot #1 
                    
                    ![image](https://user-images.githubusercontent.com/83250817/140582985-37f7e4ab-bd6b-4198-91c7-4dafc4e36372.png) 
                    - Form Fields
                        - Username, email, password, confirm password (other fields optional)
                        - Email is required and must be validated
                        - Username is required
                        - Confirm password’s match
                - Screenshot #2 & #3
                    ![image](https://user-images.githubusercontent.com/83250817/140988562-3404dfee-9fc6-46c1-b41d-a4c6423353a3.png)

                    ![image](https://user-images.githubusercontent.com/83250817/140989216-afe10000-f77a-4f5b-8462-009208ef6120.png)

                    - System should let user know if username or email is taken and allow the user to correct the error without wiping/clearing the form
                - See Project/sql folder (001 & 002)
                    - Users Table - Id, username, email, password (60 characters), created, modified
                    - Email & Username should be unique
                - Password must be hashed (plain text passwords will lose points)
                    - Code Snippet:
                        - `$hash = password_hash($password, PASSWORD_BCRYPT);
                           $db = getDB();
                           $stmt = $db->prepare("INSERT INTO Users(email, password, username) VALUES (:email, :password, :username)");
                           try
                           {
                               $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
                               //echo "You've been registered!";
                               flash("You've been registered!", "success");
                           }`
                

    - [X] (11/09/2021) User will be able to login to their account (given they enter the correct credentials)
        -  List of Evidence of Feature Completion
            - Status: Complete
            - Direct Link: https://sss8-prod.herokuapp.com/Project/login.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/31
                - PR link #2: https://github.com/shailis17/IT202-009/pull/32  
            - Screenshots
                - Screenshot #1-3
                
                    ![image](https://user-images.githubusercontent.com/83250817/140620060-2d50fbd2-648b-4d07-b59c-cddfcbef92c3.png)

                    ![image](https://user-images.githubusercontent.com/83250817/140620171-2c431c0f-255c-4a6d-9e4d-6dfabd41f7c8.png)

                    ![image](https://user-images.githubusercontent.com/83250817/141016560-028ef19d-4ffb-4c05-8ca6-2b77e234c0c9.png)
                    - User should see friendly error messages when an account either doesn’t exist or if passwords don’t match    
                - Code Snippet:
                    
                    ` $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    //check if we got the user, this returns false if no records matched
                    if($user)
                    {
                    $hash = $user["password"];
                    //remove password from the user object so it doesn't leave the scope (avoids password leakage in code)
                    unset($user["password"]);
                    if(password_verify($password, $hash))
                    {
                       //echo "Welcome, $email";
                       //flash("Welcome, $email");
                       $_SESSION["user"] = $user;
                       //lookup potential roles:
                       $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                        $stmt->execute([":user_id" => $user["id"]]);
                        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        //save roles or empty array
                        if ($roles) 
                        {
                            $_SESSION["user"]["roles"] = $roles; //at least 1 role
                        } 
                        else 
                        {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }
                       die(header("Location: home.php"));
                    }`
                        
                    - Logging in should fetch the user’s details (and roles) and save them into the session
                    - User will be directed to a landing page upon login
                        - This is a protected page (non-logged in users shouldn’t have access)
                        - This can be home, profile, a dashboard, etc

    
    - [X] (11/6/2021) User will be able to logout
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/logout.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/13
                - PR link #2: https://github.com/shailis17/IT202-009/pull/32
            - Screenshots
                - Screenshot #1
                ![image](https://user-images.githubusercontent.com/83250817/140619411-24389397-91af-4932-86b4-19461e7c780e.png)
                    - Logging out will redirect to login page
                    - User should see a message that they’ve successfully logged out
                - Screenshot #2
                ![image](https://user-images.githubusercontent.com/83250817/140619526-c587aed3-5c93-4631-825e-57511fc6ad0b.png)
                    - Session should be destroyed (so the back button doesn’t allow them access back in) ==> this screenshot is what shows after hitting back button from previous screenshot
    
    - [X] (11/6/2021) Basic security rules implemented
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/home.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/28
                - PR link #2: https://github.com/shailis17/IT202-009/pull/32
            - Screenshots/Evidence
                - Code snippet:
                    - `function is_logged_in($redirect = false, $destination = "login.php")
                        {
                            //return isset($_SESSION["user"]); //<== se($_SESSION, "user", false, false);
                            $isLoggedIn = isset($_SESSION["user"]);
                            if ($redirect && !$isLoggedIn) {
                                flash("You must be logged in to view this page", "warning");
                                die(header("Location: $destination"));
                            }
                            return $isLoggedIn; //se($_SESSION, "user", false, false);
                        }`
                        - Authentication: Function to check if user is logged in, called on appropriate pages that only allow logged in users 
                            - ex: home page cannot be seen if logged out
                            ![image](https://user-images.githubusercontent.com/83250817/140619526-c587aed3-5c93-4631-825e-57511fc6ad0b.png)

    - [X] (11/11/2021) Basic Roles implemented
        -  List of Evidence of Feature Completion
            - Status: Complete
            - Direct Links: 
                - https://sss8-prod.herokuapp.com/Project/admin/create_role.php
                - https://sss8-prod.herokuapp.com/Project/admin/assign_roles.php
                - https://sss8-prod.herokuapp.com/Project/admin/list_roles.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/28 
                - PR link #2: https://github.com/shailis17/IT202-009/pull/11 ==> `function has_role($role)`
                - PR link #3: https://github.com/shailis17/IT202-009/pull/35
            - Screenshots/Evidence
                - see Project/sql folder
                    - Roles table	(id, name, description, is_active, modified, created)
                    - User Roles table (id, user_id, role_id, is_active, created, modified)
                - admin restricted pages:
                    ![image](https://user-images.githubusercontent.com/83250817/141375969-dee62b19-8a51-4f8f-beab-c82b0c739bf5.png)

                    ![image](https://user-images.githubusercontent.com/83250817/141376005-4dfd30aa-a03d-43b8-9344-db9221ab1110.png)

                    ![image](https://user-images.githubusercontent.com/83250817/141376037-ecbe80df-c21f-425c-b8ec-1ab215c6631b.png)

                - code snippet from functions.php file
                    
                    `function has_role($role) 
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
                    }`
                    - Include a function to check if a user has a specific role (we won’t use it for this milestone but it should be usable in the future)

    - [X] (11/2/2021) Site should have basic styles/theme applied; everything should be styled
        -  List of Evidence of Feature Completion
            - Status: Completed (bootstrap TBA)
            - Direct Link: https://sss8-prod.herokuapp.com/Project/login.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/30
            - Screenshots/Evidence
                - See any screenshot above/below
                - See styles.css file
    
    - [X] (11/09/2021) Any output messages/errors should be “user friendly”
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/login.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/32
            - Screenshots & Evidence
                - See any screenshot above/below
    
    - [X] (11/09/2021) User will be able to see their profile
        -  List of Evidence of Feature Completion
            - Status: Complete
            - Direct Link: https://sss8-prod.herokuapp.com/Project/profile.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/27 
                - PR link #2: https://github.com/shailis17/IT202-009/pull/32
            - Screenshots
                ![image](https://user-images.githubusercontent.com/83250817/140989707-e301e88f-7bfc-43d2-a734-6ef21ef928af.png)
                    - User is able to see email & username
    
    - [X] (11/09/2021) User will be able to edit their profile
        -  List of Evidence of Feature Completion
            - Status: Complete
            - Direct Link: https://sss8-prod.herokuapp.com/Project/profile.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/27
                - PR link #2: https://github.com/shailis17/IT202-009/pull/32
            - Screenshots
                - Screenshot #1 
                ![image](https://user-images.githubusercontent.com/83250817/140653150-16f7fa6b-827e-4e96-bcba-82212b85ab3e.png)
                    - Allow password reset (only if the existing correct password is provided)
                        - Hint: logic for the password check would be similar to login
                - Screenshot #2 & #3
                    ![image](https://user-images.githubusercontent.com/83250817/141014149-0966939d-ce72-4f3d-b1c9-f0e3e8617ecd.png)

                    ![image](https://user-images.githubusercontent.com/83250817/141014514-05519f04-1daa-400e-85be-e48bfd349d9f.png)
                    - Changing username/email should properly check to see if it’s available before allowing the change

- Milestone 2
    - [X] (11/18/2021) Create the Accounts table
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/my_accounts.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/55
            - Screenshots & Evidence
                - see Project/sql folder
                    - file 006_create_table_accounts
                        - Create the Accounts table ==> id, account_number [unique, always 12 characters], user_id, balance (default 0), account_type, created, modified)

    
    - [X] (11/18/2021) Initial setup scripts in sql folder
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/my_accounts.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/55
            - Screenshots & Evidence
                - see Project/sql folder
                    - file 007_insert_table_users_system
                        - Create a system user if they don’t exist (this will never be logged into, it’s just to keep things working per system requirements)
                    - file 008_insert_table_accounts_world
                        - Create a world account in the Accounts table created below (if it doesn’t exist)
                            - Account_number must be “000000000000”
                            - User_id must be the id of the system user
                            - Account type must be “world”

    - [X] (11/22/2021) Create the Transactions table
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/my_accounts.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/56
            - Screenshots & Evidence
                - see Project/sql folder
                    - file 009_create_table_transactionhistory

    - [X] (11/27/2021) Dashboard page
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/home.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/57
            - Screenshots
                - Screenshot #1 
                
                ![image](https://user-images.githubusercontent.com/83250817/144163573-351b061f-b0cc-401a-82b1-8193e51efbb2.png)

                    - Will have links for Create Account, My Accounts, Deposit, Withdraw Transfer, Profile
                        - Links that don’t have pages yet should just have href=”#”, you’ll update them later


    - [X] (11/29/2021) User will be able to create a checking account
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/create_account.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/60
                - PR link #2: https://github.com/shailis17/IT202-009/pull/61
                - PR link #3: https://github.com/shailis17/IT202-009/pull/62
            - Screenshots & Evidence
                - Screenshot #1 & #2
                
                ![image](https://user-images.githubusercontent.com/83250817/144164132-47b3f2a8-1714-48dc-a459-cb813e204010.png)

                ![image](https://user-images.githubusercontent.com/83250817/144164012-86823fe7-c868-433f-b219-075fc1b07a15.png)

                    - User will see user-friendly error messages when appropriate
                    - User will see user-friendly success message when account is created successfully  
                        - Redirect user to their Accounts page

                - Code Snippet from create_account.php:
                    - `$db = getDB();
            $an = null;
            $stmt = $db->prepare("INSERT INTO Accounts (account_number, user_id, balance, account_type) VALUES(:an, :uid, :deposit, :type)");
            $uid = get_user_id(); //caching a reference

            try {
                $stmt->execute([":an" => $an, ":uid" => null, ":type" => null, ":deposit" => null]);
                $account_id = $db->lastInsertId();
                //flash("account_id = $account_id");
                $an = str_pad($account_id+1,12,"202", STR_PAD_LEFT);
                $stmt->execute([":an" => $an, ":uid" => $uid, ":type" => $type, ":deposit" => $deposit]);
                
                flash("Successfully created account!", "success");
            } 
            catch (PDOException $e) {
                flash("An unexpected error occurred, please try again " . var_export($e->errorInfo, true), "danger");
            }`
                - System will generate a unique 12 digit account number ==> Generate the number based on the id column; requires inserting a null first to get the last insert id, then update the record immediately after


    - [X] (11/29/2021) User will be able to list their accounts
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/my_accounts.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/61
                - PR link #2: https://github.com/shailis17/IT202-009/pull/62

            - Screenshots & Evidence
                - Screenshot #1
                ![image](https://user-images.githubusercontent.com/83250817/144164609-272e893c-6e0f-45ca-a535-bef9f577049c.png)

                    - Show account number, account type and balance
                - code snippet from my_accounts.php
                
                `$uid = get_user_id();
                $query = "SELECT account_number, account_type, balance, created, id from Accounts ";
                $params = null;

                $query .= " WHERE user_id = :uid AND active = 1";
                $params =  [":uid" => "$uid"];

                $query .= " ORDER BY created desc LIMIT 5";`
                    - Limit results to 5 for now

    - [X] (11/30/2021) User will be able to click an account for more information (aka Transaction History page)
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/my_accounts.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/61
                - PR link #2: https://github.com/shailis17/IT202-009/pull/62

            - Screenshots & Evidence
                - Screenshot #1 
                ![image](https://user-images.githubusercontent.com/83250817/144165429-bee13f4c-bdc4-4a53-bc11-8955ef684436.png)
                    - Show account number, account type, balance, opened/created date
                    - Show transaction history (from Transactions table)
                - Code Snippet from my_accounts.php:
                    `$src_id = (int)se($_POST, "account_id", "", false);
                    $query = "SELECT src, dest, transactionType, balanceChange, memo, created from Transaction_History ";
                    $params = null;

                    $query .= " WHERE src = :src_id";
                    $params =  [":src_id" => "$src_id"];

                    $query .= " ORDER BY created desc LIMIT 10";`
                    - For now limit results to 10 latest

    - [X] (11/30/2021) User will be able to deposit/withdraw from their account(s)
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Links: 
                - Link #1: https://sss8-prod.herokuapp.com/Project/deposit.php
                - Link #2: https://sss8-prod.herokuapp.com/Project/withdraw.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/62
                - PR link #2: https://github.com/shailis17/IT202-009/pull/65
            - Screenshots
                - Screenshot #1-5
                    - ![image](https://user-images.githubusercontent.com/83250817/144166128-bdc4f6b3-466c-418d-806c-6d5a4ba21baa.png)

                    ![image](https://user-images.githubusercontent.com/83250817/144166431-4325608b-ba49-411b-a226-c2d3048138e2.png)

                    ![image](https://user-images.githubusercontent.com/83250817/144166693-73bf2e51-4a9e-4a11-902c-ff013551a711.png)

                    ![image](https://user-images.githubusercontent.com/83250817/144166834-774ac713-32a2-4d12-9b7b-b3b75940b9f2.png)

                    ![image](https://user-images.githubusercontent.com/83250817/144169075-36391475-08da-4495-8a75-06ffdfef0eef.png)


                        - Form should have a dropdown of users accounts to pick from
                            - World account should not be in the dropdown
                        - Form should have a field to enter a positive numeric value
                        - For withdraw, add a check to make sure they can’t withdraw more money than the account has
                        - Form should allow the user to record a memo for the transaction

                - Screenshot 6-7:
                ![image](https://user-images.githubusercontent.com/83250817/144305017-63060c2c-5396-45bd-8bb9-564d94dfce02.png)

                ![image](https://user-images.githubusercontent.com/83250817/144305082-6745100a-e83f-4090-80ff-9c792e7141bd.png)

                    - Success message and redirects back to my_accounts.php

                - Code Snippet from functions.php:
                `function get_account_balance($aid)
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
                `
                    - Used to check for sufficent withdrawal funds and fetch world account id 

- Milestone 3
    - [X] (12/2/2021) User will be able to transfer between their accounts
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/transfer.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/68
            - Screenshots
                - Screenshot #1 
                    ![image](https://user-images.githubusercontent.com/83250817/145426025-f1a63b41-4a9d-44f1-bd68-bf26fde32498.png)

                    - Form should include a dropdown first AccountSrc and a dropdown for AccountDest (only accounts the user owns; no world account)
                    - Form should include a field for a positive numeric value
                    - Form should allow the user to record a memo for the transaction
                - Screenshot #2-3
                    ![image](https://user-images.githubusercontent.com/83250817/145427717-9006292c-9b5a-49e9-a114-afdadc9b5014.png)
                    ![image](https://user-images.githubusercontent.com/83250817/145427770-715ac9f7-a01e-4f69-8027-da21415219fe.png)
                        
                        - Each transaction is recorded as a transaction pair in the Transaction table
                        - These will reflect in the transaction history page
                - Screenshot #4-6
                    ![image](https://user-images.githubusercontent.com/83250817/145428091-53474d08-7076-4116-b70a-1c70e0c0138b.png)
                    ![image](https://user-images.githubusercontent.com/83250817/145428185-d78a3617-61f6-46ba-9d26-b0c2828dd400.png)
                    ![image](https://user-images.githubusercontent.com/83250817/145428261-1c500941-57d1-4e5c-b19b-eebb05145c45.png)

                    - Show appropriate user-friendly error messages
                        - checks to see if src & dest are the same to prevent transfer to/from same account 
                        - System shouldn’t allow the user to transfer more funds than what’s available in AccountSrc
                        - checks amount field for a positive numeric value
                - Screenshot #7
                    ![image](https://user-images.githubusercontent.com/83250817/145427295-e13e1cb9-54fe-4a11-8573-c0b7d5799af8.png)
    
                    - User-friendly success message and redirection to my_accounts.php to view change in balance/transaction history

    - [X] (12/8/2021) Transaction History page
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/my_accounts.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/70
                - PR link #2: https://github.com/shailis17/IT202-009/pull/72
            - Screenshots
                - Screenshot #1 
                ![image](https://user-images.githubusercontent.com/83250817/145429706-3f1f8950-d9ef-47f0-8d73-0324a04c1996.png)
                ![image](https://user-images.githubusercontent.com/83250817/145429789-5b418c0f-cfb4-4206-ba94-9bc2d0376d94.png)
                
                    - Will show the latest 10 transactions by default
                    - Transactions should paginate results after the initial 10
                - Screenshot #2
                ![image](https://user-images.githubusercontent.com/83250817/145430683-b6a413ef-9435-45af-9919-432fd9276b24.png)

                    - User will be able to filter transactions between two dates
                    - User will be able to filter transactions by type (deposit, withdraw, transfer)

    - [X] (12/7/2021) User’s profile page should record/show First and Last name
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: 
                - Link #1: https://sss8-prod.herokuapp.com/Project/register.php
                - Link #2: https://sss8-prod.herokuapp.com/Project/profile.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/69
            - Screenshots
                - Screenshot #1-2
                ![image](https://user-images.githubusercontent.com/83250817/145432004-ef000ca9-d3a7-4ec7-8c80-65468eca51f2.png)
                ![image](https://user-images.githubusercontent.com/83250817/145431853-75b3c97d-3945-4f90-8300-e87c4130dce9.png)

                    - User’s profile page should record/show First and Last name
                    - Ask for this information upon registration
    - [X] (12/8/2021) User will be able to transfer funds to another user’s account
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/ext_transfer.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/71
                - PR link #2: https://github.com/shailis17/IT202-009/pull/73
            - Screenshots
                - Screenshot #1
                ![image](https://user-images.githubusercontent.com/83250817/145453716-318fe7ea-53a6-4166-8c8c-4a06fd35f874.png)

                    - Form should include a dropdown of the current user’s accounts (as AccountSrc)
                    - Form should include a field for the destination user’s last name
                    - Form should include a field for the last 4 digits of the destination user’s account number (to lookup AccountDest)
                    - Form should include a field for a positive numerical value
                    - Form should allow the user to record a memo for the transaction
                - Screenshots #2-6
                ![image](https://user-images.githubusercontent.com/83250817/145452759-37a2e70c-7645-4a06-ab85-d9ccbbb544d6.png)
                ![image](https://user-images.githubusercontent.com/83250817/145452803-921bd38c-1ef0-4f78-98d1-643572f8af3f.png)
                ![image](https://user-images.githubusercontent.com/83250817/145452859-cad3fe29-d5a2-4add-9169-4e31d3188c70.png)
                ![image](https://user-images.githubusercontent.com/83250817/145452934-452d6e03-c81b-4b1e-bbe9-3b607083048a.png)
                ![image](https://user-images.githubusercontent.com/83250817/145452981-dd74e6f9-4c38-42e8-b106-29eed21ff519.png)

                    - User-friendly error message
                        - System shouldn’t let the user transfer more than the balance of their account
                        - System will lookup appropriate account based on destination user’s last name and the last 4 digits of the account number
                
                - Screenshot #7
                ![image](https://user-images.githubusercontent.com/83250817/145453065-4c47a32a-2017-44d8-883f-cbf677ab2231.png)

                    - Success message and redirection to my_accounts.php to see balance change and transaction history

                - Screenshot #8
                ![image](https://user-images.githubusercontent.com/83250817/145453263-6ad91cbc-21b9-4ea7-a2f9-a470eb77debc.png)
                    - Transaction will be recorded with the type as “ext-transfer”
                    - Each transaction is recorded as a transaction pair in the Transaction table
                    - These will reflect in the transaction history page

- Milestone 4
    - [X] (12/12/2021) User can set their profile to be public or private (will need another column in Users table)
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/profile.php 
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/83
            - Screenshots
                - Screenshot #1 & #2
                ![image](https://user-images.githubusercontent.com/83250817/146846539-6589f872-1997-42f2-b802-6487ed52f453.png)
                
                ![image](https://user-images.githubusercontent.com/83250817/146846567-ae0a8877-5aa5-4f15-b0ad-685350f1d66e.png)
                    - user can set public/private

    - [X] (12/18/2021) User will be able open a savings account
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/create_account.php 
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/84
            - Screenshots
                - Screenshot #1-3
                ![image](https://user-images.githubusercontent.com/83250817/146847841-a70efcd8-2585-4593-9658-462abca896b3.png)
                ![image](https://user-images.githubusercontent.com/83250817/146847913-ee417cd3-c4e4-4119-b4b3-8f2653208f51.png)
                ![image](https://user-images.githubusercontent.com/83250817/146847981-4820a834-754d-41ec-ad5d-2b05b3bd003c.png)
                  - create savings account, set apy, redirection to accounts page, see account info/transaction history

    - [X] (12/19/2021) User will be able to take out a loan
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/loan.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/85
            - Screenshots
                - Screenshot #1
                ![image](https://user-images.githubusercontent.com/83250817/146848724-3c86ded3-5762-410b-a4cd-fe0f91800ff7.png)
                    - Form will have a dropdown of the user’s accounts of which to deposit the money into
                    - require min $500
                - Screenshot #2
                ![image](https://user-images.githubusercontent.com/83250817/146848813-01e5535b-e091-4673-acc6-2c4816f77f01.png)

                - Code so that [User can’t transfer more money from a loan once it’s been opened and a loan account should not appear in the Account Source dropdowns
                ](https://github.com/shailis17/IT202-009/pull/85/commits/c3f359608987535599b839b1edfbeee49b3e348b)

                - Screenshot #3 
                ![image](https://user-images.githubusercontent.com/83250817/146849259-3a78a3d0-0f17-43b8-bd17-e94fff30b04b.png)
                    - A loan with 0 balance will be considered paid off and will not accrue interest and will be eligible to be marked as closed

    - [X] (12/18/2021) Listing accounts and/or viewing Account Details should show any applicable APY or “-” if none is set for the particular account (may alternatively just hide the display for these types)
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/my_accounts.php 
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/84
            - Screenshots
                - Screenshot #1-3
                ![image](https://user-images.githubusercontent.com/83250817/146847981-4820a834-754d-41ec-ad5d-2b05b3bd003c.png)
                ![image](https://user-images.githubusercontent.com/83250817/146848368-4a8c48a9-0b2f-4316-864c-50f3fdf58c12.png)
                ![image](https://user-images.githubusercontent.com/83250817/146848394-4102721a-815c-4943-a873-f6058b9f1335.png)
                    - shows apy where applicable, hides column where not needed (checkings account)

    - [X] (12/19/2021) User will be able to close an account
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: https://sss8-prod.herokuapp.com/Project/my_accounts.php 
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/86
            - Screenshots
                - Screenshot #1-3
                ![image](https://user-images.githubusercontent.com/83250817/146849613-5facf50f-5252-40b1-a52c-ceeb2ee4c953.png)
                ![image](https://user-images.githubusercontent.com/83250817/146849776-add04468-f819-445e-9be8-7a518f347f55.png)
                ![image](https://user-images.githubusercontent.com/83250817/146849974-315c983f-c014-4edd-8c6c-a8d38c07209f.png)
                - checks for balance = 0 to be eligible to close account
                - closing sets active = 0, closed accounts don't show up
                - Code shows that [all queries for Accounts should be updated to pull only “active” = true accounts (i.e., dropdowns, My Accounts, etc)
                ](https://github.com/shailis17/IT202-009/pull/86/commits/c5c44e687b822d536ec2ae16bf8b49c80ba1d785) 

    - [X] (12/20/2021) Admin role (leave this section for last)
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link:
                - Link #1: https://sss8-prod.herokuapp.com/Project/admin/manage_users.php
                - Link #2: https://sss8-prod.herokuapp.com/Project/admin/view_accounts.php
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/87
            - Screenshots
                - Screenshots #1-3
                ![image](https://user-images.githubusercontent.com/83250817/146850140-6fd1ca25-b17e-4e08-8707-ef6340f8f30c.png)
                ![image](https://user-images.githubusercontent.com/83250817/146850198-fd9adc7b-67af-4487-9a40-62fec7fb5e87.png)
                ![image](https://user-images.githubusercontent.com/83250817/146850246-82b5c874-2d9a-4565-bd7b-146d553ddc7a.png)
                   - Will be able to search for users by firstname and/or lastname
                   - Will be able to look-up specific account numbers (partial match).

                - Screenshot #4
                ![image](https://user-images.githubusercontent.com/83250817/146850278-a05472a0-47b5-4eda-a1be-30c27080e048.png)
                    - Will be able to see the transaction history of an account

                - Screenshot #5
                ![image](https://user-images.githubusercontent.com/83250817/146850317-d4a5aa55-0e2b-40af-9788-045a4c4ab11f.png)
                   - Will be able to [freeze an account](https://github.com/shailis17/IT202-009/pull/87/commits/74a31e8c5db80eff0843be1476937f037685b16a) (this is similar to disable/delete but it’s a different column)
                   - [Update transactions logic to not allow frozen accounts to be used for a transaction](https://github.com/shailis17/IT202-009/pull/87/commits/8da9d320077b35073104d009c6edea7d971c12af)

                - Screenshot #6-8
                ![image](https://user-images.githubusercontent.com/83250817/146961068-5b3398ac-cbbc-4d71-bd6a-b738b71223a0.png)
                ![image](https://user-images.githubusercontent.com/83250817/146961186-16a1caac-878d-4890-8be1-e121ab8aaa17.png)
                ![image](https://user-images.githubusercontent.com/83250817/146961240-94661929-8bb6-47b9-8ab3-ca35824990e1.png)
                    - Will be able to open accounts for specific users

                - Screenshot #9 
                ![image](https://user-images.githubusercontent.com/83250817/146961579-4f5581f6-52d3-4bc1-904c-0e1d0780ce79.png)
                    - Will be able to deactivate a user
                    - [set Users active = 0 and prevent login](https://github.com/shailis17/IT202-009/pull/87/commits/5f61ccc6b724879016cb077d1a19afb6267ce954)


### Instructions
#### Don't delete this
1. Pick one project type
2. Create a proposal.md file in the root of your project directory of your GitHub repository
3. Copy the contents of the Google Doc into this readme file
4. Convert the list items to markdown checkboxes (apply any other markdown for organizational purposes)
5. Create a new Project Board on GitHub
   - Choose the Automated Kanban Board Template
   - For each major line item (or sub line item if applicable) create a GitHub issue
   - The title should be the line item text
   - The first comment should be the acceptance criteria (i.e., what you need to accomplish for it to be "complete")
   - Leave these in "to do" status until you start working on them
   - Assign each issue to your Project Board (the right-side panel)
   - Assign each issue to yourself (the right-side panel)
6. As you work
  1. As you work on features, create separate branches for the code in the style of Feature-ShortDescription (using the Milestone branch as the source)
  2. Add, commit, push the related file changes to this branch
  3. Add evidence to the PR (Feat to Milestone) conversation view comments showing the feature being implemented
     - Screenshot(s) of the site view (make sure they clearly show the feature)
     - Screenshot of the database data if applicable
     - Describe each screenshot to specify exactly what's being shown
     - A code snippet screenshot or reference via GitHub markdown may be used as an alternative for evidence that can't be captured on the screen
  4. Update the checklist of the proposal.md file for each feature this is completing (ideally should be 1 branch/pull request per feature, but some cases may have multiple)
    - Basically add an x to the checkbox markdown along with a date after
      - (i.e.,   - [x] (mm/dd/yy) ....) See Template above
    - Add the pull request link as a new indented line for each line item being completed
    - Attach any related issue items on the right-side panel
  5. Merge the Feature Branch into your Milestone branch (this should close the pull request and the attached issues)
    - Merge the Milestone branch into dev, then dev into prod as needed
    - Last two steps are mostly for getting it to prod for delivery of the assignment 
  7. If the attached issues don't close wait until the next step
  8. Merge the updated dev branch into your production branch via a pull request
  9. Close any related issues that didn't auto close
    - You can edit the dropdown on the issue or drag/drop it to the proper column on the project board