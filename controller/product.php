<?php
	if(!isset($message_arr)) $message_arr = array();

	$mode = get_array($_POST, 'mode');

	if($mode == 'product_add' || $mode == 'product_edit') {
		$product_controller_error = false;

		// 檢查欄位輸入
		$name = get_array($_POST, 'name');
		if(empty($name)) {
			$message_arr[] = array('error', '商品名稱 欄位必填，請輸入');
			$product_controller_error = true;
		}
		$price = (int)@get_array($_POST, 'price');
		if(empty($price)) {
			$message_arr[] = array('error', '價格 欄位必填，請輸入');
			$product_controller_error = true;
		}
		if($mode == 'product_edit') {
			$product_id = (int)@get_array($_POST, 'product_id');
			if(empty($product_id)) {
				$message_arr[] = array('error', '未預期錯誤，請重新操作');
				$product_controller_error = true;
			}
		}

		// 檢查save權限 - 商品共用故不需要檢查

		if(!$product_controller_error) {
			// 執行儲存
			$datetime_now = date("Y-m-d H:i:s");
			if($mode == 'product_add') {
				// 新增
				$product_result = model::query('INSERT INTO `product` (`name`, `price`, `quantity`, `disable_date`, `create_date`, `edit_date`) VALUES (\'' . $name . '\', \'' . $price . '\', 0, NULL, \'' . $datetime_now . '\', \'' . $datetime_now . '\');'); // 未做 sql injection 防範 !
				if(empty($product_result)) {
					$message_arr[] = array('error', '新增 ' . $name . ' 商品失敗，請聯絡資訊部門');
				} else {
					// 新增成功
					$message_arr[] = array('success', '商品 ' . $name . '，已新增');
				}
			} else {
				// 檢查save權限 - 商品共用故不需要檢查

				// 修改
				$product_result = model::query('UPDATE `product` SET `name` = \'' . $name . '\', `price` = \'' . $price . '\', `edit_date` = \'' . $datetime_now . '\' WHERE `id` = ' . $product_id);
				if(empty($product_result)) {
					$message_arr[] = array('error', '修改 ' . $name . ' 商品失敗，請聯絡資訊部門');
				} else {
					$message_arr[] = array('success', '修改商品 ' . $name . '，完成');
				}
			}
		}
	}
?>