ALTER TABLE Users ADD COLUMN visibility tinyint(1) 
default 0
COMMENT 'Boolean of public or private (not public) profile';