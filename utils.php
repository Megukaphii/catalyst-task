<?php
namespace Utils;

use SQL\SQL;

require_once(__DIR__ . '/sql.php');

class UtilsException extends \Exception {}

class Utils {
	const ERR_MSG_OPEN_FILE_FAIL = 'Failed to open file: ';
	const ERR_MSG_INVALID_EMAIL_ADDRESS = 'Invalid email address: ';

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

	public static function write_data_to_users_table($usersData, $row) {
		// Could use first row of usersData to get column names, but that would be wildly insecure
		$query = 'INSERT INTO users(name, surname, email) VALUES (?, ?, ?)';
		// $usersData[$i] structure is [name, surname, email]
		$name = ucfirst(strtolower(trim($usersData[$row][0])));
		$surname = ucfirst(strtolower(trim($usersData[$row][1])));
		$email = trim($usersData[$row][2]);
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$params = [$name, $surname, $email];
			if (!$dryRun) {
				$sql->return_none($query, $params);
			}
			fwrite(STDOUT, 'Successfully added row: ' . implode(', ', $params) . "\n");
		} else {
			throw new UtilsException(self::ERR_MSG_INVALID_EMAIL_ADDRESS . $email, 1);
		}
	}
}
?>