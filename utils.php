<?php
namespace Utils;

use SQL\SQL;

require_once(__DIR__ . '/sql.php');

class UtilsException extends \Exception {}

class Utils {
	public static function create_table(SQL $sql) {
		$query = 'CREATE TABLE `catalyst`.`users` (
			`UserID` INT NOT NULL,
			`EmailAddress` VARCHAR(45) NOT NULL,
			`GivenName` VARCHAR(45) NOT NULL,
			`Surname` VARCHAR(45) NOT NULL,
			PRIMARY KEY (`UserID`),
			UNIQUE INDEX `EmailAddress_UNIQUE` (`EmailAddress` ASC) VISIBLE);';
		$params = [];
		$sql->return_none($query, $params);
	}
}
?>