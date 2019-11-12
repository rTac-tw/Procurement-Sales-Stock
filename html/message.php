<?php
	if(!empty($message_arr)) {
?>
<style type="text/css">
	.div_area {
		text-align:center;
		width: 100vw;
	}
	.success_msg {
		background-color: #cfc;
		color: #050;
		font-weight: bold;
		min-width: 50px;
		max-width: 90%;
		padding: 10px;
		margin: 1px auto;
	}
	.info_msg {
		background-color: #ccf;
		color: #500;
		font-weight: bold;
		min-width: 50px;
		max-width: 90%;
		padding: 10px;
		margin: 1px auto;
	}
	.error_msg {
		background-color: #fcc;
		color: #500;
		font-weight: bold;
		min-width: 50px;
		max-width: 90%;
		padding: 10px;
		margin: 1px auto;
	}
</style>
<div class="div_area">
<?php
		foreach($message_arr as $msg) {
?>
<div class="<?php echo $msg[0]; ?>_msg"><?php echo $msg[1]; ?></div>
<?php
		}
?>
</div>
<script type="text/javascript">
	// 預計加入淡出特效
</script>
<?php
	}
?>