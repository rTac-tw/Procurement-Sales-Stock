<style type="text/css">
	.body {
		text-align: center;
		width: 100vw;
		height: 100vh;
	}
	.content {
		width: 300px;
		height: 100px;
		background-color: rgba(172,172,172,0.3);

		position: absolute;
		left: 50%;
		top: 50%;
		margin-left: -150px;
		margin-top: -50px;
	}
</style>
<div class="body">
	<div class="content"><?php print_view('login.php', array( 'account' => @$account )); ?></div>
</div>