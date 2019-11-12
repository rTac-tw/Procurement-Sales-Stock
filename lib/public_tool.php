<?php
// 啟用session
function get_session( $session_key = null ) {
	if(!session_id()) {
		session_start();
	}
	$session = $_SESSION;

	$session['session_id'] = session_id();

	$session = get_array($session, $session_key);

	return $session;
}

// 簡單防XSS驗證碼
function get_verify_code() {
	return md5(get_session('session_id'));
}

// 從陣列取值小工具
function get_array($array, $keys = null) {
	if(!empty($keys)) {
		if(is_string($keys)) $keys = array($keys);

		foreach($keys as $key) {
			if(empty($array[$key])) {
				return null;
			} else {
				$array = &$array[$key];
			}
		}
	}

	return $array;
}

// 載入 view
function print_view($view_filename, array $param = []) {
	if(!empty($param)) {
		foreach($param as $param_key=>$param_val) {
			if($param_key == 'view_filename') continue;
			$$param_key = $param_val;
		}
	}
	if(is_file(WEB_PATH . '/view/' . $view_filename)) {
		include(WEB_PATH . '/view/' . $view_filename);
	} else {
		error_log('include view failed: ' . $view_filename);
	}
}

?>