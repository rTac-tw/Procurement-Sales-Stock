<style type="text/css">
	ul {
		padding: 0px;
	}
	li {
		margin-bottom: 5px;
		list-style-type: none;
	}
</style>
<form id="pss_form" method="POST">
	<ul>
		<li><label>帳號:</label><input type="text" name="pss_a" value="<?php echo @$account; ?>"></li>
		<li><label>帳號:</label><input type="password" name="pss_p"></li>
		<input type="hidden" name="verify_code" value="<?php echo get_verify_code(); ?>">
		<input type="hidden" name="mode" value="login">
		<li>
			<input type="submit" value="登入">
			<!-- 忘記密碼 -->
		</li>
	</ul>
</form>