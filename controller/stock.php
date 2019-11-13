<?php
	if(!isset($message_arr)) $message_arr = array();

	$mode = get_array($_POST, 'mode');

	if($mode == 'stock_add') {
		// 檢查欄位輸入
		$stock_type = get_array($_POST, 'stock_type');
		$product_id = get_array($_POST, 'product_id');
		$quantity = (int)@get_array($_POST, 'quantity');

		// test 新增庫存異動紀錄 未完成
	}
?>