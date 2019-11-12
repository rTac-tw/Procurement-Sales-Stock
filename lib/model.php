<?php
include_once(WEB_PATH . '/lib/db.php');

class model {
	private static $db;

	public static function query($sql) {
		if(!static::$db) static::$db = new DB();

		return static::$db->query($sql);
	}

	public static function get_insert_id() {
		return static::$db->get_insert_id();
	}
}
?>