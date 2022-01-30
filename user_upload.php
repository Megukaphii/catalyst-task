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

if (in_array('--help', $argv)) {
	include(__DIR__ . '/help.php');
} else {
	if (isset($mysqlHost) && isset($username) && isset($password)) {
		try {
			$sql = new SQL($mysqlHost, $username, $password);

			if ($sql->get_db_connection_success()) {
				echo "Successfully connected to DB\n";

				if (in_array('--create_table', $argv)) {
					Utils::create_table($sql);
				}

				if ($fileIdx = array_search('--file', $argv) + 1) {
					$usersData = Utils::get_csv_data($argv[$fileIdx]);
					echo print_r($usersData, true);
					/*for ($i = 1; $i < count($usersData); $i++) {
						$query = 'INSERT INTO users(';
					}*/
				}
			}
		} catch (TypeError $e) {
			echo '[TypeError]: ' . Config::ERR_MSG_GENERIC;
		} catch (Throwable $e) {
			echo '[' . get_class($e) . ', ' . $e->getCode() . ']: ' . $e->getMessage();
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
echo "\n";
?>