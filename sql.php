<?php
namespace SQL;

use PDO;
use PDOException;
use PDOStatement;
use Config\Config;

require_once(__DIR__ . '/config.php');

class SQLException extends \Exception {}

class SQL {
	const ERR_MSG_DB_CONNECTING = 'DB connection error';
	const ERR_MSG_NO_DB_CONNECTION = 'DB connection not yet established';

	private $conn = null;

	public function __construct($host, $username, $password) {
		$this->create_db_connection($host, $username, $password);
	}

	public function get_db_connection_success() {
		return $this->conn ? true : false;
	}

	private function create_db_connection($host, $username, $password) {
		try {
			$dsn = 'mysql:dbname=catalyst;host=' . $host;
			$this->conn = new PDO($dsn, $username, $password);
			$this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			throw new SQLException(self::ERR_MSG_DB_CONNECTING, 1);
		}
	}

	public function return_none($query, array $params) {
		if (!$this->get_db_connection_success()) {
			throw new SQLException(self::ERR_MSG_NO_DB_CONNECTION, 2);
		}

		try {
			$sql = $this->conn->prepare($query);
			$sql = self::bind_params($sql, $params);

			$sql->execute();
		} catch (Throwable $e) {
			throw new SQLException(Config::ERR_MSG_GENERIC, 3);
		}
	}

	private static function bind_params(PDOStatement $sql, array $params) {
		for ($i = 0; $i < count($params); $i++) {
			if (gettype($params[$i]) == 'boolean') {
				$sql->bindParam($i + 1, $params[$i], PDO::PARAM_BOOL);
			} else if (gettype($params[$i]) == 'integer') {
				$sql->bindParam($i + 1, $params[$i], PDO::PARAM_INT);
			}  else if (gettype($params[$i]) == 'NULL') {
				$sql->bindParam($i + 1, $params[$i], PDO::PARAM_NULL);
			} else {
				$sql->bindParam($i + 1, $params[$i], PDO::PARAM_STR);
			}
		}
		return $sql;
	}
}
?>