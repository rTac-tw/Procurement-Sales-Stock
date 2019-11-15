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

function check_department_permission($department_id) {
	$user_data = check_login();
	$department_id = (int)@$department_id;

	if(!empty($user_data['department']) && !empty($user_data['department']['permission']) && !empty($department_id)) {
		$department_permission = explode(',', $user_data['department']['permission']);
		if(in_array($department_id, $department_permission)) return true;;
	}

	return false;
}

function check_position_permission($position_id) {
	$user_data = check_login();
	$position_id = (int)@$position_id;

	if(!empty($user_data['position']) && !empty($user_data['position']['permission']) && !empty($position_id)) {
		$position_permission = explode(',', $user_data['position']['permission']);
		if(in_array($position_id, $position_permission)) return true;;
	}

	return false;
}
?>