# Project Name: Simple Bank
## Project Summary: This project will create a bank simulation for users. They’ll be able to have various accounts, do standard bank functions like deposit, withdraw, internal (user’s accounts)/external(other user’s accounts) transfers, and creating/closing 
## Github Link: https://github.com/shailis17/IT202-009/tree/prod
## Project Board Link: https://github.com/shailis17/IT202-009/projects/1
## Website Link: (Heroku Prod of Project folder)
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
    - [] (DATE OF COMPLETION) User will be able to register a new account
        -  List of Evidence of Feature Completion
            - Status: Partially Working ==> TODO get form to only clear password fields in form
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/11
                - PR link #2: https://github.com/shailis17/IT202-009/pull/27
                - PR link #3: TODO ==> MS1-Delvierable-Fixes
            - Screenshots
                - Screenshot #1 
                ![image](https://user-images.githubusercontent.com/83250817/140582985-37f7e4ab-bd6b-4198-91c7-4dafc4e36372.png) 
                    - Form Fields
                        - Username, email, password, confirm password (other fields optional)
                        - Email is required and must be validated
                        - Username is required
                        - Confirm password’s match
                - Screenshot #2 & #3
                ![image](https://user-images.githubusercontent.com/83250817/140583603-1f7fb04d-8ae4-4c73-91fd-9dc98333e415.png)
                
                ![image](https://user-images.githubusercontent.com/83250817/140583739-043c2014-ecec-451b-a521-8e2177afd2ad.png)
                    - System should let user know if username or email is taken and allow the user to correct the error without wiping/clearing the form

    - [ ] (mm/dd/yyyy of completion) User will be able to login to their account (given they enter the correct credentials)
        -  List of Evidence of Feature Completion
            - Status: Partially Working ==> TODO - User-friendly error message for incorrect username entry
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/31
                - PR link #2: TODO ==> MS1-Delvierable-Fixes
            - Screenshots
                - Screenshot #1-3
                ![image](https://user-images.githubusercontent.com/83250817/140620060-2d50fbd2-648b-4d07-b59c-cddfcbef92c3.png)

                ![image](https://user-images.githubusercontent.com/83250817/140620171-2c431c0f-255c-4a6d-9e4d-6dfabd41f7c8.png)

                TODO ==> Screenshot: figure out how to get invalid username message!
                    - User should see friendly error messages when an account either doesn’t exist or if passwords don’t match    
    - [X] (11/6/2021) User will be able to logout
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/13
                - PR link #2: TODO ==> MS1-Deliverable-Fixes
            - Screenshots
                - Screenshot #1
                ![image](https://user-images.githubusercontent.com/83250817/140619411-24389397-91af-4932-86b4-19461e7c780e.png)
                    - Logging out will redirect to login page
                    - User should see a message that they’ve successfully logged out
                - Screenshot #2
                ![image](https://user-images.githubusercontent.com/83250817/140619526-c587aed3-5c93-4631-825e-57511fc6ad0b.png)
                    - Session should be destroyed (so the back button doesn’t allow them access back in) ==> this screenshot is what shows after hitting back button from previous screenshot
                

    - [X] (11/2/2021 & 11/6/2021) Basic security rules implemented
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/28
                - PR link #2: TODO==> cleanup/fixes
            - Screenshots
                - Screenshot #1
                 ![image](https://user-images.githubusercontent.com/83250817/140619526-c587aed3-5c93-4631-825e-57511fc6ad0b.png)
                    - Authentication: Function to check if user is logged in, called on appropriate pages that only allow logged in users (ex: home page cannot be seen if logged out )

    - [ ] (mm/dd/yyyy of completion) Basic Roles implemented
        -  List of Evidence of Feature Completion
            - Status: Incomplete ==> TODO add function to check if a user has a specific role (we won’t use it for this milestone but it should be usable in the future)
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/28 
            - Screenshots/Evidence
                - see Project/sql folder
    - [X] (11/2/2021) Site should have basic styles/theme applied; everything should be styled
        -  List of Evidence of Feature Completion
            - Status: Completed (bootstrap TBA)
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
                - PR link #1: https://github.com/shailis17/IT202-009/pull/30
            - Screenshots/Evidence
                - See any screenshot above/below
                - See styles.css file
    - [X] (mm/dd/yyyy of completion) Any output messages/errors should be “user friendly”
        -  List of Evidence of Feature Completion
            - Status: Completed
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
                - PR link #1 (repeat as necessary)
            - Screenshots
                - Screenshot #1 (paste the image so it uploads to github) (repeat as necessary)
                    - Screenshot #1 description explaining what you're trying to show
    - [ ] (mm/dd/yyyy of completion) User will be able to see their profile
        -  List of Evidence of Feature Completion
            - Status: Pending (Completed, Partially working, Incomplete, Pending)
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
            - PR link #1 (repeat as necessary)
            - Screenshots
            - Screenshot #1 (paste the image so it uploads to github) (repeat as necessary)
                - Screenshot #1 description explaining what you're trying to show
    - [ ] (mm/dd/yyyy of completion) User will be able to edit their profile
        -  List of Evidence of Feature Completion
            - Status: Pending (Completed, Partially working, Incomplete, Pending)
            - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
            - Pull Requests
            - PR link #1 (repeat as necessary)
            - Screenshots
            - Screenshot #1 (paste the image so it uploads to github) (repeat as necessary)
                - Screenshot #1 description explaining what you're trying to show
- Milestone 2
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