<style type="text/css">
	.body {
		text-align:center;
		width:100vw;
		height:100vh;
	}
	.content {
		width:300px;
		height:100px;
		background-color:rgba(172,172,172,0.3);

		position:absolute;
		left:50%;
		top:50%;
		margin-left:-150px;
		margin-top:-50px;
	}
	ul {
		padding: 0px;
	}
	li {
		margin-bottom: 5px;
		list-style-type:none;
	}
</style>
<div class="body">
	<div class="content">
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
	</div>
</div>