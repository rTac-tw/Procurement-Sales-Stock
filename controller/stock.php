<?php
	if(!isset($message_arr)) $message_arr = array();

	$mode = get_array($_POST, 'mode');
	$user_data = check_login();

	if($mode == 'stock_add' && !empty($user_data['id'])) {
		$stock_controller_error = false;

		// 檢查欄位輸入
		$stock_type = get_array($_POST, 'stock_type');
		if(!empty($stock_type)) {
			if($stock_type == 'P') {
				$stock_type = 0; // 進
			} else if($stock_type == 'S') {
				$stock_type = 1; // 銷
			} else {
				$message_arr[] = array('error', '貨單類型 欄位必填，請選擇');
				$stock_controller_error = true;
			}
		} else {
			$message_arr[] = array('error', '貨單類型 欄位必填，請選擇');
			$stock_controller_error = true;
		}
		$product_id = (int)@get_array($_POST, 'product_id');
		if(empty($product_id)) {
			$message_arr[] = array('error', '商品 欄位必填，請選擇');
			$stock_controller_error = true;
		}
		$quantity = (int)@get_array($_POST, 'quantity');
		if(empty($quantity)) {
			$message_arr[] = array('error', '數量 欄位必填，請輸入');
			$stock_controller_error = true;
		}

		if(!$stock_controller_error) {
			// 執行儲存
			$datetime_now = date("Y-m-d H:i:s");

			// 嘗試異動庫存
			if(($stock_type == 0 && $quantity >= 0) || ($stock_type == 1 && $quantity < 0)) {
				$product_result = model::query('UPDATE `product` SET `quantity` = `quantity` + ' . abs($quantity) . ', `edit_date` = \'' . $datetime_now . '\' WHERE `id` = ' . $product_id . ';');
			} else {
				$product_result = model::query('UPDATE `product` SET `quantity` = `quantity` - ' . abs($quantity) . ', `edit_date` = \'' . $datetime_now . '\' WHERE `id` = ' . $product_id . ' AND `quantity` > ' . abs($quantity) . ';');
			}

			if($product_result !== false && model::get_affected_rows()) {
				// 庫存異動成功
				$stock_result = model::query('INSERT INTO `stock_log` (`user_id`, `stock_type`, `product_id`, `quantity`, `disable_date`, `create_date`, `edit_date`) VALUES (' . $user_data['id'] . ', ' . $stock_type . ', ' . $product_id . ', ' . $quantity . ', NULL, \'' . $datetime_now . '\', \'' . $datetime_now . '\');');
				if(empty($stock_result)) {
					$message_arr[] = array('error', '新增 [' . ($stock_type==0?'進':'銷') . ']貨單 失敗，請聯絡資訊部門');
				} else {
					// 新增成功
					$user_id = model::get_insert_id();
					$message_arr[] = array('success', ' [' . ($stock_type==0?'進':'銷') . ']貨單，已新增');
				}
			} else {
				$message_arr[] = array('error', '庫存錯誤');
			}
		}
	}
?>