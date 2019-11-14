<h3>商品列表</h3>
<?php
	$product_result = model::query('SELECT `id`, `name`, `price`, `quantity`, `disable_date` FROM `product`;');
?>
<a href="javascript:void(0);" id="product_form_add">新增商品</a>

<table width="100%" id="product_list_table">
	<thead>
		<tr>
			<th>動作</th>
			<th>商品名稱</th>
			<th>價格</th>
			<th>目前庫存</th>
		</tr>
	</thead>
	<tbody>
<?php
	if(empty($product_result)) {
?>
		<tr id="product_list_no_records">
			<td colspan="4">查無紀錄</td>
		</tr>
<?php
	} else {
		foreach($product_result as $val) {
?>
		<tr id="product_list_template">
			<td><a href="javascript:void(0);" id="product_edit" data-product_id="<?php echo $val['id']; ?>">修改</a></td>
			<td>[ <sapn id="product_list_name"><?php echo $val['name']; ?></sapn> ]</td>
			<td>[ <sapn id="product_list_price"><?php echo $val['price']; ?></sapn> ]</td>
			<td>[ <sapn id="product_list_quantity"><?php echo $val['quantity']; ?></sapn> ]</td>
		</tr>
<?php
		}
	}
	unset($product_result);
?>
	</tbody>
</table>
<script type="text/javascript">
	var product_list = (function() {
		var _ = {
			product_list_no_records : '',
			product_list_tr : '',
		};
		var _init = function() {
			_.product_list_no_records = $('#product_list_no_records').html();
			_.product_list_tr = $('#product_list_template').html();
		};
		var _make = function(data) {
		};
		return {
			init : _init,
		};
	})();
	product_list.init();
</script>

<style type="text/css">
	.product_form {
		position: fixed;
		top: 0;
		left: 0;
		display: none;
		width: 100vw;
		height: 100vh;
		background-color: rgba(192,192,192,0.7);
	}
	.product_form_content {
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
	.product_form_content label {
		margin: 5px 10px; 
	}
	.required_tag {
		color: #f00;
	}
</style>
<div class="product_form">
	<a href="javascript:void(0);" id="product_form_close" style="float: right; padding: 10px 10px; background-color: #ddd;">關閉</a>
	<div class="product_form_content">
		<form method="POST">
			<input type="hidden" name="mode" id="product_form_mode" value="">
			<input type="hidden" name="product_id" id="product_form_product_id" value="">
			<p><label>商品名稱<span class="required_tag" title="必填">*</span></label><input type="text" name="name" id="product_form_name"></p>
			<p><label>價格<span class="required_tag" title="必填">*</span></label><input type="text" name="price" id="product_form_price"></p>
			<p><label>數量</label><span id="product_form_quantity"></span></p>
			<p><label>狀態</label><span id="product_form_ststus"></span></p>
			<p><input type="submit" value="送出"></p>
		</form>
	</div>
</div>

<script type="text/javascript">
	// 新增商品
	$('#product_form_add').off('click').on('click', function() {
		// 清空
		$('.product_form select').prop('selectedIndex', 0);
		$('.product_form input[type=text]').val('');
		$('#product_form_quantity').html('0');
		$('#product_form_ststus').html('');

		$('#product_form_mode').val('product_add');
		$('.product_form').show(); // 顯示 form
	});

	// 修改使用者
	$('#product_list_table #product_edit').off('click').on('click', function() {
		$('#product_form_mode').val('product_edit');
		product_id = $(this).data('product_id');
		$('#product_form_product_id').val(product_id);
		$.post(document.URL, {mode:'ajax_get_product_data', product_id:product_id}, function(rep) {
			if(typeof rep.status != 'undefined') {
				if(!rep.status) {
					alert(rep.msg);
				} else {
					$('#product_form_name').val(rep.result.name);
					$('#product_form_price').val(rep.result.price);
					$('#product_form_quantity').text(rep.result.quantity);
					$('.product_form').show(); // 顯示 form
				}
			}
		}, 'json');
	});

	// 關閉 form
	$('#product_form_close').off('click').on('click', function() {
		$('.product_form').hide();
	});
</script>