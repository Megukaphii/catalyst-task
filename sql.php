<?php
namespace SQL;

use PDO;
use PDOException;

class SQLException extends \Exception {}

class SQL {
	static $conn;

	public static function create_db_connection($host, $username, $password) {
		try {
			$dsn = 'mysql:dbname=catalyst;host=' . $host;
			$conn = new PDO($dsn, $username, $password);
			$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage() . "\n";
			die();
		}
	}
}
?>