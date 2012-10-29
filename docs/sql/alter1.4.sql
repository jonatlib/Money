SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Placeholder table for view `Comulative`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Comulative` (`owner` INT, `date` INT, `summary` INT);

-- -----------------------------------------------------
-- Placeholder table for view `Perday`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Perday` (`id` INT, `category` INT, `owner` INT, `subcategory` INT, `title` INT, `date` INT, `value` INT, `description` INT, `sum` INT);

-- -----------------------------------------------------
-- View `Comulative`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `Comulative` ;
DROP TABLE IF EXISTS `Comulative`;
DELIMITER $$
CREATE  OR REPLACE VIEW `Comulative` AS
SELECT t.owner, t.date, (
	SELECT SUM( x.value ) 
	FROM Money x
	WHERE x.id <= t.id AND x.owner = t.owner
	) AS summary
FROM Money t
GROUP BY date, owner
ORDER BY t.id
$$
DELIMITER ;

;

-- -----------------------------------------------------
-- View `Perday`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `Perday` ;
DROP TABLE IF EXISTS `Perday`;
DELIMITER $$
CREATE  OR REPLACE VIEW `Perday` AS
SELECT m.*, sum(m.value) as sum from Money m group by `date`
$$
DELIMITER ;

;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
