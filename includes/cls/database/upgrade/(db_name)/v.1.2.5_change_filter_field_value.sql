ALTER TABLE `filters` CHANGE `marrital` `marrital` ENUM('single', 'married', 'marriade') NULL;
UPDATE filters SET filters.marrital = 'married' WHERE filters.marrital = 'marriade';
ALTER TABLE `filters` CHANGE `marrital` `marrital` ENUM('single', 'married') NULL;

ALTER TABLE `filters` CHANGE `employmentstatus` `employmentstatus` ENUM('employee','unemployed','retired', 'unemployee') NULL;
UPDATE filters SET filters.employmentstatus = 'unemployee' WHERE filters.employmentstatus = 'unemployed';
ALTER TABLE `filters` CHANGE `employmentstatus` `employmentstatus` ENUM('employee','unemployed','retired', 'unemployee') NULL;
