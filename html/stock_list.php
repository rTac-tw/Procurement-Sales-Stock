<h3>進銷查詢</h3>
<?php
	$user_data = check_login();
?>
<a href="javascript:void(0);" id="stock_form_add">新增[進/銷]貨單</a>

<style type="text/css">
	.search_bar {
		width: 100%;
	}
	#show_title_text {
		float: right;
	}
	#level_back {
		float: right;
		display: none;
	}
</style>
<div class="search_bar">
	<input type="text" id="datepicker_start"> ~ <input type="text" id="datepicker_end">
	<input type="button" id="search_btn" value="搜尋">
	<span id="show_title_text"></span>
	<a href="javascript:void(0)" id="level_back">&nbsp;返回&nbsp;</a>
</div>
<script type="text/javascript">
	var search_bar = (function() {
		var _ = {
			def_position : '<?php echo $user_data['position_id']; ?>',
			def_user_name : '<?php echo $user_data['name']; ?>',
			level_title_text_arr : [],
			level_id_arr : [],
			level_min : 2, // 預設 較低層級
			level_now : 2,
			level_min_cache_data : {},
		};
		var _init = function() {
			// https://api.jqueryui.com/datepicker/
			$('#datepicker_start').datepicker({dateFormat: 'yy-mm-dd'});
			$('#datepicker_end').datepicker({dateFormat: 'yy-mm-dd'});

			$('#level_back').off('click').on('click', function() {
				_back();
			});

			$('#search_btn').off('click').on('click', function() {
				_load(_.level_title_text_arr[_.level_now], _.level_id_arr[_.level_now]);
			});

			// test 職務判斷為 hard-code 對應，待修正
			switch(_.def_position) {
				case '1': // 業務主管
					_.level_now = 1;
					_.level_min = 1;
					break;
				case '2': // 業務
					_.level_now = 2;
					_.level_min = 2;
					break;
				case '3': // 會計
					_.level_now = 0;
					_.level_min = 0;
					break;
				default:
			}
			_load(_.def_user_name);
		};
		var _next = function(title_text, user_id) {
			_.level_now++;
			_load(title_text, user_id);
		};
		var _back = function() {
			switch(_.level_now) {
				case 1:
					_.level_now = 0;
					_title_text('公司');
					stock_list.make(_.level_min_cache_data);
					break;
				case 2:
					_.level_now = 1;
					_title_text(_.level_title_text_arr[_.level_now]);
					stock_detail.hide(); // 外部物件，關閉 #stock_detail
					stock_list.show(); // 外部物件，顯示 #stock_list
					break;
				default:
			}
			if(_.level_now > _.level_min) {
				$('#level_back').show();
			} else {
				$('#level_back').hide();
			}
		};
		var _load = function(title_text, user_id) {
			if(!user_id) user_id = '';
			_.level_id_arr[_.level_now] = user_id;
			_.level_title_text_arr[_.level_now] = title_text;
			switch(_.level_now) {
				case 0: // 全公司層
					_title_text('公司');
					_process.get_stock_list(); // 取得全公司庫存異動列表(統計結果)
					break;
				case 1: // 業務主管層
					_title_text(title_text);
					_process.get_stock_list_for_executives_id(user_id); // 取得部門庫存異動列表(統計結果)
					break;
				case 2: // 業務層
					_title_text(title_text + ' -- 明細');
					_process.get_stock_detail_for_user_id(user_id); // 取得庫存異動詳細清單
					break;
				default:
			}
			if(_.level_now > _.level_min) {
				$('#level_back').show();
			} else {
				$('#level_back').hide();
			}
		}

		var _title_text = function(text) {
			$('#show_title_text').text('[ ' + text + ' ]');
		};
		var _process = {
			get_stock_list : function() {
				var post_data = {mode:'ajax_get_stock_list'};

				// 設定期間
				var date_tmp = $('#datepicker_start').val();
				if(date_tmp) post_data.date_start = date_tmp;
				date_tmp = $('#datepicker_end').val();
				if(date_tmp) post_data.date_end = date_tmp;

				$.post(document.URL, post_data, function(rep) {
					if(typeof rep.status != 'undefined') {
						if(!rep.status) {
							stock_list.make([]);
							console.log(rep.msg);
						} else {
							_.level_min_cache_data = rep.result;
							stock_list.make(rep.result);
						}
					} else {
						stock_list.make([]);
					}
				}, 'json');
			},
			get_stock_list_for_executives_id : function(user_id) {
				var post_data = {mode:'ajax_get_stock_list_for_executives_id'};
				if(user_id) post_data.user_id = user_id;

				// 設定期間
				var date_tmp = $('#datepicker_start').val();
				if(date_tmp) post_data.date_start = date_tmp;
				date_tmp = $('#datepicker_end').val();
				if(date_tmp) post_data.date_end = date_tmp;

				$.post(document.URL, post_data, function(rep) {
					if(typeof rep.status != 'undefined') {
						if(!rep.status) {
							stock_list.make([]);
							console.log(rep.msg);
						} else {
							stock_list.make(rep.result);
						}
					} else {
						stock_list.make([]);
					}
				}, 'json');
			},
			get_stock_detail_for_user_id : function(user_id) {
				var post_data = {mode:'ajax_get_stock_detail_for_user_id'};
				if(user_id) post_data.user_id = user_id;

				// 設定期間
				var date_tmp = $('#datepicker_start').val();
				if(date_tmp) post_data.date_start = date_tmp;
				date_tmp = $('#datepicker_end').val();
				if(date_tmp) post_data.date_end = date_tmp;

				$.post(document.URL, post_data, function(rep) {
					if(typeof rep.status != 'undefined') {
						if(!rep.status) {
							stock_detail.make([]);
							console.log(rep.msg);
						} else {
							stock_detail.make(rep.result);
						}
					} else {
						stock_detail.make([]);
					}
				}, 'json');
			},
		};
		return {
			init : _init,
			next : _next,
		};
	})();
</script>

<!-- 進銷查詢 第一層 - 全公司庫存異動列表(統計結果) + 第二層 - 部門庫存異動列表(統計結果) -->
<style type="text/css">
	#stock_list {
		display: none;
	}
	#stock_list_table td {
		text-align: center;
	}
	/*
	.self_tag {
		color: #800;
		font-weight:bold;
	}
	*/
</style>
<div id="stock_list">
	<table width="100%" id="stock_list_table">
		<thead>
			<tr>
				<th>人員</th>
				<th>進貨</th>
				<th>銷貨</th>
				<th>小計</th>
			</tr>
		</thead>
		<tbody>
			<tr id="stock_list_no_records">
				<td colspan="4">查無紀錄</td>
			</tr>
			<tr id="stock_list_template">
				<td>[<a href="javascript:void(0);" id="stock_list_user_name"></a>]</td>
				<td>[ <sapn id="stock_list_purchase_amount"></sapn> ]</td>
				<td>[ <sapn id="stock_list_sales_amount"></sapn> ]</td>
				<td>[ <sapn id="stock_list_subtotal"></sapn> ]</td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	var stock_list = (function() {
		var _ = {
			stock_list_no_records : '',
			stock_list_tr : '',
		};
		var _init = function() {
			_.stock_list_no_records = $('#stock_list_no_records').html();
			_.stock_list_tr = $('#stock_list_template').html();
		};
		var _make = function(data) {
			$('#stock_list_table tbody tr').remove();

			if($.isArray(data) && data.length > 0) {
				for(var i = 0; i < data.length; i++) {
					var tr = $('<tr>' + _.stock_list_tr + '</tr>');
					tr.find('#stock_list_user_name').text(data[i].user_name).data('user_id', data[i].user_id);
					tr.find('#stock_list_purchase_amount').text(data[i].purchase_amount);
					tr.find('#stock_list_sales_amount').text(data[i].sales_amount);
					tr.find('#stock_list_subtotal').text(data[i].sales_amount - data[i].purchase_amount);

					$('#stock_list_table tbody').append(tr);
				}
			} else {
				$('#stock_list_table tbody').append('<tr>' + _.stock_list_no_records + '</tr>');
			}
			_event();
			_show();
		};
		var _show = function() {
			$('#stock_list').show();
		};
		var _hide = function() {
			$('#stock_list').hide();
		};
		var _event = function() {
			$('#stock_list #stock_list_user_name').off('click').on('click', function() {
				var user_id = $(this).data('user_id');
				var user_name = $(this).text();
				search_bar.next(user_name, user_id); // 外部物件，於 search_bar 顯示下層
			});
		};
		return {
			init : _init,
			make : _make,
			show : _show,
			hide : _hide,
		};
	})();
	stock_list.init();
</script>
<!-- 進銷查詢 第一層 + 第二層 End -->

<!-- 進銷查詢 第三層 - 庫存異動詳細清單 -->
<style type="text/css">
	#stock_detail {
		display: none;
	}
	#stock_detail_table td {
		text-align: center;
	}
</style>
<div id="stock_detail">
	<table width="100%" id="stock_detail_table">
		<thead>
			<tr>
				<th>產品</th>
				<th>進出</th>
				<th>當下價格</th>
				<th>數量</th>
				<th>貨單類型</th>
			</tr>
		</thead>
		<tbody>
			<tr id="stock_detail_no_records">
				<td colspan="3">查無紀錄</td>
			</tr>
			<tr id="stock_detail_template">
				<td>[<span id="stock_detail_product_name"></span>]</td>
				<td>[<span id="stock_detail_amount"></span>]</td>
				<td>[<span id="stock_detail_price"></span>]</td>
				<td>[<span id="stock_detail_quantity"></span>]</td>
				<td>[<span id="stock_detail_stock_type"></span>]</td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	var stock_detail = (function() {
		var _ = {};
		var _init = function() {
			_.stock_detail_no_records = $('#stock_detail_no_records').html();
			_.stock_detail_tr = $('#stock_detail_template').html();
		};
		var _make = function(data) {
			$('#stock_detail_table tbody tr').remove();

			if($.isArray(data) && data.length > 0) {
				var amount_symbol = '';
				for(var i = 0; i < data.length; i++) {
					var tr = $('<tr>' + _.stock_detail_tr + '</tr>');
					tr.find('#stock_detail_product_name').text(data[i].product_name);
					if(data[i].stock_type == 0) {
						if(data[i].quantity > 0)
							amount_symbol = '+ ';
						else
							amount_symbol = '- ';
						tr.find('#stock_detail_stock_type').text('進貨');
					} else {
						if(data[i].quantity > 0)
							amount_symbol = '- ';
						else
							amount_symbol = '+ ';
						tr.find('#stock_detail_stock_type').text('銷貨');
					}
					data[i].quantity = Math.abs(data[i].quantity);
					tr.find('#stock_detail_amount').text(amount_symbol + (data[i].price * data[i].quantity));
					tr.find('#stock_detail_price').text(data[i].price);
					tr.find('#stock_detail_quantity').text(amount_symbol + data[i].quantity);

					$('#stock_detail_table tbody').append(tr);
				}
			} else {
				$('#stock_list_table tbody').append('<tr>' + _.stock_list_no_records + '</tr>');
			}

			$('#stock_detail').show();
			stock_list.hide(); // 外部物件，關閉 #stock_list
		};
		var _hide = function() {
			$('#stock_detail').hide();
		};
		var _process = {
		};
		return {
			init : _init,
			make : _make,
			hide : _hide,
		};
	})();
	stock_detail.init();
</script>
<!-- 進銷查詢 第三層 End -->

<!-- 登入者執行動作 -->
<script type="text/javascript">
	search_bar.init();
</script>
<!-- 登入者執行動作 End -->

<!-- 進銷登打 -->
<?php
	$product_result = model::query('SELECT `id`, `name`, `price`, `quantity`, `disable_date` FROM `product`;');
	$product = array();
	foreach($product_result as $val) {
		$product[$val['id']] = $val['name'] . ' [單價: ' . $val['price'] . '] [庫存: ' . $val['quantity'] . ']' . (empty($val['disable_date']?'':' (停用)'));
	}
	unset($product_result);
?>
<style type="text/css">
	.stock_form {
		position: fixed;
		top: 0;
		left: 0;
		display: none;
		width: 100vw;
		height: 100vh;
		background-color: rgba(192,192,192,0.7);
	}
	.stock_form_content {
		position: absolute;
		left: 50%;
		top: 50%;
		width: 620px;
		height: 620px;
		margin-left: -350px;
		margin-top: -350px;
		background-color: #fff;

		padding: 40px;
	}
	.stock_form_content label {
		margin: 5px 10px; 
	}
	.required_tag {
		color: #f00;
	}
</style>
<div class="stock_form">
	<a href="javascript:void(0);" id="stock_form_close" style="float: right; padding: 10px 10px; background-color: #ddd;">關閉</a>
	<div class="stock_form_content">
		<form method="POST">
			<input type="hidden" name="mode" id="stock_form_mode" value="">
			<!-- <input type="hidden" name="stock_id" id="stock_form_stock_id" value=""> -->
			<p>
				<label>貨單類型<span class="required_tag" title="必填">*</span></label>
				<select name="stock_type" id="stock_form_stock_type">
					<option>未選擇</option>
					<option value="P">進貨</option>
					<option value="S">銷貨</option>
				</select>
			</p>
			<p>
				<label>商品<span class="required_tag" title="必填">*</span></label>
				<select name="product_id" id="stock_form_product_id">
					<option>未選擇</option>
<?php
	foreach($product as $key=>$val) {
?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php
	}
	unset($product);
?>
				</select>
			</p>
			<p><label>數量<span class="required_tag" title="必填">*</span></label><input type="text" name="quantity" id="stock_form_quantity"></p>
			<p><label>狀態</label><span id="stock_form_ststus"></span></p>
			<p><input type="submit" value="送出"></p>
		</form>
	</div>
</div>

<script type="text/javascript">
	// 新增 [進/銷]貨單
	$('#stock_form_add').off('click').on('click', function() {
		// 清空
		$('.stock_form select').prop('selectedIndex', 0);
		$('.stock_form input[type=text]').val('');
		$('#stock_form_ststus').html('');

		$('#stock_form_mode').val('stock_add');
		$('.stock_form').show(); // 顯示 form
	});

	// 關閉 form
	$('#stock_form_close').off('click').on('click', function() {
		$('.stock_form').hide();
	});
</script>
<!-- 進銷登打 End -->