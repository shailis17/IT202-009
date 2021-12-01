# Project Name: Simple Bank
## Project Summary: This project will create a bank simulation for users. They’ll be able to have various accounts, do standard bank functions like deposit, withdraw, internal (user’s accounts)/external(other user’s accounts) transfers, and creating/closing 
## Github Link: https://github.com/shailis17/IT202-009/tree/prod
## Project Board Link: https://github.com/shailis17/IT202-009/projects/1
## Website Link: https://sss8-prod.herokuapp.com/Project
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

                $query .= " WHERE user_id = :uid";
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
            - Status: Pending (Completed, Partially working, Incomplete, Pending)
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
- Milestone 4
### Intructions
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