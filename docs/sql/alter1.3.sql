SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- View `Comulative`
-- -----------------------------------------------------
CREATE  OR REPLACE VIEW `Comulative` AS
SELECT t.owner, t.date, SUM( t.value ) as sum , (
	SELECT SUM( x.value ) 
	FROM Money x
	WHERE x.id <= t.id AND x.owner = t.owner
	) AS summary
FROM Money t
GROUP BY date, owner
ORDER BY t.id


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

