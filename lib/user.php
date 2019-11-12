<?php
// 設定登入狀態
function user_login($user_data) {
	$session = get_session();

	if(!empty($user_data['department_id']) && empty($user_data['department'])) {
		$result = model::query('SELECT `id`, `name`, `permission` FROM `department` WHERE `id` = \'' . $user_data['department_id'] . '\';');
		if(!empty($result)) $user_data['department'] = $result[0];
	}

	if(!empty($user_data['position_id']) && empty($user_data['position'])) {
		$result = model::query('SELECT `id`, `name`, `permission` FROM `position` WHERE `id` = \'' . $user_data['position_id'] . '\';');
		if(!empty($result)) $user_data['position'] = $result[0];
	}
	if(isset($result)) unset($result);

	$_SESSION['user'] = $user_data;

	return true;
}

// 檢查登入工具
function check_login() {
	$session = get_session();
	if(empty($session['user']) || empty($session['user']['id'])) return false;

	return $session['user'];
}

// 登出工具
function sign_out() {
	$session = get_session();

	$account = $_SESSION['user']['account'];
	if(!empty($_SESSION['user'])) unset($_SESSION['user']);

	return $account;
}

?>