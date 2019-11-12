<?php
	switch (get_array($_POST, 'mode')) {
		case 'ajax_get_user_data': // 取指定 user_id 的使用者資料
			$user_id = (int)get_array($_POST, 'user_id');
			if($user_id) {
				include_once(WEB_PATH . '/lib/model.php');
				$user_result = model::query('SELECT u.`id`, u.`account`, u.`name`, u.`gender`, u.`department_id`, u.`position_id`, p.`name` AS `position`, u.`use_date`, u.`disable_date` FROM `user` AS u LEFT JOIN `position` AS p ON u.`position_id` = p.`id` WHERE u.`id` = \'' . $user_id . '\';');
				if(empty($user_result)) {
					echo json_encode(array('status' => false, 'msg' => 'No records in the database', 'result' => null));
				} else {
					echo json_encode(array('status' => true, 'msg' => 'ok', 'result' => $user_result[0]));
				}
			} else {
				echo json_encode(array('status' => false, 'msg' => 'user_id is missing', 'result' => null));
			}
			die;

		default:
			break;
	}
?>