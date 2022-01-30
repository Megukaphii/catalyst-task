#!/usr/bin/php
<?php
use SQL\SQL;
use Config\Config;
use Utils\Utils;

require_once(__DIR__ . '/sql.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/utils.php');

if ($mysqlHostIdx = array_search('-h', $argv) + 1) {
	$mysqlHost = $argv[$mysqlHostIdx];
}
if ($usernameIdx = array_search('-u', $argv) + 1) {
	$username = $argv[$usernameIdx];
}
if ($passwordIdx = array_search('-p', $argv) + 1) {
	$password = $argv[$passwordIdx];
}
if ($fileIdx = array_search('--file', $argv) + 1) {
	$filename = $argv[$fileIdx];
}
$displayHelp = false;
if (in_array('--help', $argv)) {
	$displayHelp = true;
}
$dryRun = false;
if (in_array('--dry_run', $argv)) {
	$dryRun = true;
}
$createTable = false;
if (in_array('--create_table', $argv)) {
	$createTable = true;
}

if ($displayHelp) {
	include(__DIR__ . '/help.php');
} else {
	if (isset($mysqlHost) && isset($username) && isset($password)) {
		try {
			$sql = new SQL($mysqlHost, $username, $password);

			if ($sql->get_db_connection_success()) {
				fwrite(STDOUT, "Successfully connected to DB\n");

				if ($createTable) {
					if (!$dryRun) {
						try {
							Utils::create_table($sql);
						} catch (Exception $e) {
							if (get_class($e) == 'PDOException' && $e->getCode() == "42S01") {
								fwrite(STDOUT, '[' . get_class($e) . ', ' . $e->getCode() . "]: Table already exists\n");
							} else {
								fwrite(STDOUT, '[' . get_class($e) . ', ' . $e->getCode() . ']: ' . $e->getMessage() . "\n");
							}
						}
					}
					fwrite(STDOUT, "Successfully created table\n");
				}

				if (isset($filename)) {
					$usersData = Utils::get_csv_data($filename);
					fwrite(STDOUT, "Opened file\n");
					for ($i = 1; $i < count($usersData); $i++) {
						try {
							// Could use first row of usersData to get column names, but that would be wildly insecure
							$query = 'INSERT INTO users(name, surname, email) VALUES (?, ?, ?)';
							// $usersData[$i] structure is [name, surname, email]
							$name = ucfirst(strtolower(trim($usersData[$i][0])));
							$surname = ucfirst(strtolower(trim($usersData[$i][1])));
							$email = trim($usersData[$i][2]);
							if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
								$params = [$name, $surname, $email];
								if (!$dryRun) {
									$sql->return_none($query, $params);
								}
								fwrite(STDOUT, 'Successfully added row: ' . implode(', ', $params) . "\n");
							} else {
								throw new Exception('Invalid email address: ' . $email, 1);
							}
						} catch (Exception $e) {
							if (get_class($e) == 'PDOException') {
								if ($e->getCode() == 23000) {
									fwrite(STDOUT, '[' . get_class($e) . ', ' . $e->getCode() . ']: Duplicate email address at row ' . $i . "\n");
								} else if ($e->getCode() == "42S02") {
									fwrite(STDOUT, '[' . get_class($e) . ', ' . $e->getCode() . "]: Users table doesn't exist\n");
									break;
								}
							} else {
								fwrite(STDOUT, '[' . get_class($e) . ', ' . $e->getCode() . ']: ' . $e->getMessage() . ' at row ' . $i ."\n");
							}
						}
					}
				}
			}
		} catch (TypeError $e) {
			fwrite(STDOUT, '[TypeError]: ' . Config::ERR_MSG_GENERIC .  "\n");
		} catch (Throwable $e) {
			fwrite(STDOUT, '[' . get_class($e) . ', ' . $e->getCode() . ']: ' . $e->getMessage() . "\n");
		}
	} else {
		$requiredFlags = [];
		isset($mysqlHost) ? null : $requiredFlags[] = 'Host name (-h flag)';
		isset($username) ? null : $requiredFlags[] = 'Username (-u flag)';
		isset($password) ? null : $requiredFlags[] = 'Password (-p flag)';
		$missingStr = implode(', ', $requiredFlags) . ' are required.';
		echo $missingStr;
	}
}
?>