<div class="backdrop_box large" id="login_popup">
	<div class="backdrop_detail_header"><?php echo __('Add external login'); ?></div>
	<div id="backdrop_detail_content" class="rounded_top login_content">
		<div class="logindiv openid_container" style="width: 800px;">
			<form action="<?php echo make_url('login'); ?>" method="post" id="openid_form" onclick="return openid.submit();">
				<input type="hidden" name="action" value="verify" />
					<div id="openid_choice">
						<div class="login_boxheader"><?php echo __('Log in with your OpenID'); ?></div>
						<div style="text-align: center; width: 480px; margin: 0 auto;">
							<div id="openid_btns"></div>
						</div>
					</div>
					<div id="openid_input_area">
						<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
					</div>
				<input type="submit" value="<?php echo __('Sign in'); ?>" class="button button-silver">
			</form>
		</div>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
	</div>
</div>
<script type="text/javascript">
	openid.no_sprite = true;
	openid.init('openid_identifier');
</script>