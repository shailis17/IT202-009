ALTER TABLE Users
ADD COLUMN `First Name` varchar(30) not null default (""), 
ADD COLUMN `Last Name` varchar(30) not null default ("")
COMMENT 'User’s profile page should record/show First and Last name';