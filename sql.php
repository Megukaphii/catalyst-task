<?php
namespace SQL;

use PDO;
use PDOException;
use PDOStatement;

class SQLException extends \Exception {}

class SQL {
	const GENERIC_ERR_MSG = 'An error occured, please try again later or contact support staff';
	const NO_DB_CONNECTION_ERR_MSG = 'DB connection not yet established';

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
			throw new SQLException('DB connection error', 1);
		}
	}

	public function return_none($query, array $params) {
		if (!$this->get_db_connection_success()) {
			throw new SQLException(self::NO_DB_CONNECTION_ERR_MSG, 2);
		}

		try {
			$sql = $this->conn->prepare($query);
			$sql = self::bind_params($sql, $params);
			
			$sql->execute();
		} catch (Throwable $e) {
			throw new SQLException(self::GENERIC_ERROR_MESSAGE, 3);
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