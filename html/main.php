<p>
登入囉 ~
</p>

<a id="sign_out" href="javascript:void(0);">登出</a>
<script type="text/javascript">
	$('#sign_out').off('click').on('click', function() {
		form = $('<form id="pss_form" method="POST"><input type="hidden" name="mode" value="sign_out"></form>');
		$('body').append(form);
		$(form).submit();
	});
</script>