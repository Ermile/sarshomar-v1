ALTER TABLE `filters` CHANGE `marrital` `marrital` ENUM('single', 'married') NULL;
ALTER TABLE `filters` CHANGE `employmentstatus` `employmentstatus` ENUM('employee','unemployed','retired') NULL;