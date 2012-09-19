SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Users` ;

CREATE  TABLE IF NOT EXISTS `Users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(150) NOT NULL ,
  `password` VARCHAR(150) NOT NULL ,
  `salt` VARCHAR(150) NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `lastName` VARCHAR(45) NULL ,
  `deleted` TINYINT(1)  NULL ,
  `register` DATE NULL ,
  `role` VARCHAR(45) NULL DEFAULT 'user' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `UserVars`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UserVars` ;

CREATE  TABLE IF NOT EXISTS `UserVars` (
  `name` VARCHAR(15) NOT NULL ,
  `user` INT NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY (`name`, `user`) ,
  INDEX `fk_UserVars_Users` (`user` ASC) ,
  CONSTRAINT `fk_UserVars_Users`
    FOREIGN KEY (`user` )
    REFERENCES `Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Category` ;

CREATE  TABLE IF NOT EXISTS `Category` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `owner` INT NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Catefory_Users1` (`owner` ASC) ,
  CONSTRAINT `fk_Catefory_Users1`
    FOREIGN KEY (`owner` )
    REFERENCES `Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Subcategory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Subcategory` ;

CREATE  TABLE IF NOT EXISTS `Subcategory` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `category` INT NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Subcategory_Category1` (`category` ASC) ,
  CONSTRAINT `fk_Subcategory_Category1`
    FOREIGN KEY (`category` )
    REFERENCES `Category` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Money`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Money` ;

CREATE  TABLE IF NOT EXISTS `Money` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `category` INT NOT NULL ,
  `subcategory` INT NULL ,
  `title` VARCHAR(80) NULL ,
  `date` DATE NOT NULL ,
  `value` INT NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Money_Category1` (`category` ASC) ,
  INDEX `fk_Money_Subcategory1` (`subcategory` ASC) ,
  CONSTRAINT `fk_Money_Category1`
    FOREIGN KEY (`category` )
    REFERENCES `Category` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Money_Subcategory1`
    FOREIGN KEY (`subcategory` )
    REFERENCES `Subcategory` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Reporters`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Reporters` ;

CREATE  TABLE IF NOT EXISTS `Reporters` (
  `category` INT NOT NULL ,
  `user` INT NOT NULL ,
  PRIMARY KEY (`category`, `user`) ,
  INDEX `fk_Category_has_Users_Users1` (`user` ASC) ,
  INDEX `fk_Category_has_Users_Category1` (`category` ASC) ,
  CONSTRAINT `fk_Category_has_Users_Category1`
    FOREIGN KEY (`category` )
    REFERENCES `Category` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Category_has_Users_Users1`
    FOREIGN KEY (`user` )
    REFERENCES `Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Lost`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Lost` ;

CREATE  TABLE IF NOT EXISTS `Lost` (
  `id` VARCHAR(80) NOT NULL ,
  `user` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Lost_Users1` (`user` ASC) ,
  CONSTRAINT `fk_Lost_Users1`
    FOREIGN KEY (`user` )
    REFERENCES `Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
