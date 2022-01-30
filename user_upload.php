#!/usr/bin/php
<?php
use SQL\SQL;
use Config\Config;
use Utils\Utils;

require_once(__DIR__ . '/sql.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/utils.php');

if ($mysqlHostIdx = array_search('-h', $argv)) {
	$mysqlHost = $argv[$mysqlHostIdx + 1];
}
if ($usernameIdx = array_search('-u', $argv)) {
	$username = $argv[$usernameIdx + 1];
}
if ($passwordIdx = array_search('-p', $argv)) {
	$password = $argv[$passwordIdx + 1];
}

if (in_array('--help', $argv)) {
	include(__DIR__ . '/help.php');
} else {
	if (isset($mysqlHost) && isset($username) && isset($password)) {
		try {
			$sql = new SQL($mysqlHost, $username, $password);

			if ($sql->get_db_connection_success()) {
				echo 'Successfully connected to DB';
				if (in_array('--create_table', $argv)) {
					Utils::create_table($sql);
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