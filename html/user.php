<h3>使用者</h3>
<?php
	$user_data = check_login();

	// 確認權限
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

	if(!empty($position_permission)) {
?>
<a href="javascript:void(0);" id="user_form_add">新增使用者</a>
<?php
	}
?>
<!-- 使用者列表 -->
<?php
	$where = '';
	if(!empty($department_permission) && !empty($position_permission)) {
		// 有部門權限 + 有職務權限 = 有管理其他使用者的權限
		$where = ' OR (`department_id` IN (\'' . join('\', \'', $department_permission) . '\') AND `position_id` IN (\'' . join('\', \'', $position_permission) . '\'))';
	}
	$user_result = model::query('SELECT `id`, `name`, `department_id`, `position_id` FROM `user` WHERE `id` = \'' . $user_data['id'] . '\'' . $where . ';');

	$department_result = model::query('SELECT `id`, `name`, `disable_date` FROM `department`;');
	$department = array();
	foreach($department_result as $val) {
		$department[$val['id']] = $val['name'] . (empty($val['disable_date']?'':' (停用)'));
	}
	$position_result = model::query('SELECT `id`, `name`, `disable_date` FROM `position`;');
	$position = array();
	foreach($position_result as $val) {
		$position[$val['id']] = $val['name'] . (empty($val['disable_date']?'':' (停用)'));
	}
?>
<style type="text/css">
	#user_list td {
		text-align: center;
	}
	.self_tag {
		color: #800;
		font-weight:bold;
	}
</style>
<table width="100%" id="user_list">
	<thead>
		<tr>
			<th>動作</th>
			<th>姓名</th>
			<th>部門名稱</th>
			<th>職務</th>
		</tr>
	</thead>
	<tbody>
<?php
	if(empty($user_result)) {
?>
		<tr>
			<td colspan="4">查無紀錄</td>
		</tr>
<?php
	} else {
		foreach($user_result as $val) {
?>
		<tr>
			<td><a href="javascript:void(0);" id="user_edit" data-user_id="<?php echo $val['id']; ?>">修改</a></td>
			<td><?php echo $val['name']; ?><?php echo ($val['id'] == $user_data['id'])?' (<span class="self_tag">我</span>)':''; ?></td>
			<td><?php echo $department[$val['department_id']]; ?></td>
			<td><?php echo $position[$val['position_id']]; ?></td>
		</tr>
<?php
		}
	}
?>
	</tbody>
</table>
<!-- 使用者列表 End -->
<!-- 新增使用者 -->
<style type="text/css">
	.user_form {
		position: fixed;
		top: 0;
		left: 0;
		display: none;
		width: 100vw;
		height: 100vh;
		background-color: rgba(192,192,192,0.7);
	}
	.user_form_content {
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
	.user_form_content label {
		margin: 5px 10px; 
	}
	.required_tag {
		color: #f00;
	}
</style>
<div class="user_form">
	<a href="javascript:void(0);" id="user_form_close" style="float: right; padding: 10px 10px; background-color: #ddd;">關閉</a>
	<div class="user_form_content">
		<form method="POST">
			<input type="hidden" name="mode" id="user_form_mode" value="">
			<input type="hidden" name="user_id" id="user_form_user_id" value="">
			<p><label>帳號<span class="required_tag">*</span></label><input type="text" name="account" id="user_form_account"></p>
			<p><label>設定新密碼</label><input type="text" name="new_pwd" id="user_form_new_pwd" style="background-color: #eee;"></p>
			<p><label>新密碼確認</label><input type="text" name="pwd_check" id="user_form_pwd_check" style="background-color: #eee;"></p>
			<p><label>姓名<span class="required_tag">*</span></label><input type="text" name="name" id="user_form_name"></p>
			<p>
				<label>性別</label>
				<select name="gender" id="user_form_gender">
					<option value="0">未選擇</option>
					<option value="1">男</option>
					<option value="2">女</option>
				</select>
			</p>
			<p>
				<label>部門<span class="required_tag">*</span></label>
				<select name="department_id" id="user_form_department_id">
					<option>未選擇</option>
<?php
	foreach($department as $key=>$val) {
		if($key != $user_data['department_id'] && !in_array($key, $department_permission)) continue; // 只能設定自己同部門和有權限的部門
?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php
	}
?>
				</select>
			</p>
			<p>
				<label>職務<span class="required_tag">*</span></label>
<?php
	if(empty($position_permission)) {
		// 沒有調整職務權限
?>
				<input type="hidden" name="position_id" id="user_form_position_id" value="">
				<span id="user_form_position"></span>
<?php
	} else {
		// 有調整職務權限
?>
				<select name="position_id" id="user_form_position_id">
					<option>未選擇</option>
<?php
		foreach($position as $key=>$val) {
			if(!in_array($key, $position_permission)) continue; // 只能設定有權限的職務
?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php
		}
?>
				</select>
<?php
	}
?>
			</p>
			<p><label>狀態</label><span id="user_form_ststus"></span></p>
			<p><input type="submit" value="送出"></p>
		</form>
	</div>
</div>

<script type="text/javascript">
	// -- 解決自己的職務沒有權限設定時，修改自己的帳號職務會遺失問題
	position_option = $('select#user_form_position_id').html();

	// 新增使用者
	$('#user_form_add').off('click').on('click', function() {
		// -- 解決自己的職務沒有權限設定時，修改自己的帳號職務會遺失問題
		$('select#user_form_position_id').html(position_option);

		// 清空
		$('.user_form select').prop('selectedIndex', 0);
		$('.user_form input[type=text]').val('');
		$('#user_form_position').text('');
		$('#user_form_ststus').html('');

		$('#user_form_mode').val('user_add');
		$('.user_form').show(); // 顯示 form
	});

	// 修改使用者
	$('#user_list #user_edit').off('click').on('click', function() {
		$('#user_form_mode').val('user_edit');
		user_id = $(this).data('user_id');
		$('#user_form_user_id').val(user_id);
		$.post(document.URL, {mode:'ajax_get_user_data', user_id:user_id}, function(rep) {
			if(typeof rep.status != 'undefined') {
				if(!rep.status) {
					alert(rep.msg);
				} else {
					$('#user_form_account').val(rep.result.account);
					$('#user_form_new_pwd').val('');
					$('#user_form_pwd_check').val('');
					$('#user_form_name').val(rep.result.name);
					$('#user_form_gender').val(rep.result.gender);
					$('#user_form_department_id').val(rep.result.department_id);

					// -- 解決自己的職務沒有權限設定時，修改自己的帳號職務會遺失問題
					$('select#user_form_position_id').html(position_option);
					if(!$('select#user_form_position_id option').filter(function(){return $(this).val() == rep.result.position_id}).length) {
						$('select#user_form_position_id').append('<option value="' + rep.result.position_id + '">' + rep.result.position + '</option>');
					}

					$('#user_form_position_id').val(rep.result.position_id);
					$('#user_form_position').text(rep.result.position);
					if(rep.result.disable_date) {
						$('#user_form_ststus').html('已離職: ' + rep.result.use_date.substr(0, 16));
					} else if(rep.result.use_date) {
						$('#user_form_ststus').html('就職於: ' + rep.result.use_date.substr(0, 16));
					} else {
						$('#user_form_ststus').html('未到職');
					}
					$('.user_form').show(); // 顯示 form
				}
			}
		}, 'json');
	});

	// 關閉 form
	$('#user_form_close').off('click').on('click', function() {
		$('.user_form').hide();
	});
</script>
<!-- 新增使用者 End -->