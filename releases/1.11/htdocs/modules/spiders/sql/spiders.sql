CREATE TABLE `spiders` (                             
	`id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,          
	`robot-id` VARCHAR(128) DEFAULT NULL,                   
	`robot-name` VARCHAR(255) DEFAULT NULL,                 
	`robot-cover-url` VARCHAR(255) DEFAULT NULL,            
	`robot-details-url` VARCHAR(255) DEFAULT NULL,          
	`robot-owner-name` VARCHAR(128) DEFAULT NULL,           
	`robot-owner-url` VARCHAR(255) DEFAULT NULL,            
	`robot-owner-email` VARCHAR(255) DEFAULT NULL,          
	`robot-status` VARCHAR(255) DEFAULT NULL,               
	`robot-purpose` VARCHAR(128) DEFAULT NULL,              
	`robot-type` VARCHAR(64) DEFAULT NULL,                  
	`robot-platform` VARCHAR(64) DEFAULT NULL,              
	`robot-availability` VARCHAR(128) DEFAULT NULL,         
	`robot-exclusion` VARCHAR(32) DEFAULT NULL,             
	`robot-exclusion-useragent` VARCHAR(255) DEFAULT NULL,  
	`robot-noindex` VARCHAR(32) DEFAULT NULL,               
	`robot-host` MEDIUMTEXT,                                
	`robot-from` VARCHAR(32) DEFAULT NULL,                  
	`robot-useragent` VARCHAR(255) DEFAULT NULL,            
	`robot-language` VARCHAR(64) DEFAULT NULL,              
	`robot-description` MEDIUMTEXT,                         
	`robot-history` MEDIUMTEXT,                             
	`robot-environment` VARCHAR(128) DEFAULT NULL,          
	`modified-date` VARCHAR(64) DEFAULT NULL,               
	`modified-by` VARCHAR(64) DEFAULT NULL,    
	`robot-safeuseragent` VARCHAR(255) DEFAULT NULL,
	PRIMARY KEY (`id`)                                      
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `spiders_user` (        
	`spider_id` INT(12) UNSIGNED NOT NULL,  
	`uid` INT(12) UNSIGNED NOT NULL         
) ENGINE=MYISAM DEFAULT CHARSET=utf8;