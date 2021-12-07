ALTER TABLE Users
DROP COLUMN firstname, DROP COLUMN lastname, 
ADD COLUMN firstname varchar(30) not null default (""),
ADD COLUMN lastname varchar(30) not null default ("");