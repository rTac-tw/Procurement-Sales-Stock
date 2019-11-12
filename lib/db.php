<?php
class DB {
	protected $conn;

	protected $debug = false;
	protected $debug_link_no;

	function __construct() {
		$this->conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
		if (!$this->conn) {
			error_log('DB Connection failed: ' . mysqli_connect_error());
			die('DB Connection failed.');
		} else {
			mysqli_query($this->conn, "SET NAMES 'utf8'");

			if(!mysqli_select_db($this->conn, DB_NAME)) {
				error_log('No Database: ' . DB_NAME);
				die('DB Connection failed.');
			}
		}

		if($this->debug) {
			$this->debug_no = rand(100, 900);
			error_log('db link ' . $this->debug_no);
		}
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
			return null;
		}
	}

	public function get_insert_id() {
		return mysqli_insert_id($this->conn);
	}

	function __destruct() {
		if($this->conn) mysqli_close($this->conn);
		$this->conn = null;

		if($this->debug) {
			error_log('db unlink ' . $this->debug_no);
		}
	}
}
?>