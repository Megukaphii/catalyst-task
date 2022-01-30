#!/usr/bin/php
<?php
use SQL\SQL;

require_once(__DIR__ . '/sql.php');

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
		SQL::create_db_connection($mysqlHost, $username, $password);
		if (SQL::$conn) {
			echo 'Successfully connected to DB';
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