<?php
class DB {
	protected $conn;

	function __construct() {
		$this->conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
		if (!$this->conn) {
			error_log('DB Connection failed: ' . mysqli_connect_error());
			die('DB Connection failed.');
		} else {
			if(!mysqli_select_db($this->conn, DB_NAME)) {
				error_log('No Database: ' . DB_NAME);
				die('DB Connection failed.');
			}
		}

		error_log('db_new');
	}

	public function query($sql) {
		if($this->conn) {
			$result = mysqli_query($this->conn, $sql);
			if(is_bool($result)) return $result;

			$return = array();
			while($row = mysqli_fetch_assoc($result)) {
				$return[] = $row;
			}

			return $return;
		} else {
			error_log('DB Unconnected.');
		}
	}

	function __destruct() {
		if($this->conn) mysqli_close($this->conn);
		$this->conn = null;

		error_log('db_exit');
	}
}
?>