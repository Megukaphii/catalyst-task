<?php
namespace Utils;

use SQL\SQL;

require_once(__DIR__ . '/sql.php');

class UtilsException extends \Exception {}

class Utils {
	const ERR_MSG_OPEN_FILE_FAIL = 'Failed to open file: ';

	public static function create_table(SQL $sql) {
		$query = 'CREATE TABLE `catalyst`.`users` (
			`UserID` INT NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(64) NOT NULL,
			`surname` VARCHAR(64) NOT NULL,
			`email` VARCHAR(128) NOT NULL,
			PRIMARY KEY (`UserID`),
			UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE);';
		$params = [];
		$sql->return_none($query, $params);
	}
	
	public static function get_csv_data($filename) {
		if (($csvHandle = fopen($filename, 'r')) !== FALSE) {
			$fileData = [];
			while (($csvData = fgetcsv($csvHandle)) !== FALSE) {
				$fileData[] = $csvData;
			}
			fclose($csvHandle);
		} else {
			throw new UtilsException(self::ERR_MSG_OPEN_FILE_FAIL . $filename, 1);
		}
		return $fileData;
	}
}
?>