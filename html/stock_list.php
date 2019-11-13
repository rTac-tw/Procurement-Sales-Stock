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
	<span id="show_title_text"></span>
	<a href="javascript:void(0)" id="level_back">&nbsp;返回&nbsp;</a>
</div>
<script type="text/javascript">
	var search_bar = (function() {
		var _init = function() {
			// https://api.jqueryui.com/datepicker/
			$.datepicker.formatDate("yy-mm-dd");
			$('#datepicker_start').datepicker();
			$('#datepicker_end').datepicker();

			$('#level_back').off('click').on('click', function() {
				stock_list.level_back(); // 外部物件，stock_list 返回
			});
		};
		var _title_text = function(text) {
			$('#show_title_text').text('[ ' + text + ' ]');
		};
		var _level_show = function() {
			$('#level_back').show();
		};
		var _level_hide = function() {
			$('#level_back').hide();
		};
		return {
			init : _init,
			title_text : _title_text,
			level_show : _level_show,
			level_hide : _level_hide,
		};
	})();
	search_bar.init();
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
				<td>[ <sapn id="stock_list_purchase_qty"></sapn> ]</td>
				<td>[ <sapn id="stock_list_sales_qty"></sapn> ]</td>
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
			level_min : 1, // 預設 較低層級
			top_level_cache : {},
			title_text : [],
			level_now : 1,
		};
		var _init = function(level, title_text) {
			_.stock_list_no_records = $('#stock_list_no_records').html();
			_.stock_list_tr = $('#stock_list_template').html();

			_.level_now = _.level_min = level;

			_.title_text[_.level_now] = title_text;
			if(_.level_min == 0) {
				_process.get_stock_list(); // 取得全公司庫存異動列表(統計結果)
			} else {
				_load(); // 取得部門庫存異動列表(統計結果)
			}
		};
		var _load = function(user_id) {
			_process.get_stock_list_for_executives_id(user_id); // 取得部門庫存異動列表(統計結果)
		};
		var _make = function(data) {
			$('#stock_list_table tbody tr').remove();
			search_bar.title_text(_.title_text[_.level_now]);

			if($.isArray(data) && data.length > 0) {
				for(var i = 0; i < data.length; i++) {
					var tr = $('<tr>' + _.stock_list_tr + '</tr>');
					tr.find('#stock_list_user_name').text(data[i].user_name).data('user_id', data[i].user_id);
					tr.find('#stock_list_purchase_qty').text(data[i].purchase_qty);
					tr.find('#stock_list_sales_qty').text(data[i].sales_qty);
					tr.find('#stock_list_subtotal').text(data[i].sales_qty - data[i].purchase_qty);

					$('#stock_list_table tbody').append(tr);
				}
			} else {
				$('#stock_list_table tbody').append('<tr>' + _.stock_list_no_records + '</tr>');
			}
			_event();
			if(_.level_now == _.level_min) {
				search_bar.level_hide(); // 外部物件，於公用 search_bar 隱藏 返回 按鈕
			} else {
				search_bar.level_show(); // 外部物件，於公用 search_bar 顯示 返回 按鈕
			}
			stock_detail.hide(); // 外部物件，關閉 #stock_detail
			$('#stock_list').show();
		};
		var _level_back = function() {
			switch(_.level_now) {
				case 1:
					_.level_now = 0;
					_make(_.top_level_cache);
					break;
				case 2:
					_.level_now = 1;
					if(_.level_now == _.level_min) {
						search_bar.level_hide(); // 外部物件，於公用 search_bar 隱藏 返回 按鈕
					} else {
						search_bar.level_show(); // 外部物件，於公用 search_bar 顯示 返回 按鈕
					}
					stock_detail.hide(); // 外部物件，關閉 #stock_detail
					$('#stock_list').show();
					break;
				default:
			}
		};
		var _event = function() {
			$('#stock_list #stock_list_user_name').off('click').on('click', function() {
				var user_id = $(this).data('user_id');
				var user_name = $(this).text();
				if(_.level_now) {
					_.level_now = 2;
					stock_detail.load(user_name, user_id);
					search_bar.level_show(); // 外部物件，於公用 search_bar 顯示 返回 按鈕
					$('#stock_list').hide();
				} else {
					_.level_now = 1;
					_.title_text[_.level_now] = user_name;
					_load(user_id);
				}
			});
		};
		var _process = {
			get_stock_list : function() {
				$.post(document.URL, {mode:'ajax_get_stock_list'}, function(rep) {
					if(typeof rep.status != 'undefined') {
						if(!rep.status) {
							_make([]);
							console.log(rep.msg);
						} else {
							_make(rep.result);
							_.top_level_cache = rep.result;
						}
					}
				}, 'json');
			},
			get_stock_list_for_executives_id : function(user_id) {
				var post_data = {mode:'ajax_get_stock_list_for_executives_id'};
				if(user_id) post_data.user_id = user_id;
				$.post(document.URL, post_data, function(rep) {
					if(typeof rep.status != 'undefined') {
						if(!rep.status) {
							_make([]);
							console.log(rep.msg);
						} else {
							_make(rep.result);
						}
					}
				}, 'json');
			},
		};
		return {
			init : _init,
			level_back : _level_back,
		};
	})();
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
				<th>貨單類型</th>
			</tr>
		</thead>
		<tbody>
			<tr id="stock_detail_no_records">
				<td colspan="3">查無紀錄</td>
			</tr>
			<tr id="stock_detail_template">
				<td id="stock_detail_product_name"></td>
				<td id="stock_detail_quantity"></td>
				<td id="stock_detail_stock_type"></td>
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
		var _load = function(user_name, user_id) {
			search_bar.title_text(user_name + ' -- 明細'); // 外部物件，於公用 search_bar 顯示 user_name
			_process.get_stock_detail_for_user_id(user_id);
		};
		var _make = function(data) {
			$('#stock_detail_table tbody tr').remove();

			if($.isArray(data) && data.length > 0) {
				var quantity = '';
				for(var i = 0; i < data.length; i++) {
					var tr = $('<tr>' + _.stock_detail_tr + '</tr>');
					tr.find('#stock_detail_product_name').text(data[i].product_name);
					if(data[i].stock_type == 0) {
						if(data[i].quantity > 0)
							quantity = '+ ' + data[i].quantity;
						else
							quantity = '- ' + data[i].quantity;
						tr.find('#stock_detail_stock_type').text('進貨');
					} else {
						if(data[i].quantity > 0)
							quantity = '- ' + data[i].quantity;
						else
							quantity = '+ ' + data[i].quantity;
						tr.find('#stock_detail_stock_type').text('銷貨');
					}
					tr.find('#stock_detail_quantity').text(quantity);

					$('#stock_detail_table tbody').append(tr);
				}
			} else {
				$('#stock_list_table tbody').append('<tr>' + _.stock_list_no_records + '</tr>');
			}

			$('#stock_detail').show();
		};
		var _hide = function() {
			$('#stock_detail').hide();
		};
		var _process = {
			get_stock_detail_for_user_id : function(user_id) {
				var post_data = {mode:'ajax_get_stock_detail_for_user_id'};
				if(user_id) post_data.user_id = user_id;
				$.post(document.URL, post_data, function(rep) {
					if(typeof rep.status != 'undefined') {
						if(!rep.status) {
							_make([]);
							console.log(rep.msg);
						} else {
							_make(rep.result);
						}
					}
				}, 'json');
			},
		};
		return {
			init : _init,
			load : _load,
			hide : _hide,
		};
	})();
	stock_detail.init();
</script>
<!-- 進銷查詢 第三層 End -->

<!-- 登入者執行動作 -->
<script type="text/javascript">
	var stock_main = (function() {
		var _ = {
			def_position : '<?php echo $user_data['position_id']; ?>',
			def_user_name : '<?php echo $user_data['name']; ?>',
		};
		var _init = function() {
			// test 職務判斷為 hard-code 對應，待修正
			switch(_.def_position) {
				case '1': // 業務主管
					stock_list.init(1, _.def_user_name);
					break;
				case '2': // 業務
					stock_detail.load(_.def_user_name);
					break;
				case '3': // 會計
					stock_list.init(0, '公司');
					break;
				default:
			}
		};
		return {
			init : _init,
		};
	})();
	stock_main.init();
</script>
<!-- 登入者執行動作 End -->

<!-- 進銷登打 -->
<?php
	$product_result = model::query('SELECT `id`, `name`, `disable_date` FROM `product`;');
	$product = array();
	foreach($product_result as $val) {
		$product[$val['id']] = $val['name'] . (empty($val['disable_date']?'':' (停用)'));
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
				<label>貨單類型</label>
				<select name="stock_type" id="stock_form_stock_type">
					<option>未選擇</option>
					<option value="0">進貨</option>
					<option value="1">銷貨</option>
				</select>
			</p>
			<p>
				<label>商品<span class="required_tag">*</span></label>
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
			<p><label>數量<span class="required_tag">*</span></label><input type="text" name="quantity" id="stock_form_quantity"></p>
			<p><label>狀態</label><span id="stock_form_ststus"></span></p>
			<p><input type="submit" value="送出"></p>
		</form>
	</div>
</div>

<script type="text/javascript">
	// -- 解決自己的職務沒有權限設定時，修改自己的帳號職務會遺失問題

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