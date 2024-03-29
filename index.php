<?php
	if(!defined('WEB_PATH')) define('WEB_PATH', $_SERVER['DOCUMENT_ROOT']);

	include_once(WEB_PATH . '/lib/config.php');
	include_once(WEB_PATH . '/lib/public_tool.php');
	include_once(WEB_PATH . '/lib/user.php');

	include_once(WEB_PATH . '/lib/ajax.php');
?>
<html>
	<head>
		<title>測試題目</title>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	</head>
	<body>
<?php
	$message_arr = array();

	if(check_login()) {
		if(get_array($_POST, 'mode') == 'sign_out') {
			$account = sign_out();
			$message_arr[] = array('success', '已經成功登出');
		}
	} else {
		if(get_array($_POST, 'mode') == 'login' && get_array($_POST, 'verify_code') == get_verify_code()) {
			// 登入程序
			$account = get_array($_POST, 'pss_a');
			$password = get_array($_POST, 'pss_p');
			if($account && $password) {
				include_once(WEB_PATH . '/lib/model.php');

				$user_result = model::query('SELECT u.`id`, u.`account`, u.`name`, u.`gender`, u.`department_id`, u.`position_id`, p.`id` AS `pwd_id` FROM `user` AS u LEFT JOIN `user_pwd` AS p ON u.`id` = p.`user_id` WHERE u.`account` = \'' . $account . '\' AND p.`password` = \'' . md5($password) . '\';');

				if(empty($user_result)) {
					$message_arr[] = array('error', '請確認 帳號/密碼 輸入正確');
				} else {
					// 帳號存在且密碼正確(歷史資料中)
					$user_data = $user_result[0];

					// 另外比對目前使用密碼
					$user_pwd_result = model::query('SELECT `id`, `use_date` FROM `user_pwd` WHERE `user_id` = \''. $user_data['id'] . '\' ORDER BY `use_date` DESC LIMIT 1;');

					if(empty($user_pwd_result)) {
						// 未預期的錯誤，使用者沒有密碼紀錄
						error_log('ErrorTag, user_id: ' . $user_data['id'] . ' pwd list is null.');
					} else {
						if($user_data['pwd_id'] != $user_pwd_result[0]['id']) {
							// 使用了舊密碼登入，登入失敗
							$message_arr[] = array('error', '您已經在 ' . $user_pwd_result[0]['use_date'] . ' 將密碼變更，請確認密碼後再次嘗試。');
						} else {
							// 密碼正確，登入成功
							user_login($user_data);
						}
					}
				}
			} else {
				$message_arr[] = array('error', '請輸入 帳號/密碼');
			}
		}
	}

	if(check_login()) {
		include_once(WEB_PATH . '/lib/model.php');
		include_once(WEB_PATH . '/html/main.php');
	} else {
		include_once(WEB_PATH . '/html/message.php');
		include_once(WEB_PATH . '/html/login.php');
	}
?>
	</body>
</html>