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
					// 檢查get權限
					$user_data = check_login();
					if($user_id != $user_data['id'] && (!check_department_permission($user_result[0]['department_id']) || !check_position_permission($user_result[0]['position_id']))) {
						echo json_encode(array('status' => false, 'msg' => 'insufficient permissions', 'result' => null));
					} else {
						echo json_encode(array('status' => true, 'msg' => 'ok', 'result' => $user_result[0]));
					}
				}
			} else {
				echo json_encode(array('status' => false, 'msg' => 'user_id is missing', 'result' => null));
			}
			die;

		case 'ajax_get_stock_list': // 依自身權限取得全公司庫存異動列表(統計結果)
			include_once(WEB_PATH . '/lib/model.php');

			// 確認權限
			$user_data = check_login();
			if(empty($user_data['department']) || empty($user_data['department']['permission'])) {
				$department_permission = array();
			} else {
				$department_permission = explode(',', $user_data['department']['permission']);
			}
			if(empty($user_data['position']) || empty($user_data['position']['permission'])) {
				$position_permission = array();
			} else {
				$position_permission = explode(',', $user_data['position']['permission']);
			}

			if(empty($position_permission) || !in_array(EXECUTIVES_POSITION_ID, $position_permission)) {
				echo json_encode(array('status' => false, 'msg' => 'insufficient permissions', 'result' => null));
				die;
			}

			$where = 's.`user_id` = \'' . $user_data['id'] . '\'';
			if(!empty($department_permission)) {
				// 有部門權限 且職務權限包含業務主管
				$where = '(' . $where . ' OR (e.`department_id` IN (\'' . join('\', \'', $department_permission) . '\') AND e.`position_id` = ' . EXECUTIVES_POSITION_ID . '))';
			}
			// 設定期間
			$date_start = get_array($_POST, 'date_start');
			$date_end = get_array($_POST, 'date_end');
			if(empty($date_start)) {
				if(!empty($date_end)) {
					$date_end = date("Y-m-d 23:59:59", strtotime($date_end)); // sql injection 防範
					$where .= ' AND s.`create_date` <= \'' . $date_end . '\'';
				}
			} else {
				$date_start = date("Y-m-d 00:00:00", strtotime($date_start)); // sql injection 防範
				if(empty($date_end)) {
					$where .= ' AND s.`create_date` >= \'' . $date_start . '\'';
				} else {
					$date_end = date("Y-m-d 23:59:59", strtotime($date_end)); // sql injection 防範
					$where .= ' AND s.`create_date` >= \'' . $date_start . '\' AND s.`create_date` <= \'' . $date_end . '\'';
				}
			}
			$stock_result = model::query('SELECT SUM(CASE WHEN s.`stock_type` = 0 THEN s.`quantity` * s.`price` ELSE 0 END) AS `purchase_amount`, SUM(CASE WHEN s.`stock_type` = 1 THEN s.`quantity` * s.`price` ELSE 0 END) AS `sales_amount`, e.`id` AS `user_id`, e.`name` AS `user_name` FROM `stock_log` AS s 
				LEFT JOIN `user` AS u ON s.`user_id` = u.`id` 
				LEFT JOIN `department` AS d ON FIND_IN_SET(u.`department_id`, d.`permission`) 
				LEFT JOIN `position` AS p ON p.`permission` IS NOT NULL AND FIND_IN_SET(u.`position_id`, p.`permission`) 
				LEFT JOIN `user` AS e ON (d.`id` = e.`department_id` AND (p.`id` = e.`position_id` OR u.`id` = e.`id`)) 
				WHERE ' . $where . ' AND s.`disable_date` IS NULL GROUP BY e.`id`;');
			if(empty($stock_result)) {
				echo json_encode(array('status' => false, 'msg' => 'No records in the database', 'result' => null));
			} else {
				echo json_encode(array('status' => true, 'msg' => 'ok', 'result' => $stock_result));
			}
			die;

		case 'ajax_get_stock_list_for_executives_id': // 依業務主管id取得部門庫存異動列表(統計結果)
			$user_id = (int)get_array($_POST, 'user_id');
			$user_data = check_login();
			if(!$user_id) {
				// 未輸入 目標業務主管id，使用自己的id
				$user_id = $user_data['id'];
			}
			include_once(WEB_PATH . '/lib/model.php');

			// 驗證自身ajax權限，並取得目標業務主管權限
			if(empty($user_data['department']) || empty($user_data['department']['permission'])) {
				$self_department_permission = array();
			} else {
				$self_department_permission = explode(',', $user_data['department']['permission']);
			}
			if(empty($user_data['position']) || empty($user_data['position']['permission'])) {
				$self_position_permission = array();
			} else {
				$self_position_permission = explode(',', $user_data['position']['permission']);
			}
			if($user_id == $user_data['id']) {
				// 目標業務主管是自己，故套用自己的權限即可
				$user_result = array(
					array(
						'department_permission' => join(', ', $self_department_permission),
						'position_permission' => join(', ', $self_position_permission)
					)
				);
			} else {
				if(empty($self_department_permission) || empty($self_position_permission)) {
					// 權限不足
					echo json_encode(array('status' => false, 'msg' => 'insufficient permissions', 'result' => null));
					die;
				} else {
					// 有部門權限 + 有職務權限 = 有管理其他使用者的權限
					$user_result = model::query('SELECT d.`permission` AS `department_permission`, p.`permission` AS `position_permission` FROM `user` AS u LEFT JOIN `department` AS d ON u.`department_id` = d.`id` LEFT JOIN `position` AS p ON u.`position_id` = p.`id` WHERE u.`id` = \'' . $user_id . '\' AND u.`department_id` IN (\'' . join('\', \'', $self_department_permission) . '\') AND u.`position_id` IN (\'' . join('\', \'', $self_position_permission) . '\');');
				}
			}

			if(empty($user_result)) {
				// 權限不足或查無此業務主管
				echo json_encode(array('status' => false, 'msg' => 'insufficient permissions', 'result' => null));
				die;
			} else {
				// 依目標業務主管權限，取得部門庫存異動列表(統計結果)
				$where = 's.`user_id` = \'' . $user_id . '\'';
				if(!empty($user_result[0]['department_permission']) && !empty($user_result[0]['position_permission'])) {
					// 有部門權限 + 有職務權限 = 有管理其他使用者的權限
					$where = '(' . $where . ' OR (u.`department_id` IN (\'' . $user_result[0]['department_permission'] . '\') AND u.`position_id` IN (\'' . $user_result[0]['position_permission'] . '\')))';
				}
				// 設定期間
				$date_start = get_array($_POST, 'date_start');
				$date_end = get_array($_POST, 'date_end');
				if(empty($date_start)) {
					if(!empty($date_end)) {
						$date_end = date("Y-m-d 23:59:59", strtotime($date_end)); // sql injection 防範
						$where .= ' AND s.`create_date` <= \'' . $date_end . '\'';
					}
				} else {
					$date_start = date("Y-m-d 00:00:00", strtotime($date_start)); // sql injection 防範
					if(empty($date_end)) {
						$where .= ' AND s.`create_date` >= \'' . $date_start . '\'';
					} else {
						$date_end = date("Y-m-d 23:59:59", strtotime($date_end)); // sql injection 防範
						$where .= ' AND s.`create_date` >= \'' . $date_start . '\' AND s.`create_date` <= \'' . $date_end . '\'';
					}
				}
				$stock_result = model::query('SELECT SUM(CASE WHEN s.`stock_type` = 0 THEN s.`quantity` * s.`price` ELSE 0 END) AS `purchase_amount`, SUM(CASE WHEN s.`stock_type` = 1 THEN s.`quantity` * s.`price` ELSE 0 END) AS `sales_amount`, u.`id` AS `user_id`, u.`name` AS `user_name` FROM `stock_log` AS s LEFT JOIN `user` AS u ON s.`user_id` = u.`id` WHERE ' . $where . ' AND s.`disable_date` IS NULL GROUP BY s.`user_id`;');
				if(empty($stock_result)) {
					echo json_encode(array('status' => false, 'msg' => 'No records in the database', 'result' => null));
				} else {
					echo json_encode(array('status' => true, 'msg' => 'ok', 'result' => $stock_result));
				}
			}
			die;

		case 'ajax_get_stock_detail_for_user_id': // 依user_id取得庫存異動詳細清單
			$user_id = (int)get_array($_POST, 'user_id');
			$user_data = check_login();
			if(!$user_id) {
				// 未輸入 目標使用者id，使用自己的id
				$user_id = $user_data['id'];
			}
			include_once(WEB_PATH . '/lib/model.php');

			// 檢查get權限
			$user_result = model::query('SELECT `department_id`, `position_id` FROM `user` WHERE `id` = ' . $user_id . ';');
			if(empty($user_result)) {
				echo json_encode(array('status' => false, 'msg' => 'No records in the database', 'result' => null));
				die;
			} else {
				$user_target_data = $user_result[0];
				if($user_id != $user_data['id'] && (!check_department_permission($user_result[0]['department_id']) || !check_position_permission($user_result[0]['position_id']))) {
					echo json_encode(array('status' => false, 'msg' => 'insufficient permissions', 'result' => null));
					die;
				}
			}

			$where = '';
			// 設定期間
			$date_start = get_array($_POST, 'date_start');
			$date_end = get_array($_POST, 'date_end');
			if(empty($date_start)) {
				if(!empty($date_end)) {
					$date_end = date("Y-m-d 23:59:59", strtotime($date_end)); // sql injection 防範
					$where .= ' AND s.`create_date` <= \'' . $date_end . '\'';
				}
			} else {
				$date_start = date("Y-m-d 00:00:00", strtotime($date_start)); // sql injection 防範
				if(empty($date_end)) {
					$where .= ' AND s.`create_date` >= \'' . $date_start . '\'';
				} else {
					$date_end = date("Y-m-d 23:59:59", strtotime($date_end)); // sql injection 防範
					$where .= ' AND s.`create_date` >= \'' . $date_start . '\' AND s.`create_date` <= \'' . $date_end . '\'';
				}
			}
			$stock_result = model::query('SELECT s.`stock_type`, s.`price`, s.`quantity`, p.`name` AS `product_name` FROM `stock_log` AS s LEFT JOIN `product` AS p ON s.`product_id` = p.`id` WHERE s.`user_id` = \'' . $user_id . '\'' . $where . ' AND s.`disable_date` IS NULL;');
			if(empty($stock_result)) {
				echo json_encode(array('status' => false, 'msg' => 'No records in the database', 'result' => null));
			} else {
				echo json_encode(array('status' => true, 'msg' => 'ok', 'result' => $stock_result));
			}
			die;

		case 'ajax_get_product_data':
			$product_id = (int)get_array($_POST, 'product_id');
			if($product_id) {
				include_once(WEB_PATH . '/lib/model.php');

				// 檢查get權限 - 商品共用故不需要檢查

				$product_result = model::query('SELECT `id`, `name`, `price`, `quantity`, `disable_date` FROM `product` WHERE `id` = ' . $product_id . ';');
				if(empty($product_result)) {
					echo json_encode(array('status' => false, 'msg' => 'No records in the database', 'result' => null));
				} else {
					echo json_encode(array('status' => true, 'msg' => 'ok', 'result' => $product_result[0]));
				}
			} else {
				echo json_encode(array('status' => false, 'msg' => 'product_id is missing', 'result' => null));
			}
			die;

		default:
			break;
	}
?>