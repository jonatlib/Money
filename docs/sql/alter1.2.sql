SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `Money`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Money` ;

CREATE  TABLE IF NOT EXISTS `Money` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `category` INT NOT NULL ,
  `owner` INT NOT NULL ,
  `subcategory` INT NULL ,
  `title` VARCHAR(80) NULL ,
  `date` DATE NOT NULL ,
  `value` INT NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Money_Category1` (`category` ASC) ,
  INDEX `fk_Money_Subcategory1` (`subcategory` ASC) ,
  INDEX `fk_Money_Users1` (`owner` ASC) ,
  CONSTRAINT `fk_Money_Category1`
    FOREIGN KEY (`category` )
    REFERENCES `Category` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Money_Subcategory1`
    FOREIGN KEY (`subcategory` )
    REFERENCES `Subcategory` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Money_Users1`
    FOREIGN KEY (`owner` )
    REFERENCES `Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
