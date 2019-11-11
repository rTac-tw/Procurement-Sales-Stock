<?php
function get_session( $session_key = null ) {
	if(!session_id()) {
		session_start();
	}
	$session = $_SESSION;

	$session['session_id'] = session_id();

	$session = get_array($session, $session_key);

	return $session;
}

function user_login($user_data) {
	$session = get_session();

	$_SESSION['user'] = $user_data;

	return true;
}

function check_login() {
	$session = get_session();
	if(empty($session['user']) || empty($session['user']['id'])) return false;

	return true;
}

function sign_out() {
	$session = get_session();

	$account = $_SESSION['user']['account'];
	if(!empty($_SESSION['user'])) unset($_SESSION['user']);

	return $account;
}

function get_verify_code() {
	return md5(get_session('session_id'));
}

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
?>