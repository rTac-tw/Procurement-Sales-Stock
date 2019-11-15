<?php
	if(!isset($message_arr)) $message_arr = array();

	$mode = get_array($_POST, 'mode');

	if($mode == 'user_add' || $mode == 'user_edit') {
		$user_controller_error = false;
		$user_controller_pwd_change = false;

		// 檢查欄位輸入
		$account = get_array($_POST, 'account');
		if(empty($account)) {
			$message_arr[] = array('error', '帳號 欄位必填，請輸入');
			$user_controller_error = true;
		} else {
			// 檢查帳號是否發生重複
			if($mode == 'user_edit') {
				// 修改的檢查須排除自己
				$user_id = (int)@get_array($_POST, 'user_id');
				if(empty($user_id)) {
					$message_arr[] = array('error', '未預期錯誤，請重新操作');
					$user_controller_error = true;
				}

				$user_result = model::query('SELECT `id` FROM `user` WHERE `account` = \'' . $account . '\' AND `id` != ' . $user_id .';'); // 未做 sql injection 防範 !
			} else {
				$user_result = model::query('SELECT `id` FROM `user` WHERE `account` = \'' . $account . '\';'); // 未做 sql injection 防範 !
			}
			if(!empty($user_result)) {
				$message_arr[] = array('error', $account . ' 帳號已經存在，請嘗試使用其他帳號');
				$account = '';
				$user_controller_error = true;
			}

			unset($user_result);
		}
		$new_pwd = get_array($_POST, 'new_pwd');
		$pwd_check = get_array($_POST, 'pwd_check');
		if($new_pwd != $pwd_check) {
			$message_arr[] = array('error', '新密碼驗證錯誤，本次儲存將不會修改密碼');
		} else if(!empty($new_pwd)) {
			$user_controller_pwd_change = true; // 需要修改密碼
		}
		$name = get_array($_POST, 'name');
		if(empty($name)) {
			$message_arr[] = array('error', '姓名 欄位必填，請輸入');
			$user_controller_error = true;
		}
		$gender = (int)@get_array($_POST, 'gender');
		$department_id = (int)@get_array($_POST, 'department_id');
		if(empty($department_id)) {
			$message_arr[] = array('error', '部門 欄位必填，請選擇');
			$user_controller_error = true;
		}
		$position_id = (int)@get_array($_POST, 'position_id');
		if(empty($position_id)) {
			$message_arr[] = array('error', '職務 欄位必填，請選擇');
			$user_controller_error = true;
		}

		// 檢查save權限 - 設定 目標 部門/職位
		$user_data = check_login();
		if(!empty($department_id) && !empty($position_id) && !check_department_permission($department_id) || ($user_id != $user_data['id'] && !check_position_permission($position_id))) {
			$message_arr[] = array('error', ($mode == 'user_add'?'新增':'修改') . ' 權限不足，請聯絡資訊部門');
			$user_controller_error = true;
			$user_controller_pwd_change = false;
		}

		// 檢查邏輯: 一個部門不會有兩位業務主管
		if($position_id == EXECUTIVES_POSITION_ID) {
			$where = '';
			if($mode != 'user_add' && !empty($user_id)) {
				$where = ' AND `id` != ' . $user_id; // 修改時 判斷條件排除目標使用者自己
			}
			$user_result = model::query('SELECT `id` FROM `user` WHERE `department_id` = ' . $department_id . ' AND `position_id` = ' . EXECUTIVES_POSITION_ID . $where . ' AND `use_date` IS NOT NULL AND `disable_date` IS NULL;');
			if(!empty($user_result)) {
				$message_arr[] = array('error', '同一個部門只能有一位業務主管');
				$user_controller_error = true;
			}
		}

		if(!$user_controller_error) {
			// 執行儲存
			$datetime_now = date("Y-m-d H:i:s");
			if($mode == 'user_add') {
				// 新增
				// test 因到職設定尚未，故新增使用者直接到職
				$user_result = model::query('INSERT INTO `user` (`account`, `name`, `gender`, `department_id`, `position_id`, `use_date`, `disable_date`, `create_date`, `edit_date`) VALUES (\'' . $account . '\', \'' . $name . '\', \'' . $gender . '\', \'' . $department_id . '\', \'' . $position_id . '\', \'' . $datetime_now . '\', NULL, \'' . $datetime_now . '\', \'' . $datetime_now . '\');'); // 未做 sql injection 防範 !
				if(empty($user_result)) {
					$message_arr[] = array('error', '新增 ' . $name . ' 使用者失敗，請聯絡資訊部門');
				} else {
					// 新增成功
					$user_id = model::get_insert_id();
					if(!$user_controller_pwd_change) {
						$message_arr[] = array('info', '使用者 ' . $name . ' 已新增，但未設定密碼。請另行設定密碼');
					} else {
						$message_arr[] = array('success', '使用者 ' . $name . '，已新增');
					}
				}
			} else {
				// 檢查save權限 - 設定 使用者 原 部門/職位
				$user_result = model::query('SELECT `name`, `department_id`, `position_id` FROM `user` WHERE `id` = ' . $user_id . ';');
				if(empty($user_result)) {
					$message_arr[] = array('error', '修改 ' . $name . ' 使用者失敗，此帳號並不存在');
					$user_controller_error = true;
					$user_controller_pwd_change = false;
				} else {
					$user_target_data = $user_result[0];
					if(!check_department_permission($user_target_data['department_id']) || ($user_id != $user_data['id'] && !check_position_permission($user_target_data['position_id']))) {
						$message_arr[] = array('error', '修改 權限不足，請聯絡資訊部門');
						$user_controller_error = true;
						$user_controller_pwd_change = false;
					}
				}

				if(!$user_controller_error) {
					// 修改
					$user_result = model::query('UPDATE `user` SET `account` = \'' . $account . '\', `name` = \'' . $name . '\', `gender` = \'' . $gender . '\', `department_id` = \'' . $department_id . '\', `position_id` = \'' . $position_id . '\', `edit_date` = \'' . $datetime_now . '\' WHERE `id` = ' . $user_id);
					if(empty($user_result)) {
						$message_arr[] = array('error', '修改 ' . $name . ($name==$user_target_data['name']?'':'(原: ' . $user_target_data['name'] . ')') . ' 使用者失敗，請聯絡資訊部門');
					} else {
						$message_arr[] = array('success', '修改使用者 ' . $name . ($name==$user_target_data['name']?'':'(原: ' . $user_target_data['name'] . ')') . '，完成');
					}
				}
			}
		}

		// 修改密碼
		if(empty($user_id)) $user_controller_pwd_change = false;
		if($user_controller_pwd_change) {
			$new_pwd = md5($new_pwd);
			// 檢查是否與目前使用密碼相同
			$user_pwd_result = model::query('SELECT `id`, `password` FROM `user_pwd` WHERE `user_id` = \''. $user_id . '\' ORDER BY `use_date` DESC LIMIT 1;');
			if(!empty($user_pwd_result)) {
				if($user_pwd_result[0]['password'] == $new_pwd) {
					// 新密碼與當前密碼相同，不需要修改
					$user_controller_pwd_change = false;
				} else {
					// 搜尋舊密碼
					$user_pwd_result = model::query('SELECT `id` FROM `user_pwd` WHERE `user_id` = \''. $user_id . '\' AND `password` = \'' . $new_pwd . '\';');
					if(!empty($user_pwd_result)) {
						// 發現相符的舊密碼，刷新舊密碼啟用時間
						$user_pwd_result = model::query('UPDATE `user_pwd` SET `use_date` = \'' . $datetime_now . '\', `edit_date` = \'' . $datetime_now . '\' WHERE `id` = ' . $user_pwd_result[0]['id'] . ';');
						$message_arr[] = array('success', '使用者 ' . $name . '，新密碼已設定');
						$user_controller_pwd_change = false;
					}
				}
			}
		}
		if($user_controller_pwd_change) {
			// 沒有符合的舊密碼，新增一組密碼
			$user_pwd_result = model::query('INSERT INTO `user_pwd` (`user_id`, `password`, `use_date`, `create_date`, `edit_date`) VALUES (\''. $user_id . '\', \''. $new_pwd . '\', \''. $datetime_now . '\', \''. $datetime_now . '\', \''. $datetime_now . '\');');
			$message_arr[] = array('success', '使用者 ' . $name . '，新密碼已設定');
		}

		unset($user_controller_error);
		unset($user_controller_pwd_change);
	}
?>