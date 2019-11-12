<?php 
	$user_data = check_login();
?>
<style type="text/css">
	.user_left {
		float: left;
	}
	.user_left:not(:first-child) {
		padding-left: 15px;
	}
	.user_left span {
		color: #063;
	}
	.user_right {
		margin-right: 5px;
		float: right;
	}
</style>
<div class="user_left">
	<div class="user_left">
		<label>使用者名稱：</label>
		<span><?php echo $user_data['name']; ?></span>
	</div>
	<div class="user_left">
		<label>所屬部門：</label>
		<span><?php echo $user_data['department']['name']; ?></span>
	</div>
	<div class="user_left">
		<label>職務：</label>
		<span><?php echo $user_data['position']['name']; ?></span>
	</div>
</div>
<div class="user_right">
	<?php print_view('sign_out.php'); ?>
</div>