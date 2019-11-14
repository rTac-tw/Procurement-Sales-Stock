<?php
	if(!empty($message_arr)) {
?>
<style type="text/css">
	.div_area {
		text-align:center; /* 致中用 */
		width: 100%;
	}
	.div_area div {
		font-weight: bold;
		min-width: 50px;
		max-width: 90%;
		padding: 10px;
		margin: auto; /* 致中用 */
		margin-bottom: 10px;
	}
	.success_msg {
		background-color: #cfc;
		color: #050;
	}
	.info_msg {
		background-color: #ccf;
		color: #005;
	}
	.error_msg {
		background-color: #fcc;
		color: #500;
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