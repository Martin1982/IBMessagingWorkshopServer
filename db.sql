SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `intweet` ;
CREATE SCHEMA IF NOT EXISTS `intweet` DEFAULT CHARACTER SET latin1 ;
USE `intweet` ;

-- -----------------------------------------------------
-- Table `intweet`.`messages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `intweet`.`messages` ;

CREATE  TABLE IF NOT EXISTS `intweet`.`messages` (
  `messageId` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '	' ,
  `username` VARCHAR(25) NOT NULL ,
  `message` VARCHAR(255) NOT NULL ,
  `placed` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`messageId`) )
ENGINE = MyISAM
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1
COMMENT = 'Table containing \'tweet\' messages' ;


-- -----------------------------------------------------
-- Table `intweet`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `intweet`.`users` ;

CREATE  TABLE IF NOT EXISTS `intweet`.`users` (
  `username` VARCHAR(25) NOT NULL ,
  `firstname` VARCHAR(145) NULL DEFAULT NULL ,
  `lastname` VARCHAR(145) NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `phone` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`username`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = latin1, 
COMMENT = 'Users table' ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
