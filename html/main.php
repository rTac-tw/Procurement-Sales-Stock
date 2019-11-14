<style type="text/css">
	.main_head {
		float: left;
		width: calc(100vw - 16px);
		min-height: 20px;
	}
	.main_body {
		height: calc(100vh - 16px);
	}
	.main_menu {
		float: left;
		width: 100px;
		padding: 0px 5px;
		background-color: rgba(172,172,255,0.3);
	}
	.main_menu ul {
		padding: 0px;
	}
	.main_menu li {
		margin-bottom: 10px;
		padding: 10px;
		list-style-type: none;
		font-weight: bold;
		font-size: 12px;
		border-style: outset;
		background-color: rgba(255,192,172,0.5);
	}
	.main_menu li.active {
		background-color: #fc0;
	}
	.main_content {
		float: left;
		min-width: 600px;
		margin-left: 20px;
	}
</style>
<div class="main_head">
	<?php print_view('user_bar.php'); ?>
</div>
<?php
	$mode = get_array($_POST, 'mode');
?>
<div class="main_body">
	<div class="main_menu">
		<ul id="menu_btn">
			<li data-mode="product_list"<?php echo ($mode=='product_list')?' class="active"':''; ?>>商品列表</li>
			<li data-mode="stock_list"<?php echo ($mode=='stock_list')?' class="active"':''; ?>>進銷查詢</li>
			<li data-mode="user"<?php echo ($mode=='user')?' class="active"':''; ?>>使用者</li>
		</ul>
	</div>
	<div class="main_content">
<?php
	switch ($mode) {
		case 'product_add':
		case 'product_edit':
			include_once(WEB_PATH . '/controller/product.php'); // 處理 新增修改
		case 'product_list':
			include_once(WEB_PATH . '/html/message.php');
			include_once(WEB_PATH . '/html/product_list.php');
			break;

		case 'stock_add':
			include_once(WEB_PATH . '/controller/stock.php'); // 處理 新增修改
		case 'stock_list':
			include_once(WEB_PATH . '/html/message.php');
			include_once(WEB_PATH . '/html/stock_list.php');
			break;

		case 'user_add':
		case 'user_edit':
			include_once(WEB_PATH . '/controller/user.php'); // 處理 新增修改
		case 'user':
			include_once(WEB_PATH . '/html/message.php');
			include_once(WEB_PATH . '/html/user.php');
			break;

		default:
?>
		<p>登入囉 ~</p>
<?php
			break;
	}

	unset($mode);
?>
	</div>
</div>

<script type="text/javascript">
	$('#menu_btn li').off('click').on('click', function() {
		form = $('<form id="pss_form" method="POST"><input type="hidden" name="mode" value="' + $(this).data('mode') + '"></form>');
		$('body').append(form);
		$(form).submit();
	});
</script>